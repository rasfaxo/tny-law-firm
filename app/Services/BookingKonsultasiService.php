<?php

namespace App\Services;

use App\Models\BookingKonsultasi;
use App\Models\JadwalKonsultasi;
use App\Models\PraPendaftaranPerkara;
use App\Models\RiwayatStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingKonsultasiService
{
    /**
     * @param array{id_jadwal: int|string, metode_konsultasi: string, catatan_preferensi_klien?: string|null} $data
     */
    public function book(
        PraPendaftaranPerkara $praPendaftaranPerkara,
        array $data,
        int $klienId,
    ): BookingKonsultasi {
        return DB::transaction(function () use (
            $praPendaftaranPerkara,
            $data,
            $klienId,
        ): BookingKonsultasi {
            $pengajuan = PraPendaftaranPerkara::query()
                ->whereKey($praPendaftaranPerkara->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureCanBook($pengajuan, $klienId);

            $jadwal = JadwalKonsultasi::query()
                ->whereKey((int) $data["id_jadwal"])
                ->lockForUpdate()
                ->firstOrFail();

            if ($jadwal->status_slot !== "tersedia") {
                throw ValidationException::withMessages([
                    "id_jadwal" => "Slot jadwal konsultasi tidak tersedia.",
                ]);
            }

            $booking = BookingKonsultasi::create([
                "id_pendaftaran" => $pengajuan->id_pendaftaran,
                "id_jadwal" => $jadwal->id_jadwal,
                "id_user" => $klienId,
                "status_booking" => "aktif",
                "metode_konsultasi" => $data["metode_konsultasi"],
                "status_konfirmasi_konsultasi" => "menunggu_konfirmasi",
                "link_konsultasi" => null,
                "lokasi_konsultasi" => null,
                "catatan_konsultasi" => null,
                "catatan_preferensi_klien" =>
                    $data["catatan_preferensi_klien"] ?? null,
                "dikonfirmasi_pada" => null,
                "id_admin_konfirmasi" => null,
                "tanggal_booking" => now(),
            ]);

            $jadwal->update([
                "status_slot" => "terisi",
            ]);

            $pengajuan->update([
                "status_pengajuan" => "jadwal_dipilih",
            ]);

            RiwayatStatus::create([
                "id_pendaftaran" => $pengajuan->id_pendaftaran,
                "id_user" => $klienId,
                "status" => "jadwal_dipilih",
                "keterangan" => "Klien telah memilih jadwal konsultasi",
            ]);

            return $booking;
        });
    }

    private function ensureCanBook(
        PraPendaftaranPerkara $pengajuan,
        int $klienId,
    ): void {
        if ($pengajuan->id_user !== $klienId) {
            throw ValidationException::withMessages([
                "id_jadwal" =>
                    "Pengajuan ini tidak dapat dibooking oleh akun ini.",
            ]);
        }

        if ($pengajuan->status_pengajuan !== "berkas_lengkap") {
            throw ValidationException::withMessages([
                "id_jadwal" =>
                    "Jadwal konsultasi hanya dapat dipilih saat status pengajuan berkas lengkap.",
            ]);
        }

        $hasActiveBooking = BookingKonsultasi::query()
            ->where("id_pendaftaran", $pengajuan->id_pendaftaran)
            ->where("status_booking", "aktif")
            ->exists();

        if ($hasActiveBooking) {
            throw ValidationException::withMessages([
                "id_jadwal" =>
                    "Pengajuan ini sudah memiliki booking konsultasi aktif.",
            ]);
        }
    }
}
