<?php

namespace App\Services;

use App\Models\BookingKonsultasi;
use App\Models\JadwalKonsultasi;
use App\Models\PermintaanReschedule;
use App\Models\PraPendaftaranPerkara;
use App\Models\RiwayatStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PermintaanRescheduleService
{
    public function createForKlien(
        BookingKonsultasi $bookingKonsultasi,
        array $data,
        int $klienId,
    ): PermintaanReschedule {
        return DB::transaction(function () use (
            $bookingKonsultasi,
            $data,
            $klienId,
        ): PermintaanReschedule {
            $booking = BookingKonsultasi::query()
                ->whereKey($bookingKonsultasi->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $pengajuan = PraPendaftaranPerkara::query()
                ->whereKey($booking->id_pendaftaran)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureKlienCanRequest($booking, $pengajuan, $klienId);

            return PermintaanReschedule::create([
                "id_booking" => $booking->id_booking,
                "id_user" => $klienId,
                "alasan_reschedule" => $data["alasan_reschedule"],
                "preferensi_jadwal" => $data["preferensi_jadwal"] ?? null,
                "preferensi_metode" => $data["preferensi_metode"] ?? null,
                "status_reschedule" => "menunggu_persetujuan",
                "id_jadwal_baru" => null,
                "id_booking_baru" => null,
                "catatan_admin" => null,
                "tanggal_pengajuan" => now(),
                "tanggal_keputusan" => null,
            ]);
        });
    }

    public function approve(
        PermintaanReschedule $permintaanReschedule,
        array $data,
        int $adminId,
    ): PermintaanReschedule {
        return DB::transaction(function () use (
            $permintaanReschedule,
            $data,
            $adminId,
        ): PermintaanReschedule {
            $permintaan = PermintaanReschedule::query()
                ->whereKey($permintaanReschedule->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $bookingLama = BookingKonsultasi::query()
                ->whereKey($permintaan->id_booking)
                ->lockForUpdate()
                ->firstOrFail();

            $pengajuan = PraPendaftaranPerkara::query()
                ->whereKey($bookingLama->id_pendaftaran)
                ->lockForUpdate()
                ->firstOrFail();

            $jadwalLama = JadwalKonsultasi::query()
                ->whereKey($bookingLama->id_jadwal)
                ->lockForUpdate()
                ->firstOrFail();

            $jadwalBaru = JadwalKonsultasi::query()
                ->whereKey((int) $data["id_jadwal_baru"])
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureCanApprove(
                $permintaan,
                $bookingLama,
                $pengajuan,
                $jadwalLama,
                $jadwalBaru,
            );

            $bookingLama->update([
                "status_booking" => "dibatalkan",
            ]);

            $jadwalLama->update([
                "status_slot" => "tersedia",
            ]);

            $bookingBaru = BookingKonsultasi::create([
                "id_pendaftaran" => $bookingLama->id_pendaftaran,
                "id_jadwal" => $jadwalBaru->id_jadwal,
                "id_user" => $bookingLama->id_user,
                "status_booking" => "aktif",
                "metode_konsultasi" =>
                    $permintaan->preferensi_metode ?:
                    ($bookingLama->metode_konsultasi ?:
                    "offline"),
                "status_konfirmasi_konsultasi" => "menunggu_konfirmasi",
                "link_konsultasi" => null,
                "lokasi_konsultasi" => null,
                "catatan_konsultasi" => null,
                "catatan_preferensi_klien" => $this->buildCatatanPreferensi(
                    $permintaan,
                ),
                "dikonfirmasi_pada" => null,
                "id_admin_konfirmasi" => null,
                "tanggal_booking" => now(),
            ]);

            $jadwalBaru->update([
                "status_slot" => "terisi",
            ]);

            $permintaan->update([
                "status_reschedule" => "disetujui",
                "id_jadwal_baru" => $jadwalBaru->id_jadwal,
                "id_booking_baru" => $bookingBaru->id_booking,
                "catatan_admin" => $data["catatan_admin"] ?? null,
                "tanggal_keputusan" => now(),
            ]);

            RiwayatStatus::create([
                "id_pendaftaran" => $pengajuan->id_pendaftaran,
                "id_user" => $adminId,
                "status" => "jadwal_dipilih",
                "keterangan" =>
                    "Reschedule konsultasi disetujui oleh admin. Booking lama dibatalkan dan jadwal baru dipilih.",
            ]);

            return $permintaan->refresh();
        });
    }

    public function reject(
        PermintaanReschedule $permintaanReschedule,
        array $data,
    ): PermintaanReschedule {
        return DB::transaction(function () use (
            $permintaanReschedule,
            $data,
        ): PermintaanReschedule {
            $permintaan = PermintaanReschedule::query()
                ->whereKey($permintaanReschedule->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($permintaan->status_reschedule !== "menunggu_persetujuan") {
                throw ValidationException::withMessages([
                    "catatan_admin" =>
                        "Permintaan reschedule ini sudah diproses.",
                ]);
            }

            $permintaan->update([
                "status_reschedule" => "ditolak",
                "catatan_admin" => $data["catatan_admin"],
                "tanggal_keputusan" => now(),
            ]);

            return $permintaan->refresh();
        });
    }

    private function ensureKlienCanRequest(
        BookingKonsultasi $booking,
        PraPendaftaranPerkara $pengajuan,
        int $klienId,
    ): void {
        if (
            $booking->id_user !== $klienId ||
            $pengajuan->id_user !== $klienId
        ) {
            throw ValidationException::withMessages([
                "alasan_reschedule" =>
                    "Booking ini tidak dapat diajukan reschedule oleh akun ini.",
            ]);
        }

        if ($booking->status_booking !== "aktif") {
            throw ValidationException::withMessages([
                "alasan_reschedule" =>
                    "Reschedule hanya dapat diajukan untuk booking aktif.",
            ]);
        }

        if ($pengajuan->status_pengajuan !== "jadwal_dipilih") {
            throw ValidationException::withMessages([
                "alasan_reschedule" =>
                    "Reschedule hanya dapat diajukan setelah jadwal konsultasi dipilih.",
            ]);
        }

        $hasPendingRequest = PermintaanReschedule::query()
            ->where("id_booking", $booking->id_booking)
            ->where("status_reschedule", "menunggu_persetujuan")
            ->exists();

        if ($hasPendingRequest) {
            throw ValidationException::withMessages([
                "alasan_reschedule" =>
                    "Booking ini masih memiliki permintaan reschedule yang menunggu persetujuan.",
            ]);
        }
    }

    private function ensureCanApprove(
        PermintaanReschedule $permintaan,
        BookingKonsultasi $bookingLama,
        PraPendaftaranPerkara $pengajuan,
        JadwalKonsultasi $jadwalLama,
        JadwalKonsultasi $jadwalBaru,
    ): void {
        if ($permintaan->status_reschedule !== "menunggu_persetujuan") {
            throw ValidationException::withMessages([
                "id_jadwal_baru" => "Permintaan reschedule ini sudah diproses.",
            ]);
        }

        if ($bookingLama->status_booking !== "aktif") {
            throw ValidationException::withMessages([
                "id_jadwal_baru" => "Booking lama sudah tidak aktif.",
            ]);
        }

        if ($pengajuan->status_pengajuan !== "jadwal_dipilih") {
            throw ValidationException::withMessages([
                "id_jadwal_baru" =>
                    "Pengajuan tidak berada pada status jadwal dipilih.",
            ]);
        }

        if ($jadwalBaru->id_jadwal === $jadwalLama->id_jadwal) {
            throw ValidationException::withMessages([
                "id_jadwal_baru" =>
                    "Jadwal baru tidak boleh sama dengan jadwal lama.",
            ]);
        }

        if ($jadwalBaru->status_slot !== "tersedia") {
            throw ValidationException::withMessages([
                "id_jadwal_baru" => "Jadwal baru tidak tersedia.",
            ]);
        }
    }

    private function buildCatatanPreferensi(
        PermintaanReschedule $permintaan,
    ): string {
        $parts = [
            "Reschedule konsultasi diajukan oleh klien.",
            "Alasan: " . $permintaan->alasan_reschedule,
        ];

        if ($permintaan->preferensi_jadwal) {
            $parts[] = "Preferensi jadwal: " . $permintaan->preferensi_jadwal;
        }

        if ($permintaan->preferensi_metode) {
            $parts[] = "Preferensi metode: " . $permintaan->preferensi_metode;
        }

        return implode("\n", $parts);
    }
}
