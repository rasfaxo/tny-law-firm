<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = "users";

    protected $primaryKey = "id_user";

    public $incrementing = true;

    protected $keyType = "int";

    protected $fillable = [
        "nama",
        "email",
        "password",
        "role",
        "no_telepon",
        "status_akun",
    ];

    protected $hidden = ["password"];

    protected function casts(): array
    {
        return [
            "password" => "hashed",
        ];
    }

    public function getRouteKeyName(): string
    {
        return "id_user";
    }

    public function profilKlien()
    {
        return $this->hasOne(ProfilKlien::class, "id_user", "id_user");
    }

    public function praPendaftaranPerkara()
    {
        return $this->hasMany(
            PraPendaftaranPerkara::class,
            "id_user",
            "id_user",
        );
    }

    public function verifikasiBerkas()
    {
        return $this->hasMany(VerifikasiBerkas::class, "id_user", "id_user");
    }

    public function jadwalKonsultasi()
    {
        return $this->hasMany(JadwalKonsultasi::class, "id_user", "id_user");
    }

    public function bookingKonsultasi()
    {
        return $this->hasMany(BookingKonsultasi::class, "id_user", "id_user");
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatus::class, "id_user", "id_user");
    }

    public function isKlien(): bool
    {
        return $this->role === "klien";
    }

    public function isAdmin(): bool
    {
        return $this->role === "admin";
    }

    public function isStafLegal(): bool
    {
        return $this->role === "staf_legal";
    }
}
