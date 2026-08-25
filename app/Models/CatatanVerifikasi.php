<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanVerifikasi extends Model
{
    use HasFactory;

    protected $table = 'catatan_verifikasi';

    protected $primaryKey = 'id_catatan';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_verifikasi',
        'id_dokumen',
        'isi_catatan',
        'status_perbaikan',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_catatan';
    }

    public function verifikasiBerkas()
    {
        return $this->belongsTo(VerifikasiBerkas::class, 'id_verifikasi', 'id_verifikasi');
    }

    public function dokumenPerkara()
    {
        return $this->belongsTo(DokumenPerkara::class, 'id_dokumen', 'id_dokumen');
    }
}
