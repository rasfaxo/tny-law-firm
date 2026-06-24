<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use App\Http\Requests\Klien\StoreBookingKonsultasiRequest;
use App\Models\JadwalKonsultasi;
use App\Models\PraPendaftaranPerkara;
use App\Services\BookingKonsultasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingKonsultasiController extends Controller
{
    public function create(
        Request $request,
        PraPendaftaranPerkara $praPendaftaranPerkara,
    ): View|RedirectResponse {
        $this->ensureOwnedByKlien($praPendaftaranPerkara, $request->user()->id_user);

        $praPendaftaranPerkara->load("bookingAktif.jadwalKonsultasi");

        if ($praPendaftaranPerkara->status_pengajuan !== "berkas_lengkap") {
            return redirect()
                ->route("klien.pra-pendaftaran.show", $praPendaftaranPerkara)
                ->with(
                    "error",
                    "Jadwal konsultasi hanya dapat dipilih setelah berkas dinyatakan lengkap.",
                );
        }

        if ($praPendaftaranPerkara->bookingAktif !== null) {
            return redirect()
                ->route("klien.pra-pendaftaran.show", $praPendaftaranPerkara)
                ->with("error", "Pengajuan ini sudah memiliki booking aktif.");
        }

        $jadwalKonsultasi = JadwalKonsultasi::query()
            ->where("status_slot", "tersedia")
            ->orderBy("tanggal")
            ->orderBy("waktu_mulai")
            ->paginate(10);

        return view(
            "klien.booking-konsultasi.create",
            compact("praPendaftaranPerkara", "jadwalKonsultasi"),
        );
    }

    public function store(
        StoreBookingKonsultasiRequest $request,
        PraPendaftaranPerkara $praPendaftaranPerkara,
        BookingKonsultasiService $service,
    ): RedirectResponse {
        $this->ensureOwnedByKlien($praPendaftaranPerkara, $request->user()->id_user);

        $service->book(
            $praPendaftaranPerkara,
            (int) $request->validated("id_jadwal"),
            $request->user()->id_user,
        );

        return redirect()
            ->route("klien.pra-pendaftaran.show", $praPendaftaranPerkara)
            ->with("success", "Jadwal konsultasi berhasil dipilih.");
    }

    private function ensureOwnedByKlien(
        PraPendaftaranPerkara $praPendaftaranPerkara,
        int $klienId,
    ): void {
        abort_unless($praPendaftaranPerkara->id_user === $klienId, 403);
    }
}
