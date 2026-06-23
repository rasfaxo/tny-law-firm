<?php

namespace App\Services;

use App\Models\CatatanVerifikasi;
use App\Models\DokumenPerkara;
use App\Models\PraPendaftaranPerkara;
use App\Models\RiwayatStatus;
use App\Models\VerifikasiBerkas;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class VerifikasiBerkasService
{
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
        return DB::transaction(function () use (
            $pengajuan,
            $data,
            $stafLegalId,
        ): VerifikasiBerkas {
            $lockedPengajuan = PraPendaftaranPerkara::query()
                ->whereKey($pengajuan->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($lockedPengajuan->status_pengajuan, self::VERIFIABLE_STATUSES, true)) {
                throw ValidationException::withMessages([
                    "status_verifikasi" => "Pengajuan ini tidak dapat diverifikasi pada status saat ini.",
                ]);
            }

            $documents = DokumenPerkara::query()
                ->where("id_pendaftaran", $lockedPengajuan->id_pendaftaran)
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
                $documents->each(function (DokumenPerkara $dokumen): void {
                    $dokumen->update(["status_dokumen" => "valid"]);
                });
            }

            if ($data["status_verifikasi"] === "berkas_tidak_lengkap") {
                foreach ($submittedDocuments as $documentId => $documentData) {
                    $dokumen = $documents->get((int) $documentId);

                    if (!$dokumen instanceof DokumenPerkara) {
                        continue;
                    }

                    $dokumen->update([
                        "status_dokumen" => $documentData["status_dokumen"],
                    ]);

                    if ($documentData["status_dokumen"] !== "perlu_perbaikan") {
                        continue;
                    }

                    CatatanVerifikasi::create([
                        "id_verifikasi" => $verifikasi->id_verifikasi,
                        "id_dokumen" => $dokumen->id_dokumen,
                        "isi_catatan" => $documentData["catatan"],
                        "status_perbaikan" => "belum_diperbaiki",
                    ]);
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
    }

    /**
     * @return array<int, string>
     */
    public static function verifiableStatuses(): array
    {
        return self::VERIFIABLE_STATUSES;
    }

    private function riwayatKeterangan(string $status): string
    {
        return match ($status) {
            "berkas_lengkap" => "Berkas diverifikasi lengkap oleh Staf Legal.",
            "berkas_tidak_lengkap" => "Berkas diverifikasi tidak lengkap oleh Staf Legal.",
            default => "Status pengajuan diperbarui oleh Staf Legal.",
        };
    }
}
