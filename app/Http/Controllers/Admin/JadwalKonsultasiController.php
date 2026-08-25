<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJadwalKonsultasiRequest;
use App\Http\Requests\Admin\UpdateJadwalKonsultasiRequest;
use App\Models\JadwalKonsultasi;
use App\Services\JadwalKonsultasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JadwalKonsultasiController extends Controller
{
    public function index(): View
    {
        $jadwalKonsultasi = JadwalKonsultasi::query()
            ->with("admin")
            ->orderByDesc("tanggal")
            ->orderByDesc("waktu_mulai")
            ->paginate(10);

        return view(
            "admin.jadwal-konsultasi.index",
            compact("jadwalKonsultasi"),
        );
    }

    public function create(): View
    {
        return view("admin.jadwal-konsultasi.create");
    }

    public function store(
        StoreJadwalKonsultasiRequest $request,
        JadwalKonsultasiService $service,
    ): RedirectResponse {
        $jadwalKonsultasi = $service->create(
            $request->validated(),
            $request->user()->id_user,
        );

        return redirect()
            ->route("admin.jadwal-konsultasi.show", $jadwalKonsultasi)
            ->with("success", "Jadwal konsultasi berhasil dibuat.");
    }

    public function show(JadwalKonsultasi $jadwalKonsultasi): View
    {
        $jadwalKonsultasi->load("admin");

        return view(
            "admin.jadwal-konsultasi.show",
            compact("jadwalKonsultasi"),
        );
    }

    public function edit(JadwalKonsultasi $jadwalKonsultasi): View|RedirectResponse
    {
        if ($jadwalKonsultasi->status_slot === "terisi") {
            return redirect()
                ->route("admin.jadwal-konsultasi.show", $jadwalKonsultasi)
                ->with(
                    "error",
                    "Slot jadwal yang sudah terisi tidak dapat diedit pada fase ini.",
                );
        }

        return view(
            "admin.jadwal-konsultasi.edit",
            compact("jadwalKonsultasi"),
        );
    }

    public function update(
        UpdateJadwalKonsultasiRequest $request,
        JadwalKonsultasi $jadwalKonsultasi,
        JadwalKonsultasiService $service,
    ): RedirectResponse {
        $service->update($jadwalKonsultasi, $request->validated());

        return redirect()
            ->route("admin.jadwal-konsultasi.show", $jadwalKonsultasi)
            ->with("success", "Jadwal konsultasi berhasil diperbarui.");
    }

    public function updateStatus(
        Request $request,
        JadwalKonsultasi $jadwalKonsultasi,
        JadwalKonsultasiService $service,
    ): RedirectResponse {
        $validated = $request->validate([
            "status_slot" => [
                "required",
                Rule::in(["tersedia", "tidak_aktif"]),
            ],
        ]);

        $service->updateStatus($jadwalKonsultasi, $validated["status_slot"]);

        return back()->with("success", "Status slot jadwal berhasil diperbarui.");
    }
}
