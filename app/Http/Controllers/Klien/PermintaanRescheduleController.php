<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use App\Http\Requests\Klien\StorePermintaanRescheduleRequest;
use App\Models\BookingKonsultasi;
use App\Models\PermintaanReschedule;
use App\Services\PermintaanRescheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermintaanRescheduleController extends Controller
{
    public function create(
        Request $request,
        BookingKonsultasi $bookingKonsultasi,
    ): View|RedirectResponse {
        abort_unless($bookingKonsultasi->id_user === $request->user()->id_user, 403);

        $bookingKonsultasi->load([
            "jadwalKonsultasi",
            "praPendaftaranPerkara.kategori",
            "permintaanReschedule" => fn($query) => $query->latest(
                "tanggal_pengajuan",
            ),
        ]);

        $pengajuan = $bookingKonsultasi->praPendaftaranPerkara;
        $permintaanMenunggu = $bookingKonsultasi->permintaanReschedule
            ->firstWhere("status_reschedule", "menunggu_persetujuan");

        if (
            $bookingKonsultasi->status_booking !== "aktif" ||
            $pengajuan?->status_pengajuan !== "jadwal_dipilih" ||
            $permintaanMenunggu
        ) {
            return redirect()
                ->route("klien.pra-pendaftaran.show", $pengajuan)
                ->with(
                    "error",
                    "Permintaan reschedule tidak dapat diajukan untuk booking ini.",
                );
        }

        return view(
            "klien.permintaan-reschedule.create",
            compact("bookingKonsultasi"),
        );
    }

    public function store(
        StorePermintaanRescheduleRequest $request,
        BookingKonsultasi $bookingKonsultasi,
        PermintaanRescheduleService $service,
    ): RedirectResponse {
        abort_unless($bookingKonsultasi->id_user === $request->user()->id_user, 403);

        $permintaanReschedule = $service->createForKlien(
            $bookingKonsultasi,
            $request->validated(),
            $request->user()->id_user,
        );

        return redirect()
            ->route("klien.permintaan-reschedule.show", $permintaanReschedule)
            ->with(
                "success",
                "Permintaan reschedule berhasil diajukan dan menunggu persetujuan Admin.",
            );
    }

    public function show(
        Request $request,
        PermintaanReschedule $permintaanReschedule,
    ): View {
        abort_unless($permintaanReschedule->id_user === $request->user()->id_user, 403);

        $permintaanReschedule->load([
            "bookingLama.jadwalKonsultasi",
            "bookingLama.praPendaftaranPerkara.kategori",
            "jadwalBaru",
            "bookingBaru.jadwalKonsultasi",
        ]);

        return view(
            "klien.permintaan-reschedule.show",
            compact("permintaanReschedule"),
        );
    }
}
