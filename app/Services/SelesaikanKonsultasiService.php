<?php

namespace App\Services;

use App\Models\BookingKonsultasi;
use App\Models\PermintaanReschedule;
use App\Models\PraPendaftaranPerkara;
use App\Models\RiwayatStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SelesaikanKonsultasiService
{
    public function selesaikan(
        BookingKonsultasi $bookingKonsultasi,
        int $adminId,
    ): BookingKonsultasi {
        return DB::transaction(function () use (
            $bookingKonsultasi,
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

            $permintaanRescheduleMenunggu = PermintaanReschedule::query()
                ->where("id_booking", $booking->id_booking)
                ->where("status_reschedule", "menunggu_persetujuan")
                ->lockForUpdate()
                ->exists();

            $this->ensureCanComplete(
                $booking,
                $pengajuan,
                $permintaanRescheduleMenunggu,
            );

            $booking->update([
                "status_booking" => "selesai",
            ]);

            $pengajuan->update([
                "status_pengajuan" => "selesai",
            ]);

            RiwayatStatus::create([
                "id_pendaftaran" => $pengajuan->id_pendaftaran,
                "id_user" => $adminId,
                "status" => "selesai",
                "keterangan" => "Konsultasi telah diselesaikan oleh admin",
            ]);

            return $booking->fresh([
                "adminKonfirmasi",
                "jadwalKonsultasi",
                "klien",
                "permintaanReschedule",
                "praPendaftaranPerkara.kategori",
            ]);
        });
    }

    private function ensureCanComplete(
        BookingKonsultasi $booking,
        PraPendaftaranPerkara $pengajuan,
        bool $hasPendingReschedule,
    ): void {
        if ($booking->status_booking === "selesai") {
            throw ValidationException::withMessages([
                "booking" => "Booking konsultasi ini sudah selesai.",
            ]);
        }

        if ($booking->status_booking === "dibatalkan") {
            throw ValidationException::withMessages([
                "booking" => "Booking konsultasi yang dibatalkan tidak dapat diselesaikan.",
            ]);
        }

        if ($booking->status_booking !== "aktif") {
            throw ValidationException::withMessages([
                "booking" => "Hanya booking aktif yang dapat diselesaikan.",
            ]);
        }

        if ($pengajuan->status_pengajuan === "selesai") {
            throw ValidationException::withMessages([
                "booking" => "Pengajuan ini sudah selesai.",
            ]);
        }

        if ($pengajuan->status_pengajuan !== "jadwal_dipilih") {
            throw ValidationException::withMessages([
                "booking" => "Pengajuan harus berstatus jadwal dipilih sebelum konsultasi diselesaikan.",
            ]);
        }

        if ($booking->status_konfirmasi_konsultasi !== "terkonfirmasi") {
            throw ValidationException::withMessages([
                "booking" => "Detail konsultasi belum dikonfirmasi Admin.",
            ]);
        }

        if ($hasPendingReschedule) {
            throw ValidationException::withMessages([
                "booking" => "Masih ada permintaan reschedule yang menunggu persetujuan.",
            ]);
        }
    }
}
