<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApprovePermintaanRescheduleRequest;
use App\Http\Requests\Admin\RejectPermintaanRescheduleRequest;
use App\Models\JadwalKonsultasi;
use App\Models\PermintaanReschedule;
use App\Services\PermintaanRescheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PermintaanRescheduleController extends Controller
{
    public function index(): View
    {
        $permintaanReschedule = PermintaanReschedule::query()
            ->with([
                "bookingLama.jadwalKonsultasi",
                "bookingLama.praPendaftaranPerkara.kategori",
                "klien",
            ])
            ->latest("tanggal_pengajuan")
            ->paginate(10);

        return view(
            "admin.permintaan-reschedule.index",
            compact("permintaanReschedule"),
        );
    }

    public function show(PermintaanReschedule $permintaanReschedule): View
    {
        $permintaanReschedule->load([
            "bookingBaru.jadwalKonsultasi",
            "bookingLama.jadwalKonsultasi",
            "bookingLama.praPendaftaranPerkara.kategori",
            "jadwalBaru",
            "klien",
        ]);

        $jadwalTersedia = JadwalKonsultasi::query()
            ->where("status_slot", "tersedia")
            ->orderBy("tanggal")
            ->orderBy("waktu_mulai")
            ->get();

        return view(
            "admin.permintaan-reschedule.show",
            compact("permintaanReschedule", "jadwalTersedia"),
        );
    }

    public function approve(
        ApprovePermintaanRescheduleRequest $request,
        PermintaanReschedule $permintaanReschedule,
        PermintaanRescheduleService $service,
    ): RedirectResponse {
        $service->approve(
            $permintaanReschedule,
            $request->validated(),
            $request->user()->id_user,
        );

        return redirect()
            ->route("admin.permintaan-reschedule.show", $permintaanReschedule)
            ->with("success", "Permintaan reschedule berhasil disetujui.");
    }

    public function reject(
        RejectPermintaanRescheduleRequest $request,
        PermintaanReschedule $permintaanReschedule,
        PermintaanRescheduleService $service,
    ): RedirectResponse {
        $service->reject($permintaanReschedule, $request->validated());

        return redirect()
            ->route("admin.permintaan-reschedule.show", $permintaanReschedule)
            ->with("success", "Permintaan reschedule berhasil ditolak.");
    }
}
