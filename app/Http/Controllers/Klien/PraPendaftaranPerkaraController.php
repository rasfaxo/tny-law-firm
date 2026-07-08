<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use App\Http\Requests\Klien\StorePraPendaftaranPerkaraRequest;
use App\Models\KategoriPerkara;
use App\Models\PraPendaftaranPerkara;
use App\Services\PraPendaftaranPerkaraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PraPendaftaranPerkaraController extends Controller
{
    public function index(Request $request): View
    {
        $query = PraPendaftaranPerkara::query()
            ->with("kategori")
            ->where("id_user", $request->user()->id_user);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('judul_perkara', 'like', '%' . $search . '%');
                
                // Cek jika input mencari kode, e.g. PP-001
                $cleanSearch = preg_replace('/[^0-9]/', '', $search);
                if (!empty($cleanSearch)) {
                    $q->orWhere('id_pendaftaran', (int)$cleanSearch);
                }
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status_pengajuan', $request->input('status'));
        }

        $praPendaftaranPerkara = $query->latest("tanggal_pengajuan")
            ->paginate(10)
            ->withQueryString(); // Mempertahankan query string saat paginasi

        return view(
            "klien.pra-pendaftaran.index",
            compact("praPendaftaranPerkara"),
        );
    }

    public function create(): View
    {
        $kategoriPerkara = KategoriPerkara::query()
            ->orderBy("nama_kategori")
            ->get();

        return view("klien.pra-pendaftaran.create", compact("kategoriPerkara"));
    }

    public function store(
        StorePraPendaftaranPerkaraRequest $request,
        PraPendaftaranPerkaraService $service,
    ): RedirectResponse {
        $pengajuan = $service->createForKlien(
            $request->validated(),
            $request->user()->id_user,
        );

        return redirect()
            ->route("klien.pra-pendaftaran.show", $pengajuan)
            ->with("success", "Pra-pendaftaran perkara berhasil dibuat.");
    }

    public function show(
        Request $request,
        PraPendaftaranPerkara $praPendaftaranPerkara,
    ): View {
        abort_unless(
            $praPendaftaranPerkara->id_user === $request->user()->id_user,
            403,
        );

        $praPendaftaranPerkara->load([
            "kategori",
            "dokumenAktif" => fn($query) => $query->latest(),
            "riwayatDokumen" => fn($query) => $query->latest(),
            "verifikasiBerkas" => fn($query) => $query->latest(
                "tanggal_verifikasi",
            ),
            "verifikasiBerkas.catatanVerifikasi" => fn($query) => $query
                ->whereNotNull("id_dokumen")
                ->latest(),
            "verifikasiBerkas.catatanVerifikasi.dokumenPerkara",
            "bookingAktif.adminKonfirmasi",
            "bookingAktif.jadwalKonsultasi",
            "bookingAktif.permintaanReschedule" => fn($query) => $query->latest(
                "tanggal_pengajuan",
            ),
            "bookingAktif.permintaanReschedule.bookingBaru.jadwalKonsultasi",
            "bookingAktif.permintaanReschedule.jadwalBaru",
            "bookingTerakhir.adminKonfirmasi",
            "bookingTerakhir.jadwalKonsultasi",
            "bookingKonsultasi.permintaanReschedule" => fn(
                $query,
            ) => $query->latest("tanggal_pengajuan"),
            "bookingKonsultasi.permintaanReschedule.bookingBaru.jadwalKonsultasi",
            "bookingKonsultasi.permintaanReschedule.jadwalBaru",
            "riwayatStatus" => fn($query) => $query->with("user")->oldest(),
        ]);

        return view(
            "klien.pra-pendaftaran.show",
            compact("praPendaftaranPerkara"),
        );
    }
}
