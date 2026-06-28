<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'jadwal_konsultasi';

    protected $primaryKey = 'id_jadwal';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_user',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'status_slot',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_jadwal';
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function bookingKonsultasi()
    {
        return $this->hasMany(BookingKonsultasi::class, 'id_jadwal', 'id_jadwal');
    }

    public function bookingAktif()
    {
        return $this->hasOne(BookingKonsultasi::class, 'id_jadwal', 'id_jadwal')
            ->where('status_booking', 'aktif');
    }
}
