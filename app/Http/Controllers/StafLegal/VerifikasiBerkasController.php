<?php

namespace App\Http\Controllers\StafLegal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StafLegal\StoreVerifikasiBerkasRequest;
use App\Models\DokumenPerkara;
use App\Models\PraPendaftaranPerkara;
use App\Services\VerifikasiBerkasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerifikasiBerkasController extends Controller
{
    public function index(Request $request): View
    {
        $query = PraPendaftaranPerkara::query()
            ->with(["klien", "kategori"])
            ->withCount("dokumenAktif");

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('judul_perkara', 'like', "%{$search}%")
                  ->orWhere('id_pendaftaran', 'like', "%{$search}%")
                  ->orWhereHas('klien', function ($qk) use ($search) {
                      $qk->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status_pengajuan', $request->input('status'));
        }

        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->input('kategori'));
        }

        $pengajuan = $query->latest("tanggal_pengajuan")->paginate(10)->withQueryString();
        $kategoriList = \App\Models\KategoriPerkara::all();

        return view("staf-legal.verifikasi-berkas.index", compact("pengajuan", "kategoriList"));
    }

    public function riwayat(): View
    {
        $riwayat = \App\Models\VerifikasiBerkas::query()
            ->with(["praPendaftaranPerkara.klien", "praPendaftaranPerkara.kategori"])
            ->where("id_user", auth()->id())
            ->latest("tanggal_verifikasi")
            ->paginate(10);

        return view("staf-legal.verifikasi-berkas.riwayat", compact("riwayat"));
    }

    public function show(PraPendaftaranPerkara $praPendaftaranPerkara): View
    {
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

    public function verifikasi(PraPendaftaranPerkara $praPendaftaranPerkara): View|RedirectResponse
    {
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
            "staf-legal.verifikasi-berkas.verifikasi",
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
            ->route("staf-legal.verifikasi-berkas.riwayat")
            ->with("success", "Hasil verifikasi berkas berhasil disimpan.");
    }

    public function showDokumen(
        DokumenPerkara $dokumenPerkara,
    ): StreamedResponse {
        $dokumenPerkara->load("praPendaftaranPerkara");

        // Staf Legal can view any document belonging to a case
        abort_unless(
            $dokumenPerkara->praPendaftaranPerkara instanceof PraPendaftaranPerkara,
            403,
        );

        abort_unless(
            Storage::disk("local")->exists($dokumenPerkara->file_path),
            404,
        );

        return Storage::disk("local")->download(
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
