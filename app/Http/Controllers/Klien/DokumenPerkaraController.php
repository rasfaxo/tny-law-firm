<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use App\Http\Requests\Klien\StoreDokumenPerkaraRequest;
use App\Models\DokumenPerkara;
use App\Models\PraPendaftaranPerkara;
use App\Services\DokumenPerkaraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DokumenPerkaraController extends Controller
{
    public function create(
        Request $request,
        PraPendaftaranPerkara $praPendaftaranPerkara,
    ): View|RedirectResponse {
        $this->ensurePengajuanOwnedByKlien($request, $praPendaftaranPerkara);

        if (
            $praPendaftaranPerkara->status_pengajuan !== "menunggu_verifikasi"
        ) {
            return redirect()
                ->route("klien.pra-pendaftaran.show", $praPendaftaranPerkara)
                ->with(
                    "error",
                    "Dokumen hanya dapat diunggah saat status pengajuan menunggu verifikasi.",
                );
        }

        return view("klien.dokumen.create", compact("praPendaftaranPerkara"));
    }

    public function store(
        StoreDokumenPerkaraRequest $request,
        PraPendaftaranPerkara $praPendaftaranPerkara,
        DokumenPerkaraService $service,
    ): RedirectResponse {
        $this->ensurePengajuanOwnedByKlien($request, $praPendaftaranPerkara);

        if (
            $praPendaftaranPerkara->status_pengajuan !== "menunggu_verifikasi"
        ) {
            return redirect()
                ->route("klien.pra-pendaftaran.show", $praPendaftaranPerkara)
                ->with(
                    "error",
                    "Dokumen hanya dapat diunggah saat status pengajuan menunggu verifikasi.",
                );
        }

        $service->storeForPengajuan(
            $praPendaftaranPerkara,
            $request->validated(),
        );

        return redirect()
            ->route("klien.pra-pendaftaran.show", $praPendaftaranPerkara)
            ->with("success", "Dokumen perkara berhasil diunggah.");
    }

    public function show(
        Request $request,
        DokumenPerkara $dokumenPerkara,
    ): StreamedResponse {
        $dokumenPerkara->load("praPendaftaranPerkara");

        $this->ensureDokumenOwnedByKlien($request, $dokumenPerkara);

        abort_unless(
            Storage::disk("local")->exists($dokumenPerkara->file_path),
            404,
        );

        return Storage::disk("local")->download(
            $dokumenPerkara->file_path,
            $this->downloadFileName($dokumenPerkara),
        );
    }

    private function ensurePengajuanOwnedByKlien(
        Request $request,
        PraPendaftaranPerkara $praPendaftaranPerkara,
    ): void {
        abort_unless(
            $praPendaftaranPerkara->id_user === $request->user()->id_user,
            403,
        );
    }

    private function ensureDokumenOwnedByKlien(
        Request $request,
        DokumenPerkara $dokumenPerkara,
    ): void {
        abort_unless(
            $dokumenPerkara->praPendaftaranPerkara?->id_user ===
                $request->user()->id_user,
            403,
        );
    }

    private function downloadFileName(DokumenPerkara $dokumenPerkara): string
    {
        $extension = pathinfo($dokumenPerkara->file_path, PATHINFO_EXTENSION);
        $baseName =
            Str::slug($dokumenPerkara->nama_dokumen) ?: "dokumen-perkara";

        return $extension ? "{$baseName}.{$extension}" : $baseName;
    }
}
