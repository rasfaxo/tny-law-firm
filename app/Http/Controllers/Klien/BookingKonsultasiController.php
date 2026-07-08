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
        $this->ensureOwnedByKlien(
            $praPendaftaranPerkara,
            $request->user()->id_user,
        );

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

        $query = JadwalKonsultasi::query()
            ->where("status_slot", "tersedia");

        if ($request->filled("tanggal")) {
            $query->whereDate("tanggal", $request->tanggal);
        }

        $jadwalKonsultasi = $query->orderBy("tanggal")
            ->orderBy("waktu_mulai")
            ->paginate(10)
            ->withQueryString();

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
        $this->ensureOwnedByKlien(
            $praPendaftaranPerkara,
            $request->user()->id_user,
        );

        $service->book(
            $praPendaftaranPerkara,
            $request->validated(),
            $request->user()->id_user,
        );

        return redirect()
            ->route("klien.pra-pendaftaran.show", $praPendaftaranPerkara)
            ->with("success", "Jadwal konsultasi berhasil dipilih.");
    }

    public function index(Request $request): View
    {
        $query = \App\Models\BookingKonsultasi::query()
            ->with(["jadwalKonsultasi", "praPendaftaranPerkara.kategori"])
            ->where("id_user", $request->user()->id_user);

        if ($request->filled("search")) {
            $query->whereHas("praPendaftaranPerkara", function ($q) use ($request) {
                $q->where("judul_perkara", "like", "%" . $request->search . "%");
            });
        }

        if ($request->filled("status_booking")) {
            $query->where("status_booking", $request->status_booking);
        }

        $bookingKonsultasi = $query->latest("tanggal_booking")
            ->paginate(10)
            ->withQueryString();

        return view(
            "klien.booking-konsultasi.index",
            compact("bookingKonsultasi"),
        );
    }

    public function show(Request $request, \App\Models\BookingKonsultasi $bookingKonsultasi): View
    {
        abort_unless($bookingKonsultasi->id_user === $request->user()->id_user, 403);

        $bookingKonsultasi->load([
            "jadwalKonsultasi",
            "praPendaftaranPerkara.kategori",
            "permintaanReschedule" => fn($q) => $q->latest("tanggal_pengajuan"),
        ]);

        return view(
            "klien.booking-konsultasi.show",
            compact("bookingKonsultasi"),
        );
    }

    private function ensureOwnedByKlien(
        PraPendaftaranPerkara $praPendaftaranPerkara,
        int $klienId,
    ): void {
        abort_unless($praPendaftaranPerkara->id_user === $klienId, 403);
    }
}
