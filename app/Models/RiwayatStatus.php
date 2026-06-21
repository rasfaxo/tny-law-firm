<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatStatus extends Model
{
    protected $table = 'riwayat_status';

    protected $primaryKey = 'id_riwayat';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_pendaftaran',
        'id_user',
        'status',
        'keterangan',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_riwayat';
    }

    public function praPendaftaranPerkara()
    {
        return $this->belongsTo(PraPendaftaranPerkara::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
