<?php

namespace App\Services;

use App\Models\DokumenPerkara;
use App\Models\PraPendaftaranPerkara;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class DokumenPerkaraService
{
    /**
     * @param array{nama_dokumen: string, jenis_dokumen: string, file: UploadedFile} $data
     *
     * @throws Throwable
     */
    public function storeForPengajuan(
        PraPendaftaranPerkara $pengajuan,
        array $data,
    ): DokumenPerkara {
        $filePath = $data["file"]->store("dokumen-perkara", "public");

        if ($filePath === false) {
            throw new RuntimeException("File dokumen gagal disimpan.");
        }

        try {
            return DB::transaction(function () use (
                $pengajuan,
                $data,
                $filePath,
            ): DokumenPerkara {
                return DokumenPerkara::create([
                    "id_pendaftaran" => $pengajuan->id_pendaftaran,
                    "nama_dokumen" => $data["nama_dokumen"],
                    "jenis_dokumen" => $data["jenis_dokumen"],
                    "file_path" => $filePath,
                    "status_dokumen" => "terkirim",
                ]);
            });
        } catch (Throwable $exception) {
            Storage::disk("public")->delete($filePath);

            throw $exception;
        }
    }
}
