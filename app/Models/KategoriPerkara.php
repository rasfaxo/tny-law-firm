<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPerkara extends Model
{
    use HasFactory;

    protected $table = 'kategori_perkara';

    protected $primaryKey = 'id_kategori';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_kategori';
    }

    public function praPendaftaranPerkara()
    {
        return $this->hasMany(PraPendaftaranPerkara::class, 'id_kategori', 'id_kategori');
    }
}
