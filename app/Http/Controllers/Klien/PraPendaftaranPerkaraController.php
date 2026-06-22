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
        $praPendaftaranPerkara = PraPendaftaranPerkara::query()
            ->with("kategori")
            ->where("id_user", $request->user()->id_user)
            ->latest("tanggal_pengajuan")
            ->paginate(10);

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
            "dokumenPerkara" => fn($query) => $query->latest(),
            "riwayatStatus" => fn($query) => $query->with("user")->oldest(),
        ]);

        return view(
            "klien.pra-pendaftaran.show",
            compact("praPendaftaranPerkara"),
        );
    }
}
