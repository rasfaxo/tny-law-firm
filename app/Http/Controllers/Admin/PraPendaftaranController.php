<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexFilterRequest;
use App\Models\DokumenPerkara;
use App\Models\KategoriPerkara;
use App\Models\PraPendaftaranPerkara;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PraPendaftaranController extends Controller
{
    public function index(IndexFilterRequest $request): View
    {
        $filters = $request->validated();
        $query = PraPendaftaranPerkara::query()->with(['klien', 'kategori']);

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

        if (isset($filters['tanggal_mulai'])) {
            $query->whereDate('tanggal_pengajuan', '>=', $filters['tanggal_mulai']);
        }

        if (isset($filters['tanggal_selesai'])) {
            $query->whereDate('tanggal_pengajuan', '<=', $filters['tanggal_selesai']);
        }

        $pengajuan = $query->latest('tanggal_pengajuan')->paginate(10)->withQueryString();
        $kategoriList = KategoriPerkara::all();

        return view('admin.pra-pendaftaran.index', compact('pengajuan', 'kategoriList'));
    }

    public function show(PraPendaftaranPerkara $praPendaftaranPerkara): View
    {
        $this->authorize('view', $praPendaftaranPerkara);
        $praPendaftaranPerkara->load([
            'klien.profilKlien',
            'kategori',
            'dokumenPerkara.catatanVerifikasi',
            'verifikasiTerakhir.catatanVerifikasi',
            'riwayatStatus.user',
        ]);

        return view('admin.pra-pendaftaran.show', compact('praPendaftaranPerkara'));
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

        $extension = pathinfo($dokumenPerkara->file_path, PATHINFO_EXTENSION);
        $baseName = Str::slug($dokumenPerkara->nama_dokumen) ?: 'dokumen-perkara';
        $fileName = $extension ? "{$baseName}.{$extension}" : $baseName;

        return Storage::disk(config('filesystems.document_disk'))->download(
            $dokumenPerkara->file_path,
            $fileName
        );
    }
}
