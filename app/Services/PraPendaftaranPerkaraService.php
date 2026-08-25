<?php

namespace App\Services;

use App\Models\PraPendaftaranPerkara;
use App\Models\RiwayatStatus;
use Illuminate\Support\Facades\DB;

class PraPendaftaranPerkaraService
{
    public function __construct(
        private DokumenPerkaraService $dokumenService
    ) {}

    /**
     * @param array{id_kategori: mixed, judul_perkara: string, kronologi: string, dokumen: array<int, array{nama_dokumen: string, jenis_dokumen: string, file_dokumen: \Illuminate\Http\UploadedFile}>} $data
     */
    public function createForKlien(array $data, int $userId): PraPendaftaranPerkara
    {
        return DB::transaction(function () use ($data, $userId): PraPendaftaranPerkara {
            $pengajuan = PraPendaftaranPerkara::create([
                "id_user" => $userId,
                "id_kategori" => $data["id_kategori"],
                "judul_perkara" => $data["judul_perkara"],
                "kronologi" => $data["kronologi"],
                "status_pengajuan" => "menunggu_verifikasi",
                "tanggal_pengajuan" => now(),
            ]);

            RiwayatStatus::create([
                "id_pendaftaran" => $pengajuan->id_pendaftaran,
                "id_user" => $userId,
                "status" => "menunggu_verifikasi",
                "keterangan" => "Pengajuan pra-pendaftaran perkara dibuat oleh klien",
            ]);

            // Save all documents
            foreach ($data["dokumen"] as $doc) {
                $this->dokumenService->storeForPengajuan($pengajuan, [
                    "nama_dokumen" => $doc["nama_dokumen"],
                    "jenis_dokumen" => $doc["jenis_dokumen"],
                    "file" => $doc["file_dokumen"],
                ]);
            }

            return $pengajuan;
        });
    }
}
