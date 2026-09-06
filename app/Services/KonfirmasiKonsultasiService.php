<?php

namespace App\Services;

use App\Models\BookingKonsultasi;
use App\Models\PraPendaftaranPerkara;
use App\Models\User;
use App\Notifications\ConsultationStatusNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KonfirmasiKonsultasiService
{
    public function __construct(private AuditLogService $auditLog) {}

    /**
     * @param  array{link_konsultasi?: string|null, lokasi_konsultasi?: string|null, catatan_konsultasi?: string|null}  $data
     */
    public function confirm(
        BookingKonsultasi $bookingKonsultasi,
        array $data,
        int $adminId,
    ): BookingKonsultasi {
        [$booking, $event] = DB::transaction(function () use (
            $bookingKonsultasi,
            $data,
            $adminId,
        ): array {
            $booking = BookingKonsultasi::query()
                ->whereKey($bookingKonsultasi->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $pengajuan = PraPendaftaranPerkara::query()
                ->whereKey($booking->id_pendaftaran)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureCanConfirm($booking, $pengajuan);

            $technicalDetails = [
                'catatan_konsultasi' => $data['catatan_konsultasi'] ?? null,
            ];

            if ($booking->metode_konsultasi === 'online') {
                $technicalDetails['link_konsultasi'] = $data['link_konsultasi'] ?? null;
                $technicalDetails['lokasi_konsultasi'] = null;
            }

            if ($booking->metode_konsultasi === 'offline') {
                $technicalDetails['link_konsultasi'] = null;
                $technicalDetails['lokasi_konsultasi'] = $data['lokasi_konsultasi'] ?? null;
            }

            $event = $booking->status_konfirmasi_konsultasi === 'terkonfirmasi'
                ? 'updated'
                : 'confirmed';

            $detailsUnchanged = $event === 'updated'
                && collect($technicalDetails)->every(
                    fn (mixed $value, string $key): bool => $booking->getAttribute($key) === $value,
                );

            if (! $detailsUnchanged) {
                $booking->update($technicalDetails + [
                    'status_konfirmasi_konsultasi' => 'terkonfirmasi',
                    'dikonfirmasi_pada' => now(),
                    'id_admin_konfirmasi' => $adminId,
                ]);

                $this->auditLog->record(
                    'consultation.'.$event,
                    $booking,
                    User::query()->findOrFail($adminId),
                    ['method' => $booking->metode_konsultasi],
                );
            }

            return [$booking->fresh([
                'adminKonfirmasi',
                'jadwalKonsultasi',
                'klien',
                'praPendaftaranPerkara.kategori',
            ]), $detailsUnchanged ? null : $event];
        });

        if ($event !== null) {
            $booking->klien?->notify(new ConsultationStatusNotification($booking, $event));
        }

        return $booking;
    }

    private function ensureCanConfirm(
        BookingKonsultasi $booking,
        PraPendaftaranPerkara $pengajuan,
    ): void {
        if ($booking->status_booking !== 'aktif') {
            throw ValidationException::withMessages([
                'catatan_konsultasi' => 'Booking konsultasi yang tidak aktif tidak dapat dikonfirmasi.',
            ]);
        }

        if ($pengajuan->status_pengajuan !== 'jadwal_dipilih') {
            throw ValidationException::withMessages([
                'catatan_konsultasi' => 'Pengajuan harus berstatus jadwal dipilih untuk dikonfirmasi.',
            ]);
        }
    }
}
