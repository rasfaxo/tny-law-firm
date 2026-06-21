<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiBerkas extends Model
{
    protected $table = 'verifikasi_berkas';

    protected $primaryKey = 'id_verifikasi';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_pendaftaran',
        'id_user',
        'status_verifikasi',
        'tanggal_verifikasi',
        'catatan_umum',
    ];

    protected $casts = [
        'tanggal_verifikasi' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_verifikasi';
    }

    public function praPendaftaranPerkara()
    {
        return $this->belongsTo(PraPendaftaranPerkara::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function stafLegal()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function catatanVerifikasi()
    {
        return $this->hasMany(CatatanVerifikasi::class, 'id_verifikasi', 'id_verifikasi');
    }
}
