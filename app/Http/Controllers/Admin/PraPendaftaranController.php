<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriPerkara;
use App\Models\PraPendaftaranPerkara;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PraPendaftaranController extends Controller
{
    public function index(Request $request): View
    {
        $query = PraPendaftaranPerkara::query()->with(["klien", "kategori"]);

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

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_pengajuan', '>=', $request->input('tanggal_mulai'));
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_pengajuan', '<=', $request->input('tanggal_selesai'));
        }

        $pengajuan = $query->latest("tanggal_pengajuan")->paginate(10)->withQueryString();
        $kategoriList = KategoriPerkara::all();

        return view('admin.pra-pendaftaran.index', compact('pengajuan', 'kategoriList'));
    }

    public function show(PraPendaftaranPerkara $praPendaftaranPerkara): View
    {
        $praPendaftaranPerkara->load([
            'klien.profilKlien',
            'kategori',
            'dokumenPerkara.catatanVerifikasi',
            'verifikasiTerakhir.catatanVerifikasi',
            'riwayatStatus.user'
        ]);

        return view('admin.pra-pendaftaran.show', compact('praPendaftaranPerkara'));
    }
}
