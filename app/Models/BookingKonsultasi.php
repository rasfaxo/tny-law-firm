<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingKonsultasi extends Model
{
    protected $table = "booking_konsultasi";

    protected $primaryKey = "id_booking";

    public $incrementing = true;

    protected $keyType = "int";

    protected $fillable = [
        "id_pendaftaran",
        "id_jadwal",
        "id_user",
        "status_booking",
        "metode_konsultasi",
        "status_konfirmasi_konsultasi",
        "link_konsultasi",
        "lokasi_konsultasi",
        "catatan_konsultasi",
        "catatan_preferensi_klien",
        "dikonfirmasi_pada",
        "id_admin_konfirmasi",
        "tanggal_booking",
    ];

    protected $casts = [
        "tanggal_booking" => "datetime",
        "dikonfirmasi_pada" => "datetime",
    ];

    public function getRouteKeyName(): string
    {
        return "id_booking";
    }

    public function praPendaftaranPerkara()
    {
        return $this->belongsTo(
            PraPendaftaranPerkara::class,
            "id_pendaftaran",
            "id_pendaftaran",
        );
    }

    public function jadwalKonsultasi()
    {
        return $this->belongsTo(
            JadwalKonsultasi::class,
            "id_jadwal",
            "id_jadwal",
        );
    }

    public function klien()
    {
        return $this->belongsTo(User::class, "id_user", "id_user");
    }

    public function adminKonfirmasi()
    {
        return $this->belongsTo(User::class, "id_admin_konfirmasi", "id_user");
    }
}
