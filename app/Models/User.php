<?php

namespace App\Models;

use App\Notifications\QueuedResetPasswordNotification;
use App\Notifications\QueuedVerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nama',
        'email',
        'email_verified_at',
        'password',
        'role',
        'no_telepon',
        'status_akun',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'id_user';
    }

    public function sendEmailVerificationNotification(): void
    {
        if (! $this->isKlien()) {
            return;
        }

        $this->notify(new QueuedVerifyEmailNotification);
    }

    public function sendPasswordResetNotification($token): void
    {
        if (! $this->isKlien() || $this->status_akun !== 'aktif') {
            return;
        }

        $this->notify(new QueuedResetPasswordNotification($token));
    }

    public function profilKlien()
    {
        return $this->hasOne(ProfilKlien::class, 'id_user', 'id_user');
    }

    public function praPendaftaranPerkara()
    {
        return $this->hasMany(
            PraPendaftaranPerkara::class,
            'id_user',
            'id_user',
        );
    }

    public function verifikasiBerkas()
    {
        return $this->hasMany(VerifikasiBerkas::class, 'id_user', 'id_user');
    }

    public function jadwalKonsultasi()
    {
        return $this->hasMany(JadwalKonsultasi::class, 'id_user', 'id_user');
    }

    public function bookingKonsultasi()
    {
        return $this->hasMany(BookingKonsultasi::class, 'id_user', 'id_user');
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatus::class, 'id_user', 'id_user');
    }

    public function privacyConsents()
    {
        return $this->hasMany(PrivacyConsent::class, 'id_user', 'id_user');
    }

    public function hasAcceptedPrivacyPolicy(?string $version = null): bool
    {
        $version ??= config('privacy.policy_version');

        return is_string($version) && $version !== '' && $this->privacyConsents()
            ->where('policy_version', $version)
            ->exists();
    }

    public function isKlien(): bool
    {
        return $this->role === 'klien';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStafLegal(): bool
    {
        return $this->role === 'staf_legal';
    }
}
