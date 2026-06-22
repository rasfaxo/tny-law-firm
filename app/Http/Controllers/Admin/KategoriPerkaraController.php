<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKategoriPerkaraRequest;
use App\Http\Requests\Admin\UpdateKategoriPerkaraRequest;
use App\Models\KategoriPerkara;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KategoriPerkaraController extends Controller
{
    public function index(): View
    {
        $kategoriPerkara = KategoriPerkara::query()
            ->withCount("praPendaftaranPerkara")
            ->orderBy("nama_kategori")
            ->paginate(10);

        return view("admin.kategori-perkara.index", compact("kategoriPerkara"));
    }

    public function create(): View
    {
        return view("admin.kategori-perkara.create");
    }

    public function store(StoreKategoriPerkaraRequest $request): RedirectResponse
    {
        $kategoriPerkara = KategoriPerkara::create($request->validated());

        return redirect()
            ->route("admin.kategori-perkara.show", $kategoriPerkara)
            ->with("success", "Kategori perkara berhasil dibuat.");
    }

    public function show(KategoriPerkara $kategoriPerkara): View
    {
        $kategoriPerkara->loadCount("praPendaftaranPerkara");

        return view("admin.kategori-perkara.show", compact("kategoriPerkara"));
    }

    public function edit(KategoriPerkara $kategoriPerkara): View
    {
        return view("admin.kategori-perkara.edit", compact("kategoriPerkara"));
    }

    public function update(
        UpdateKategoriPerkaraRequest $request,
        KategoriPerkara $kategoriPerkara,
    ): RedirectResponse {
        $kategoriPerkara->update($request->validated());

        return redirect()
            ->route("admin.kategori-perkara.show", $kategoriPerkara)
            ->with("success", "Kategori perkara berhasil diperbarui.");
    }

    public function destroy(KategoriPerkara $kategoriPerkara): RedirectResponse
    {
        if ($kategoriPerkara->praPendaftaranPerkara()->exists()) {
            return back()->with(
                "error",
                "Kategori perkara tidak dapat dihapus karena sudah digunakan pada pengajuan.",
            );
        }

        $kategoriPerkara->delete();

        return redirect()
            ->route("admin.kategori-perkara.index")
            ->with("success", "Kategori perkara berhasil dihapus.");
    }
}
