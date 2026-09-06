<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexFilterRequest;
use App\Http\Requests\Klien\StoreBookingKonsultasiRequest;
use App\Models\BookingKonsultasi;
use App\Models\JadwalKonsultasi;
use App\Models\PraPendaftaranPerkara;
use App\Services\BookingKonsultasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingKonsultasiController extends Controller
{
    public function create(
        IndexFilterRequest $request,
        PraPendaftaranPerkara $praPendaftaranPerkara,
    ): View|RedirectResponse {
        $this->authorize('view', $praPendaftaranPerkara);

        $praPendaftaranPerkara->load('bookingAktif.jadwalKonsultasi');

        if ($praPendaftaranPerkara->status_pengajuan !== 'berkas_lengkap') {
            return redirect()
                ->route('klien.pra-pendaftaran.show', $praPendaftaranPerkara)
                ->with(
                    'error',
                    'Jadwal konsultasi hanya dapat dipilih setelah berkas dinyatakan lengkap.',
                );
        }

        if ($praPendaftaranPerkara->bookingAktif !== null) {
            return redirect()
                ->route('klien.pra-pendaftaran.show', $praPendaftaranPerkara)
                ->with('error', 'Pengajuan ini sudah memiliki booking aktif.');
        }

        $query = JadwalKonsultasi::query()
            ->where('status_slot', 'tersedia');

        $filters = $request->validated();
        if (isset($filters['tanggal'])) {
            $query->whereDate('tanggal', $filters['tanggal']);
        }

        $jadwalKonsultasi = $query->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->paginate(10)
            ->withQueryString();

        return view(
            'klien.booking-konsultasi.create',
            compact('praPendaftaranPerkara', 'jadwalKonsultasi'),
        );
    }

    public function store(
        StoreBookingKonsultasiRequest $request,
        PraPendaftaranPerkara $praPendaftaranPerkara,
        BookingKonsultasiService $service,
    ): RedirectResponse {
        $this->authorize('view', $praPendaftaranPerkara);

        $service->book(
            $praPendaftaranPerkara,
            $request->validated(),
            $request->user()->id_user,
        );

        return redirect()
            ->route('klien.pra-pendaftaran.show', $praPendaftaranPerkara)
            ->with('success', 'Jadwal konsultasi berhasil dipilih.');
    }

    public function index(IndexFilterRequest $request): View
    {
        $filters = $request->validated();
        $query = BookingKonsultasi::query()
            ->with(['jadwalKonsultasi', 'praPendaftaranPerkara.kategori'])
            ->where('id_user', $request->user()->id_user);

        if (isset($filters['search'])) {
            $query->whereHas('praPendaftaranPerkara', function ($q) use ($filters) {
                $q->where('judul_perkara', 'like', '%'.$filters['search'].'%');
            });
        }

        if (isset($filters['status_booking'])) {
            $query->where('status_booking', $filters['status_booking']);
        }

        $bookingKonsultasi = $query->latest('tanggal_booking')
            ->paginate(10)
            ->withQueryString();

        return view(
            'klien.booking-konsultasi.index',
            compact('bookingKonsultasi'),
        );
    }

    public function show(Request $request, BookingKonsultasi $bookingKonsultasi): View
    {
        $this->authorize('view', $bookingKonsultasi);

        $bookingKonsultasi->load([
            'jadwalKonsultasi',
            'praPendaftaranPerkara.kategori',
            'permintaanReschedule' => fn ($q) => $q->latest('tanggal_pengajuan'),
        ]);

        return view(
            'klien.booking-konsultasi.show',
            compact('bookingKonsultasi'),
        );
    }
}
