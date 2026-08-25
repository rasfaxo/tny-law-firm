<?php

namespace Tests\Concerns;

use App\Models\BookingKonsultasi;
use App\Models\CatatanVerifikasi;
use App\Models\DokumenPerkara;
use App\Models\JadwalKonsultasi;
use App\Models\KategoriPerkara;
use App\Models\PermintaanReschedule;
use App\Models\PraPendaftaranPerkara;
use App\Models\RiwayatStatus;
use App\Models\User;
use App\Models\VerifikasiBerkas;

trait CreatesTestingData
{
    protected function createAdmin(array $overrides = []): User
    {
        return User::factory()->admin()->create($overrides);
    }

    protected function createStafLegal(array $overrides = []): User
    {
        return User::factory()->stafLegal()->create($overrides);
    }

    protected function createKlien(array $overrides = []): User
    {
        return User::factory()->klien()->create($overrides);
    }

    protected function createKategori(array $overrides = []): KategoriPerkara
    {
        return KategoriPerkara::factory()->create($overrides);
    }

    protected function createPengajuan(
        ?User $klien = null,
        array $overrides = [],
    ): PraPendaftaranPerkara {
        $klien ??= $this->createKlien();
        $kategoriId = $overrides["id_kategori"] ?? $this->createKategori()->id_kategori;

        return PraPendaftaranPerkara::factory()->create(array_merge([
            "id_user" => $klien->id_user,
            "id_kategori" => $kategoriId,
        ], $overrides));
    }

    protected function createDokumen(
        PraPendaftaranPerkara $pengajuan,
        array $overrides = [],
    ): DokumenPerkara {
        return DokumenPerkara::factory()->create(array_merge([
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
        ], $overrides));
    }

    protected function createRiwayatStatus(
        PraPendaftaranPerkara $pengajuan,
        User $user,
        array $overrides = [],
    ): RiwayatStatus {
        return RiwayatStatus::factory()->create(array_merge([
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "id_user" => $user->id_user,
            "status" => $pengajuan->status_pengajuan,
        ], $overrides));
    }

    protected function createVerifikasi(
        PraPendaftaranPerkara $pengajuan,
        ?User $stafLegal = null,
        array $overrides = [],
    ): VerifikasiBerkas {
        $stafLegal ??= $this->createStafLegal();

        return VerifikasiBerkas::factory()->create(array_merge([
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "id_user" => $stafLegal->id_user,
        ], $overrides));
    }

    protected function createCatatan(
        VerifikasiBerkas $verifikasi,
        ?DokumenPerkara $dokumen = null,
        array $overrides = [],
    ): CatatanVerifikasi {
        return CatatanVerifikasi::factory()->create(array_merge([
            "id_verifikasi" => $verifikasi->id_verifikasi,
            "id_dokumen" => $dokumen?->id_dokumen,
        ], $overrides));
    }

    protected function createJadwalTersedia(
        ?User $admin = null,
        array $overrides = [],
    ): JadwalKonsultasi {
        $admin ??= $this->createAdmin();

        return JadwalKonsultasi::factory()->create(array_merge([
            "id_user" => $admin->id_user,
            "status_slot" => "tersedia",
        ], $overrides));
    }

    protected function createBookingAktif(
        ?User $klien = null,
        ?PraPendaftaranPerkara $pengajuan = null,
        ?JadwalKonsultasi $jadwal = null,
        array $overrides = [],
    ): BookingKonsultasi {
        $klien ??= $this->createKlien();
        $pengajuan ??= $this->createPengajuan($klien, [
            "status_pengajuan" => "jadwal_dipilih",
        ]);
        $jadwal ??= $this->createJadwalTersedia(null, ["status_slot" => "terisi"]);

        return BookingKonsultasi::factory()->create(array_merge([
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "id_jadwal" => $jadwal->id_jadwal,
            "id_user" => $klien->id_user,
            "status_booking" => "aktif",
            "status_konfirmasi_konsultasi" => "menunggu_konfirmasi",
        ], $overrides));
    }

    protected function createReschedulePending(
        ?BookingKonsultasi $booking = null,
        array $overrides = [],
    ): PermintaanReschedule {
        $booking ??= $this->createBookingAktif();

        return PermintaanReschedule::factory()->create(array_merge([
            "id_booking" => $booking->id_booking,
            "id_user" => $booking->id_user,
            "status_reschedule" => "menunggu_persetujuan",
            "id_jadwal_baru" => null,
            "id_booking_baru" => null,
            "tanggal_keputusan" => null,
        ], $overrides));
    }
}
