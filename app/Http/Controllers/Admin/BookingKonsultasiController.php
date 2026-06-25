<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfirmBookingKonsultasiRequest;
use App\Models\BookingKonsultasi;
use App\Services\KonfirmasiKonsultasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookingKonsultasiController extends Controller
{
    public function index(): View
    {
        $bookingKonsultasi = BookingKonsultasi::query()
            ->with([
                "jadwalKonsultasi",
                "klien",
                "praPendaftaranPerkara.kategori",
            ])
            ->latest("tanggal_booking")
            ->paginate(10);

        return view(
            "admin.booking-konsultasi.index",
            compact("bookingKonsultasi"),
        );
    }

    public function show(BookingKonsultasi $bookingKonsultasi): View
    {
        $bookingKonsultasi->load([
            "adminKonfirmasi",
            "jadwalKonsultasi",
            "klien",
            "praPendaftaranPerkara.kategori",
        ]);

        return view(
            "admin.booking-konsultasi.show",
            compact("bookingKonsultasi"),
        );
    }

    public function confirm(
        ConfirmBookingKonsultasiRequest $request,
        BookingKonsultasi $bookingKonsultasi,
        KonfirmasiKonsultasiService $service,
    ): RedirectResponse {
        $service->confirm(
            $bookingKonsultasi,
            $request->validated(),
            $request->user()->id_user,
        );

        return redirect()
            ->route("admin.booking-konsultasi.show", $bookingKonsultasi)
            ->with("success", "Informasi konsultasi berhasil dikonfirmasi.");
    }
}
