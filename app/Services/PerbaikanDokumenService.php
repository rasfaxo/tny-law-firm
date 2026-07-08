<?php

namespace App\Services;

use App\Models\CatatanVerifikasi;
use App\Models\DokumenPerkara;
use App\Models\PraPendaftaranPerkara;
use App\Models\RiwayatStatus;
use App\Models\VerifikasiBerkas;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class PerbaikanDokumenService
{
    /**
     * @throws Throwable
     */
    public function uploadReplacement(
        CatatanVerifikasi $catatanVerifikasi,
        UploadedFile $file,
        int $klienId,
    ): DokumenPerkara {
        $filePath = $file->store("dokumen-perkara", "local");

        if ($filePath === false) {
            throw new RuntimeException(
                "File dokumen pengganti gagal disimpan.",
            );
        }

        try {
            return DB::transaction(function () use (
                $catatanVerifikasi,
                $filePath,
                $klienId,
            ): DokumenPerkara {
                $catatan = CatatanVerifikasi::query()
                    ->whereKey($catatanVerifikasi->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($catatan->id_dokumen === null) {
                    throw ValidationException::withMessages([
                        "file" =>
                            "Catatan verifikasi ini tidak terhubung dengan dokumen perkara.",
                    ]);
                }

                $verifikasi = VerifikasiBerkas::query()
                    ->whereKey($catatan->id_verifikasi)
                    ->firstOrFail();

                $pengajuan = PraPendaftaranPerkara::query()
                    ->whereKey($verifikasi->id_pendaftaran)
                    ->lockForUpdate()
                    ->firstOrFail();

                $dokumenLama = DokumenPerkara::query()
                    ->whereKey($catatan->id_dokumen)
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->ensureCanRepair(
                    $catatan,
                    $pengajuan,
                    $dokumenLama,
                    $klienId,
                );

                $dokumenLama->update([
                    "status_dokumen" => "diganti",
                ]);

                $dokumenBaru = DokumenPerkara::create([
                    "id_pendaftaran" => $pengajuan->id_pendaftaran,
                    "nama_dokumen" => $dokumenLama->nama_dokumen,
                    "jenis_dokumen" => $dokumenLama->jenis_dokumen,
                    "file_path" => $filePath,
                    "status_dokumen" => "terkirim",
                ]);

                $catatan->update([
                    "status_perbaikan" => "sudah_diperbaiki",
                ]);

                if (!$this->hasPendingCatatanPerbaikan($pengajuan)) {
                    $pengajuan->update([
                        "status_pengajuan" => "menunggu_verifikasi_ulang",
                    ]);

                    RiwayatStatus::create([
                        "id_pendaftaran" => $pengajuan->id_pendaftaran,
                        "id_user" => $klienId,
                        "status" => "menunggu_verifikasi_ulang",
                        "keterangan" =>
                            "Dokumen perbaikan telah diunggah oleh klien dan menunggu verifikasi ulang",
                    ]);
                }

                return $dokumenBaru;
            });
        } catch (Throwable $exception) {
            Storage::disk("local")->delete($filePath);

            throw $exception;
        }
    }

    private function ensureCanRepair(
        CatatanVerifikasi $catatan,
        PraPendaftaranPerkara $pengajuan,
        DokumenPerkara $dokumenLama,
        int $klienId,
    ): void {
        if ($pengajuan->id_user !== $klienId) {
            throw ValidationException::withMessages([
                "file" => "Dokumen ini tidak dapat diperbaiki oleh akun ini.",
            ]);
        }

        if ($pengajuan->status_pengajuan !== "berkas_tidak_lengkap") {
            throw ValidationException::withMessages([
                "file" =>
                    "Dokumen hanya dapat diperbaiki saat status pengajuan berkas tidak lengkap.",
            ]);
        }

        if ($catatan->status_perbaikan !== "belum_diperbaiki") {
            throw ValidationException::withMessages([
                "file" => "Catatan verifikasi ini sudah diperbaiki.",
            ]);
        }

        if ($dokumenLama->id_pendaftaran !== $pengajuan->id_pendaftaran) {
            throw ValidationException::withMessages([
                "file" => "Dokumen tidak sesuai dengan pengajuan perkara.",
            ]);
        }

        if ($dokumenLama->status_dokumen !== "perlu_perbaikan") {
            throw ValidationException::withMessages([
                "file" => "Dokumen ini tidak berstatus perlu perbaikan.",
            ]);
        }
    }

    private function hasPendingCatatanPerbaikan(
        PraPendaftaranPerkara $pengajuan,
    ): bool {
        return CatatanVerifikasi::query()
            ->where("status_perbaikan", "belum_diperbaiki")
            ->whereNotNull("id_dokumen")
            ->whereHas("verifikasiBerkas", function ($query) use (
                $pengajuan,
            ): void {
                $query->where("id_pendaftaran", $pengajuan->id_pendaftaran);
            })
            ->exists();
    }
}
