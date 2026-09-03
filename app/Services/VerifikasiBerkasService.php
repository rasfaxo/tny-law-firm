<?php

namespace App\Services;

use App\Enums\StatusPengajuan;
use App\Models\CatatanVerifikasi;
use App\Models\DokumenPerkara;
use App\Models\PraPendaftaranPerkara;
use App\Models\RiwayatStatus;
use App\Models\VerifikasiBerkas;
use App\Support\PerformanceTelemetry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class VerifikasiBerkasService
{
    // Dipindahkan ke StatusPengajuan::verifiableStatuses() agar satu source of truth.
    // Const ini dipertahankan untuk backward compat internal service.
    private const VERIFIABLE_STATUSES = [
        "menunggu_verifikasi",
        "menunggu_verifikasi_ulang",
    ];

    /**
     * @param array{
     *     status_verifikasi: string,
     *     catatan_umum?: string|null,
     *     dokumen?: array<int|string, array{status_dokumen: string, catatan?: string|null}>
     * } $data
     *
     * @throws Throwable
     */
    public function verify(
        PraPendaftaranPerkara $pengajuan,
        array $data,
        int $stafLegalId,
    ): VerifikasiBerkas {
        $transactionStartedAt = PerformanceTelemetry::start();

        $verifikasi = DB::transaction(function () use (
            $pengajuan,
            $data,
            $stafLegalId,
        ): VerifikasiBerkas {
            $lockedPengajuan = PraPendaftaranPerkara::query()
                ->whereKey($pengajuan->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (
                !in_array(
                    $lockedPengajuan->status_pengajuan,
                    self::VERIFIABLE_STATUSES,
                    true,
                )
            ) {
                throw ValidationException::withMessages([
                    "status_verifikasi" =>
                        "Pengajuan ini tidak dapat diverifikasi pada status saat ini.",
                ]);
            }

            $documents = DokumenPerkara::query()
                ->where("id_pendaftaran", $lockedPengajuan->id_pendaftaran)
                ->aktif()
                ->lockForUpdate()
                ->get()
                ->keyBy("id_dokumen");

            $submittedDocuments = $data["dokumen"] ?? [];
            $newPengajuanStatus = $data["status_verifikasi"];

            $verifikasi = VerifikasiBerkas::create([
                "id_pendaftaran" => $lockedPengajuan->id_pendaftaran,
                "id_user" => $stafLegalId,
                "status_verifikasi" => $data["status_verifikasi"],
                "tanggal_verifikasi" => now(),
                "catatan_umum" => $data["catatan_umum"] ?? null,
            ]);

            if ($data["status_verifikasi"] === "berkas_lengkap") {
                DokumenPerkara::query()
                    ->where("id_pendaftaran", $lockedPengajuan->id_pendaftaran)
                    ->aktif()
                    ->update([
                        "status_dokumen" => "valid",
                        "updated_at" => now(),
                    ]);
            }

            if ($data["status_verifikasi"] === "berkas_tidak_lengkap") {
                $documentIdsByStatus = [];
                $catatanRows = [];
                $timestamp = now();

                foreach ($submittedDocuments as $documentId => $documentData) {
                    $dokumen = $documents->get((int) $documentId);

                    if (! $dokumen instanceof DokumenPerkara) {
                        continue;
                    }

                    $documentIdsByStatus[$documentData['status_dokumen']][] = $dokumen->id_dokumen;

                    if ($documentData['status_dokumen'] === 'perlu_perbaikan') {
                        $catatanRows[] = [
                            'id_verifikasi' => $verifikasi->id_verifikasi,
                            'id_dokumen' => $dokumen->id_dokumen,
                            // Form request already requires this note. The fallback
                            // keeps the current defensive behaviour unchanged.
                            'isi_catatan' => $documentData['catatan'] ?? '',
                            'status_perbaikan' => 'belum_diperbaiki',
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];
                    }
                }

                foreach ($documentIdsByStatus as $status => $documentIds) {
                    DokumenPerkara::query()
                        ->whereKey($documentIds)
                        ->update([
                            'status_dokumen' => $status,
                            'updated_at' => $timestamp,
                        ]);
                }

                if ($catatanRows !== []) {
                    CatatanVerifikasi::query()->insert($catatanRows);
                }
            }

            $lockedPengajuan->update([
                "status_pengajuan" => $newPengajuanStatus,
            ]);

            RiwayatStatus::create([
                "id_pendaftaran" => $lockedPengajuan->id_pendaftaran,
                "id_user" => $stafLegalId,
                "status" => $newPengajuanStatus,
                "keterangan" => $this->riwayatKeterangan($newPengajuanStatus),
            ]);

            return $verifikasi;
        });

        PerformanceTelemetry::record('case_verification.database_transaction', $transactionStartedAt, [
            'submitted_document_count' => count($data['dokumen'] ?? []),
        ]);

        return $verifikasi;
    }

    /**
     * @return array<int, string>
     */
    public static function verifiableStatuses(): array
    {
        return StatusPengajuan::verifiableStatuses();
    }

    private function riwayatKeterangan(string $status): string
    {
        return match ($status) {
            "berkas_lengkap" => "Berkas diverifikasi lengkap oleh Staf Legal.",
            "berkas_tidak_lengkap"
                => "Berkas diverifikasi tidak lengkap oleh Staf Legal.",
            default => "Status pengajuan diperbarui oleh Staf Legal.",
        };
    }
}
