<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanReschedule extends Model
{
    use HasFactory;

    protected $table = 'permintaan_reschedule';

    protected $primaryKey = 'id_reschedule';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_booking',
        'id_user',
        'id_admin_keputusan',
        'alasan_reschedule',
        'preferensi_jadwal',
        'preferensi_metode',
        'status_reschedule',
        'id_jadwal_baru',
        'id_booking_baru',
        'catatan_admin',
        'tanggal_pengajuan',
        'tanggal_keputusan',
    ];

    protected $casts = [
        'id_booking' => 'integer',
        'id_user' => 'integer',
        'id_admin_keputusan' => 'integer',
        'id_jadwal_baru' => 'integer',
        'id_booking_baru' => 'integer',
        'tanggal_pengajuan' => 'datetime',
        'tanggal_keputusan' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_reschedule';
    }

    public function bookingLama()
    {
        return $this->belongsTo(BookingKonsultasi::class, 'id_booking', 'id_booking');
    }

    public function klien()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function adminKeputusan()
    {
        return $this->belongsTo(User::class, 'id_admin_keputusan', 'id_user');
    }

    public function jadwalBaru()
    {
        return $this->belongsTo(JadwalKonsultasi::class, 'id_jadwal_baru', 'id_jadwal');
    }

    public function bookingBaru()
    {
        return $this->belongsTo(BookingKonsultasi::class, 'id_booking_baru', 'id_booking');
    }
}
