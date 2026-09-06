<?php

namespace App\Services;

use App\Models\PraPendaftaranPerkara;
use App\Models\RiwayatStatus;
use App\Models\User;
use App\Support\PerformanceTelemetry;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

class PraPendaftaranPerkaraService
{
    public function __construct(
        private DokumenPerkaraService $dokumenService,
        private AuditLogService $auditLog,
    ) {}

    /**
     * @param  array{id_kategori: mixed, judul_perkara: string, kronologi: string, dokumen: array<int, array{nama_dokumen: string, jenis_dokumen: string, file_dokumen: UploadedFile}>}  $data
     */
    public function createForKlien(array $data, int $userId): PraPendaftaranPerkara
    {
        /** @var array<int, array{nama_dokumen: string, jenis_dokumen: string, file_path: string}> $uploadedDocuments */
        $uploadedDocuments = [];

        try {
            foreach ($data['dokumen'] as $document) {
                $uploadedDocuments[] = [
                    'nama_dokumen' => $document['nama_dokumen'],
                    'jenis_dokumen' => $document['jenis_dokumen'],
                    'file_path' => $this->dokumenService->storeUploadedFile($document['file_dokumen']),
                ];
            }
        } catch (Throwable $exception) {
            $this->deleteUploadedDocuments($uploadedDocuments);

            throw $exception;
        }

        $transactionStartedAt = PerformanceTelemetry::start();

        try {
            $pengajuan = DB::transaction(function () use ($data, $userId, $uploadedDocuments): PraPendaftaranPerkara {
                $pengajuan = PraPendaftaranPerkara::create([
                    'id_user' => $userId,
                    'id_kategori' => $data['id_kategori'],
                    'judul_perkara' => $data['judul_perkara'],
                    'kronologi' => $data['kronologi'],
                    'status_pengajuan' => 'menunggu_verifikasi',
                    'tanggal_pengajuan' => now(),
                ]);

                RiwayatStatus::create([
                    'id_pendaftaran' => $pengajuan->id_pendaftaran,
                    'id_user' => $userId,
                    'status' => 'menunggu_verifikasi',
                    'keterangan' => 'Pengajuan pra-pendaftaran perkara dibuat oleh klien',
                ]);

                foreach ($uploadedDocuments as $document) {
                    $dokumen = $this->dokumenService->createMetadata($pengajuan, $document, $document['file_path']);
                    $this->auditLog->record(
                        'document.uploaded',
                        $dokumen,
                        User::query()->findOrFail($userId),
                        ['status' => 'terkirim'],
                    );
                }

                $this->auditLog->record(
                    'case.submitted',
                    $pengajuan,
                    User::query()->findOrFail($userId),
                    ['document_count' => count($uploadedDocuments), 'status' => 'menunggu_verifikasi'],
                );

                return $pengajuan;
            });
        } catch (Throwable $exception) {
            $this->deleteUploadedDocuments($uploadedDocuments);

            throw $exception;
        }

        PerformanceTelemetry::record('case_submission.database_transaction', $transactionStartedAt, [
            'document_count' => count($uploadedDocuments),
        ]);

        return $pengajuan;
    }

    /** @param array<int, array{file_path: string}> $uploadedDocuments */
    private function deleteUploadedDocuments(array $uploadedDocuments): void
    {
        foreach ($uploadedDocuments as $document) {
            try {
                $this->dokumenService->deleteStoredFile($document['file_path']);
            } catch (Throwable) {
                // The original exception is more useful to the caller. A failed
                // compensating delete is still visible through storage telemetry.
            }
        }
    }
}
