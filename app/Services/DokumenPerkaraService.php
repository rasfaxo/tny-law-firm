<?php

namespace App\Services;

use App\Models\DokumenPerkara;
use App\Models\PraPendaftaranPerkara;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Support\PerformanceTelemetry;
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
        $filePath = $this->storeUploadedFile($data['file']);

        try {
            return DB::transaction(function () use (
                $pengajuan,
                $data,
                $filePath,
            ): DokumenPerkara {
                return $this->createMetadata($pengajuan, $data, $filePath);
            });
        } catch (Throwable $exception) {
            Storage::disk(config("filesystems.document_disk"))->delete($filePath);

            throw $exception;
        }
    }

    public function storeUploadedFile(UploadedFile $file): string
    {
        $startedAt = PerformanceTelemetry::start();
        $filePath = $file->store('dokumen-perkara', config('filesystems.document_disk'));

        PerformanceTelemetry::record('document.blob_store', $startedAt, [
            'bytes' => (int) ($file->getSize() ?: 0),
        ]);

        if ($filePath === false) {
            throw new RuntimeException('File dokumen gagal disimpan.');
        }

        return $filePath;
    }

    /**
     * @param array{nama_dokumen: string, jenis_dokumen: string, file?: UploadedFile} $data
     */
    public function createMetadata(
        PraPendaftaranPerkara $pengajuan,
        array $data,
        string $filePath,
    ): DokumenPerkara {
        return DokumenPerkara::create([
            'id_pendaftaran' => $pengajuan->id_pendaftaran,
            'nama_dokumen' => $data['nama_dokumen'],
            'jenis_dokumen' => $data['jenis_dokumen'],
            'file_path' => $filePath,
            'status_dokumen' => 'terkirim',
        ]);
    }

    public function deleteStoredFile(string $filePath): void
    {
        Storage::disk(config('filesystems.document_disk'))->delete($filePath);
    }
}
