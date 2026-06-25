<?php

namespace App\Services;

use App\Models\BookingKonsultasi;
use App\Models\PraPendaftaranPerkara;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KonfirmasiKonsultasiService
{
    /**
     * @param array{link_konsultasi?: string|null, lokasi_konsultasi?: string|null, catatan_konsultasi?: string|null} $data
     */
    public function confirm(
        BookingKonsultasi $bookingKonsultasi,
        array $data,
        int $adminId,
    ): BookingKonsultasi {
        return DB::transaction(function () use (
            $bookingKonsultasi,
            $data,
            $adminId,
        ): BookingKonsultasi {
            $booking = BookingKonsultasi::query()
                ->whereKey($bookingKonsultasi->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $pengajuan = PraPendaftaranPerkara::query()
                ->whereKey($booking->id_pendaftaran)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureCanConfirm($booking, $pengajuan);

            $updateData = [
                "catatan_konsultasi" => $data["catatan_konsultasi"] ?? null,
                "status_konfirmasi_konsultasi" => "terkonfirmasi",
                "dikonfirmasi_pada" => now(),
                "id_admin_konfirmasi" => $adminId,
            ];

            if ($booking->metode_konsultasi === "online") {
                $updateData["link_konsultasi"] = $data["link_konsultasi"] ?? null;
                $updateData["lokasi_konsultasi"] = null;
            }

            if ($booking->metode_konsultasi === "offline") {
                $updateData["link_konsultasi"] = null;
                $updateData["lokasi_konsultasi"] = $data["lokasi_konsultasi"] ?? null;
            }

            $booking->update($updateData);

            return $booking->fresh([
                "adminKonfirmasi",
                "jadwalKonsultasi",
                "klien",
                "praPendaftaranPerkara.kategori",
            ]);
        });
    }

    private function ensureCanConfirm(
        BookingKonsultasi $booking,
        PraPendaftaranPerkara $pengajuan,
    ): void {
        if ($booking->status_booking !== "aktif") {
            throw ValidationException::withMessages([
                "catatan_konsultasi" => "Booking konsultasi yang tidak aktif tidak dapat dikonfirmasi.",
            ]);
        }

        if ($pengajuan->status_pengajuan !== "jadwal_dipilih") {
            throw ValidationException::withMessages([
                "catatan_konsultasi" => "Pengajuan harus berstatus jadwal dipilih untuk dikonfirmasi.",
            ]);
        }
    }
}
