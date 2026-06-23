<?php

namespace App\Http\Controllers\StafLegal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StafLegal\StoreVerifikasiBerkasRequest;
use App\Models\DokumenPerkara;
use App\Models\PraPendaftaranPerkara;
use App\Services\VerifikasiBerkasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerifikasiBerkasController extends Controller
{
    public function index(): View
    {
        $pengajuan = PraPendaftaranPerkara::query()
            ->with(["klien", "kategori"])
            ->withCount("dokumenAktif")
            ->whereIn(
                "status_pengajuan",
                VerifikasiBerkasService::verifiableStatuses(),
            )
            ->latest("tanggal_pengajuan")
            ->paginate(10);

        return view("staf-legal.verifikasi-berkas.index", compact("pengajuan"));
    }

    public function show(
        PraPendaftaranPerkara $praPendaftaranPerkara,
    ): View|RedirectResponse {
        if (!$this->isVerifiable($praPendaftaranPerkara)) {
            return redirect()
                ->route("staf-legal.verifikasi-berkas.index")
                ->with(
                    "error",
                    "Pengajuan ini tidak dapat diverifikasi pada status saat ini.",
                );
        }

        $praPendaftaranPerkara->load([
            "klien",
            "kategori",
            "dokumenAktif" => fn($query) => $query->oldest(),
            "riwayatDokumen" => fn($query) => $query->oldest(),
        ]);

        return view(
            "staf-legal.verifikasi-berkas.show",
            compact("praPendaftaranPerkara"),
        );
    }

    public function store(
        StoreVerifikasiBerkasRequest $request,
        PraPendaftaranPerkara $praPendaftaranPerkara,
        VerifikasiBerkasService $service,
    ): RedirectResponse {
        if (!$this->isVerifiable($praPendaftaranPerkara)) {
            return redirect()
                ->route("staf-legal.verifikasi-berkas.index")
                ->with(
                    "error",
                    "Pengajuan ini tidak dapat diverifikasi pada status saat ini.",
                );
        }

        $service->verify(
            $praPendaftaranPerkara,
            $request->validated(),
            $request->user()->id_user,
        );

        return redirect()
            ->route("staf-legal.verifikasi-berkas.index")
            ->with("success", "Hasil verifikasi berkas berhasil disimpan.");
    }

    public function showDokumen(
        DokumenPerkara $dokumenPerkara,
    ): StreamedResponse {
        $dokumenPerkara->load("praPendaftaranPerkara");

        abort_unless(
            $dokumenPerkara->praPendaftaranPerkara instanceof
                PraPendaftaranPerkara &&
                $this->isVerifiable($dokumenPerkara->praPendaftaranPerkara),
            403,
        );

        abort_unless(
            Storage::disk("public")->exists($dokumenPerkara->file_path),
            404,
        );

        return Storage::disk("public")->download(
            $dokumenPerkara->file_path,
            $this->downloadFileName($dokumenPerkara),
        );
    }

    private function isVerifiable(PraPendaftaranPerkara $pengajuan): bool
    {
        return in_array(
            $pengajuan->status_pengajuan,
            VerifikasiBerkasService::verifiableStatuses(),
            true,
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
