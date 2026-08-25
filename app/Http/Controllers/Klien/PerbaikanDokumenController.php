<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use App\Http\Requests\Klien\StorePerbaikanDokumenRequest;
use App\Models\CatatanVerifikasi;
use App\Models\DokumenPerkara;
use App\Models\PraPendaftaranPerkara;
use App\Services\PerbaikanDokumenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerbaikanDokumenController extends Controller
{
    public function create(
        Request $request,
        CatatanVerifikasi $catatanVerifikasi,
    ): View|RedirectResponse {
        $this->loadRepairContext($catatanVerifikasi);

        $pengajuan = $catatanVerifikasi->verifikasiBerkas?->praPendaftaranPerkara;
        $dokumen = $catatanVerifikasi->dokumenPerkara;

        abort_unless(
            $pengajuan instanceof PraPendaftaranPerkara &&
                $pengajuan->id_user === $request->user()->id_user,
            403,
        );

        if (!$this->canRepair($catatanVerifikasi, $pengajuan, $dokumen)) {
            return redirect()
                ->route("klien.pra-pendaftaran.show", $pengajuan)
                ->with(
                    "error",
                    "Catatan verifikasi ini tidak dapat diperbaiki.",
                );
        }

        return view(
            "klien.perbaikan-dokumen.create",
            compact("catatanVerifikasi", "pengajuan", "dokumen"),
        );
    }

    public function store(
        StorePerbaikanDokumenRequest $request,
        CatatanVerifikasi $catatanVerifikasi,
        PerbaikanDokumenService $service,
    ): RedirectResponse {
        $this->loadRepairContext($catatanVerifikasi);

        $pengajuan = $catatanVerifikasi->verifikasiBerkas?->praPendaftaranPerkara;
        $dokumen = $catatanVerifikasi->dokumenPerkara;

        abort_unless(
            $pengajuan instanceof PraPendaftaranPerkara &&
                $pengajuan->id_user === $request->user()->id_user,
            403,
        );

        if (!$this->canRepair($catatanVerifikasi, $pengajuan, $dokumen)) {
            return redirect()
                ->route("klien.pra-pendaftaran.show", $pengajuan)
                ->with(
                    "error",
                    "Catatan verifikasi ini tidak dapat diperbaiki.",
                );
        }

        $service->uploadReplacement(
            $catatanVerifikasi,
            $request->file("file"),
            $request->user()->id_user,
        );

        return redirect()
            ->route("klien.pra-pendaftaran.show", $pengajuan)
            ->with("success", "Dokumen pengganti berhasil diunggah.");
    }

    private function loadRepairContext(CatatanVerifikasi $catatanVerifikasi): void
    {
        $catatanVerifikasi->load([
            "dokumenPerkara",
            "verifikasiBerkas.praPendaftaranPerkara",
        ]);
    }

    private function canRepair(
        CatatanVerifikasi $catatanVerifikasi,
        PraPendaftaranPerkara $pengajuan,
        ?DokumenPerkara $dokumen,
    ): bool {
        return $pengajuan->status_pengajuan === "berkas_tidak_lengkap" &&
            $catatanVerifikasi->status_perbaikan === "belum_diperbaiki" &&
            $catatanVerifikasi->id_dokumen !== null &&
            $dokumen instanceof DokumenPerkara &&
            $dokumen->id_pendaftaran === $pengajuan->id_pendaftaran &&
            $dokumen->status_dokumen === "perlu_perbaikan";
    }
}
