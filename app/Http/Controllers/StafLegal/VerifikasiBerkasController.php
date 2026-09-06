<?php

namespace App\Http\Controllers\StafLegal;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexFilterRequest;
use App\Http\Requests\StafLegal\StoreVerifikasiBerkasRequest;
use App\Models\DokumenPerkara;
use App\Models\KategoriPerkara;
use App\Models\PraPendaftaranPerkara;
use App\Models\VerifikasiBerkas;
use App\Services\AuditLogService;
use App\Services\VerifikasiBerkasService;
use App\Support\PerformanceTelemetry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerifikasiBerkasController extends Controller
{
    public function index(IndexFilterRequest $request): View
    {
        $filters = $request->validated();
        $query = PraPendaftaranPerkara::query()
            ->with(['klien', 'kategori'])
            ->withCount('dokumenAktif');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('judul_perkara', 'like', "%{$search}%")
                    ->orWhere('id_pendaftaran', 'like', "%{$search}%")
                    ->orWhereHas('klien', function ($qk) use ($search) {
                        $qk->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if (isset($filters['status'])) {
            $query->where('status_pengajuan', $filters['status']);
        }

        if (isset($filters['kategori'])) {
            $query->where('id_kategori', $filters['kategori']);
        }

        $pengajuan = $query->latest('tanggal_pengajuan')->paginate(10)->withQueryString();
        $kategoriList = KategoriPerkara::all();

        return view('staf-legal.verifikasi-berkas.index', compact('pengajuan', 'kategoriList'));
    }

    public function riwayat(): View
    {
        $startedAt = PerformanceTelemetry::start();
        $riwayat = VerifikasiBerkas::query()
            ->with(['praPendaftaranPerkara.klien', 'praPendaftaranPerkara.kategori'])
            ->where('id_user', auth()->id())
            ->latest('tanggal_verifikasi')
            ->paginate(10);

        $view = view('staf-legal.verifikasi-berkas.riwayat', compact('riwayat'));

        PerformanceTelemetry::record('verification_history.render', $startedAt);

        return $view;
    }

    public function show(PraPendaftaranPerkara $praPendaftaranPerkara): View
    {
        $this->authorize('view', $praPendaftaranPerkara);
        $praPendaftaranPerkara->load([
            'klien',
            'kategori',
            'dokumenAktif' => fn ($query) => $query->oldest(),
            'riwayatDokumen' => fn ($query) => $query->oldest(),
        ]);

        return view(
            'staf-legal.verifikasi-berkas.show',
            compact('praPendaftaranPerkara'),
        );
    }

    public function verifikasi(PraPendaftaranPerkara $praPendaftaranPerkara): View|RedirectResponse
    {
        $this->authorize('view', $praPendaftaranPerkara);
        if (! $this->isVerifiable($praPendaftaranPerkara)) {
            return redirect()
                ->route('staf-legal.verifikasi-berkas.index')
                ->with(
                    'error',
                    'Pengajuan ini tidak dapat diverifikasi pada status saat ini.',
                );
        }

        $praPendaftaranPerkara->load([
            'klien',
            'kategori',
            'dokumenAktif' => fn ($query) => $query->oldest(),
            'riwayatDokumen' => fn ($query) => $query->oldest(),
        ]);

        return view(
            'staf-legal.verifikasi-berkas.verifikasi',
            compact('praPendaftaranPerkara'),
        );
    }

    public function store(
        StoreVerifikasiBerkasRequest $request,
        PraPendaftaranPerkara $praPendaftaranPerkara,
        VerifikasiBerkasService $service,
    ): RedirectResponse {
        if (! $this->isVerifiable($praPendaftaranPerkara)) {
            return redirect()
                ->route('staf-legal.verifikasi-berkas.index')
                ->with(
                    'error',
                    'Pengajuan ini tidak dapat diverifikasi pada status saat ini.',
                );
        }

        $service->verify(
            $praPendaftaranPerkara,
            $request->validated(),
            $request->user()->id_user,
        );

        return redirect()
            ->route('staf-legal.verifikasi-berkas.riwayat')
            ->with('success', 'Hasil verifikasi berkas berhasil disimpan.');
    }

    public function showDokumen(
        DokumenPerkara $dokumenPerkara,
        AuditLogService $auditLog,
    ): StreamedResponse {
        $dokumenPerkara->load('praPendaftaranPerkara');

        $this->authorize('view', $dokumenPerkara);

        abort_unless(
            Storage::disk(config('filesystems.document_disk'))->exists($dokumenPerkara->file_path),
            404,
        );

        $auditLog->record('document.downloaded', $dokumenPerkara, request()->user(), [
            'status' => $dokumenPerkara->status_dokumen,
        ]);

        return Storage::disk(config('filesystems.document_disk'))->download(
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
            Str::slug($dokumenPerkara->nama_dokumen) ?: 'dokumen-perkara';

        return $extension ? "{$baseName}.{$extension}" : $baseName;
    }
}
