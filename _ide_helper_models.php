<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id_booking
 * @property int $id_pendaftaran
 * @property int $id_jadwal
 * @property int $id_user
 * @property string $status_booking
 * @property \Illuminate\Support\Carbon $tanggal_booking
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\JadwalKonsultasi $jadwalKonsultasi
 * @property-read \App\Models\User $klien
 * @property-read \App\Models\PraPendaftaranPerkara $praPendaftaranPerkara
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingKonsultasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingKonsultasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingKonsultasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingKonsultasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingKonsultasi whereIdBooking($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingKonsultasi whereIdJadwal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingKonsultasi whereIdPendaftaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingKonsultasi whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingKonsultasi whereStatusBooking($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingKonsultasi whereTanggalBooking($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingKonsultasi whereUpdatedAt($value)
 */
	class BookingKonsultasi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_catatan
 * @property int $id_verifikasi
 * @property int|null $id_dokumen
 * @property string $isi_catatan
 * @property string $status_perbaikan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DokumenPerkara|null $dokumenPerkara
 * @property-read \App\Models\VerifikasiBerkas $verifikasiBerkas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatatanVerifikasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatatanVerifikasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatatanVerifikasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatatanVerifikasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatatanVerifikasi whereIdCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatatanVerifikasi whereIdDokumen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatatanVerifikasi whereIdVerifikasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatatanVerifikasi whereIsiCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatatanVerifikasi whereStatusPerbaikan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatatanVerifikasi whereUpdatedAt($value)
 */
	class CatatanVerifikasi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_dokumen
 * @property int $id_pendaftaran
 * @property string $nama_dokumen
 * @property string $jenis_dokumen
 * @property string $file_path
 * @property string $status_dokumen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CatatanVerifikasi> $catatanVerifikasi
 * @property-read int|null $catatan_verifikasi_count
 * @property-read \App\Models\PraPendaftaranPerkara $praPendaftaranPerkara
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenPerkara newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenPerkara newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenPerkara query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenPerkara whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenPerkara whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenPerkara whereIdDokumen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenPerkara whereIdPendaftaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenPerkara whereJenisDokumen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenPerkara whereNamaDokumen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenPerkara whereStatusDokumen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumenPerkara whereUpdatedAt($value)
 */
	class DokumenPerkara extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_jadwal
 * @property int $id_user
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string $waktu_mulai
 * @property string $waktu_selesai
 * @property string $status_slot
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $admin
 * @property-read \App\Models\BookingKonsultasi|null $bookingAktif
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BookingKonsultasi> $bookingKonsultasi
 * @property-read int|null $booking_konsultasi_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalKonsultasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalKonsultasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalKonsultasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalKonsultasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalKonsultasi whereIdJadwal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalKonsultasi whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalKonsultasi whereStatusSlot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalKonsultasi whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalKonsultasi whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalKonsultasi whereWaktuMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalKonsultasi whereWaktuSelesai($value)
 */
	class JadwalKonsultasi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_kategori
 * @property string $nama_kategori
 * @property string|null $deskripsi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PraPendaftaranPerkara> $praPendaftaranPerkara
 * @property-read int|null $pra_pendaftaran_perkara_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriPerkara newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriPerkara newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriPerkara query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriPerkara whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriPerkara whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriPerkara whereIdKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriPerkara whereNamaKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KategoriPerkara whereUpdatedAt($value)
 */
	class KategoriPerkara extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_pendaftaran
 * @property int $id_user
 * @property int $id_kategori
 * @property string $judul_perkara
 * @property string $kronologi
 * @property string $status_pengajuan
 * @property \Illuminate\Support\Carbon $tanggal_pengajuan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BookingKonsultasi|null $bookingAktif
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BookingKonsultasi> $bookingKonsultasi
 * @property-read int|null $booking_konsultasi_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DokumenPerkara> $dokumenPerkara
 * @property-read int|null $dokumen_perkara_count
 * @property-read \App\Models\KategoriPerkara $kategori
 * @property-read \App\Models\User $klien
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RiwayatStatus> $riwayatStatus
 * @property-read int|null $riwayat_status_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VerifikasiBerkas> $verifikasiBerkas
 * @property-read int|null $verifikasi_berkas_count
 * @property-read \App\Models\VerifikasiBerkas|null $verifikasiTerakhir
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PraPendaftaranPerkara newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PraPendaftaranPerkara newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PraPendaftaranPerkara query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PraPendaftaranPerkara whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PraPendaftaranPerkara whereIdKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PraPendaftaranPerkara whereIdPendaftaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PraPendaftaranPerkara whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PraPendaftaranPerkara whereJudulPerkara($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PraPendaftaranPerkara whereKronologi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PraPendaftaranPerkara whereStatusPengajuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PraPendaftaranPerkara whereTanggalPengajuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PraPendaftaranPerkara whereUpdatedAt($value)
 */
	class PraPendaftaranPerkara extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_profil
 * @property int $id_user
 * @property string|null $alamat
 * @property string|null $jenis_kelamin
 * @property string|null $pekerjaan
 * @property string|null $no_identitas
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilKlien newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilKlien newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilKlien query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilKlien whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilKlien whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilKlien whereIdProfil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilKlien whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilKlien whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilKlien whereNoIdentitas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilKlien wherePekerjaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilKlien whereUpdatedAt($value)
 */
	class ProfilKlien extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_riwayat
 * @property int $id_pendaftaran
 * @property int $id_user
 * @property string $status
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PraPendaftaranPerkara $praPendaftaranPerkara
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatus whereIdPendaftaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatus whereIdRiwayat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatus whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatus whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStatus whereUpdatedAt($value)
 */
	class RiwayatStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_user
 * @property string $nama
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $no_telepon
 * @property string $status_akun
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BookingKonsultasi> $bookingKonsultasi
 * @property-read int|null $booking_konsultasi_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JadwalKonsultasi> $jadwalKonsultasi
 * @property-read int|null $jadwal_konsultasi_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PraPendaftaranPerkara> $praPendaftaranPerkara
 * @property-read int|null $pra_pendaftaran_perkara_count
 * @property-read \App\Models\ProfilKlien|null $profilKlien
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RiwayatStatus> $riwayatStatus
 * @property-read int|null $riwayat_status_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VerifikasiBerkas> $verifikasiBerkas
 * @property-read int|null $verifikasi_berkas_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNoTelepon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatusAkun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_verifikasi
 * @property int $id_pendaftaran
 * @property int $id_user
 * @property string $status_verifikasi
 * @property \Illuminate\Support\Carbon $tanggal_verifikasi
 * @property string|null $catatan_umum
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CatatanVerifikasi> $catatanVerifikasi
 * @property-read int|null $catatan_verifikasi_count
 * @property-read \App\Models\PraPendaftaranPerkara $praPendaftaranPerkara
 * @property-read \App\Models\User $stafLegal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerifikasiBerkas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerifikasiBerkas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerifikasiBerkas query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerifikasiBerkas whereCatatanUmum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerifikasiBerkas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerifikasiBerkas whereIdPendaftaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerifikasiBerkas whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerifikasiBerkas whereIdVerifikasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerifikasiBerkas whereStatusVerifikasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerifikasiBerkas whereTanggalVerifikasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerifikasiBerkas whereUpdatedAt($value)
 */
	class VerifikasiBerkas extends \Eloquent {}
}

