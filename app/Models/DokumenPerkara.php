<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DokumenPerkara extends Model
{
    protected $table = "dokumen_perkara";

    protected $primaryKey = "id_dokumen";

    public $incrementing = true;

    protected $keyType = "int";

    protected $fillable = [
        "id_pendaftaran",
        "nama_dokumen",
        "jenis_dokumen",
        "file_path",
        "status_dokumen",
    ];

    public function getRouteKeyName(): string
    {
        return "id_dokumen";
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereIn("status_dokumen", [
            "terkirim",
            "valid",
            "perlu_perbaikan",
        ]);
    }

    public function scopeDiganti(Builder $query): Builder
    {
        return $query->where("status_dokumen", "diganti");
    }

    public function praPendaftaranPerkara()
    {
        return $this->belongsTo(
            PraPendaftaranPerkara::class,
            "id_pendaftaran",
            "id_pendaftaran",
        );
    }

    public function catatanVerifikasi()
    {
        return $this->hasMany(
            CatatanVerifikasi::class,
            "id_dokumen",
            "id_dokumen",
        );
    }
}
