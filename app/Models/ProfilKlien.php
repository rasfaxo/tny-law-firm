<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilKlien extends Model
{
    use HasFactory;

    protected $table = 'profil_klien';

    protected $primaryKey = 'id_profil';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_user',
        'alamat',
        'jenis_kelamin',
        'pekerjaan',
        'no_identitas',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_profil';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
