<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

    public function scopeBelumDimulai(
        Builder $query,
        ?CarbonInterface $reference = null,
    ): Builder {
        $reference = $reference
            ? Carbon::instance($reference)->setTimezone(config('app.timezone'))
            : now(config('app.timezone'));

        return $query->where(function (Builder $query) use ($reference): void {
            $query
                ->whereDate('tanggal', '>', $reference->toDateString())
                ->orWhere(function (Builder $query) use ($reference): void {
                    $query
                        ->whereDate('tanggal', $reference->toDateString())
                        ->where('waktu_mulai', '>', $reference->format('H:i:s'));
                });
        });
    }

    public function belumDimulai(?CarbonInterface $reference = null): bool
    {
        $reference = $reference
            ? Carbon::instance($reference)->setTimezone(config('app.timezone'))
            : now(config('app.timezone'));

        $waktuMulai = strlen((string) $this->waktu_mulai) === 5
            ? $this->waktu_mulai.':00'
            : substr((string) $this->waktu_mulai, 0, 8);
        $mulai = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $this->tanggal->toDateString().' '.$waktuMulai,
            config('app.timezone'),
        );

        return $mulai->isAfter($reference);
    }

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
