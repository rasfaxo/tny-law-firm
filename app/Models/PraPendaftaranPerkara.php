<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PraPendaftaranPerkara extends Model
{
    protected $table = "pra_pendaftaran_perkara";

    protected $primaryKey = "id_pendaftaran";

    public $incrementing = true;

    protected $keyType = "int";

    protected $fillable = [
        "id_user",
        "id_kategori",
        "judul_perkara",
        "kronologi",
        "status_pengajuan",
        "tanggal_pengajuan",
    ];

    protected $casts = [
        "tanggal_pengajuan" => "datetime",
    ];

    public function getRouteKeyName(): string
    {
        return "id_pendaftaran";
    }

    public function klien()
    {
        return $this->belongsTo(User::class, "id_user", "id_user");
    }

    public function kategori()
    {
        return $this->belongsTo(
            KategoriPerkara::class,
            "id_kategori",
            "id_kategori",
        );
    }

    public function dokumenPerkara()
    {
        return $this->hasMany(
            DokumenPerkara::class,
            "id_pendaftaran",
            "id_pendaftaran",
        );
    }

    public function dokumenAktif()
    {
        return $this->hasMany(
            DokumenPerkara::class,
            "id_pendaftaran",
            "id_pendaftaran",
        )->aktif();
    }

    public function riwayatDokumen()
    {
        return $this->hasMany(
            DokumenPerkara::class,
            "id_pendaftaran",
            "id_pendaftaran",
        )->diganti();
    }

    public function verifikasiBerkas()
    {
        return $this->hasMany(
            VerifikasiBerkas::class,
            "id_pendaftaran",
            "id_pendaftaran",
        );
    }

    public function verifikasiTerakhir()
    {
        return $this->hasOne(
            VerifikasiBerkas::class,
            "id_pendaftaran",
            "id_pendaftaran",
        )->latestOfMany("tanggal_verifikasi");
    }

    public function riwayatStatus()
    {
        return $this->hasMany(
            RiwayatStatus::class,
            "id_pendaftaran",
            "id_pendaftaran",
        );
    }

    public function bookingKonsultasi()
    {
        return $this->hasMany(
            BookingKonsultasi::class,
            "id_pendaftaran",
            "id_pendaftaran",
        );
    }

    public function bookingAktif()
    {
        return $this->hasOne(
            BookingKonsultasi::class,
            "id_pendaftaran",
            "id_pendaftaran",
        )->where("status_booking", "aktif");
    }

    public function bookingTerakhir()
    {
        return $this->hasOne(
            BookingKonsultasi::class,
            "id_pendaftaran",
            "id_pendaftaran",
        )->latestOfMany("tanggal_booking");
    }
}
