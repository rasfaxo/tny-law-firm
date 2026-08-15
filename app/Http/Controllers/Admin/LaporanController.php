<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusBooking;
use App\Enums\StatusKonfirmasi;
use App\Enums\StatusPengajuan;
use App\Enums\StatusReschedule;
use App\Http\Controllers\Controller;
use App\Models\BookingKonsultasi;
use App\Models\KategoriPerkara;
use App\Models\PermintaanReschedule;
use App\Models\PraPendaftaranPerkara;
use App\Models\User;
use App\Models\VerifikasiBerkas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LaporanController extends Controller
{
    private array $statusPengajuan;

    private array $statusVerifikasi = [
        StatusPengajuan::BerkasLengkap->value,
        StatusPengajuan::BerkasTidakLengkap->value,
    ];

    private array $statusBooking;

    private array $metodeKonsultasi = ["online", "offline"];

    private array $statusKonfirmasiKonsultasi;

    private array $statusReschedule;

    public function __construct()
    {
        $this->statusPengajuan = StatusPengajuan::values();
        $this->statusBooking = StatusBooking::values();
        $this->statusKonfirmasiKonsultasi = StatusKonfirmasi::values();
        $this->statusReschedule = StatusReschedule::values();
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            "tanggal_mulai" => ["nullable", "date"],
            "tanggal_selesai" => [
                "nullable",
                "date",
                "after_or_equal:tanggal_mulai",
            ],
        ]);

        $totalPengajuan = PraPendaftaranPerkara::query()
            ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
            ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d))
            ->count();

        $pengajuanSelesai = PraPendaftaranPerkara::query()
            ->where("status_pengajuan", "selesai")
            ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
            ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d))
            ->count();

        $berkasLengkap = PraPendaftaranPerkara::query()
            ->where("status_pengajuan", "berkas_lengkap")
            ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
            ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d))
            ->count();

        $bookingSelesai = BookingKonsultasi::query()
            ->where("status_booking", "selesai")
            ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_booking", ">=", $d))
            ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_booking", "<=", $d))
            ->count();

        $kategoriSummary = KategoriPerkara::query()
            ->withCount([
                "praPendaftaranPerkara as total_count" => function ($query) use ($filters) {
                    $query->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
                        ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d));
                },
                "praPendaftaranPerkara as berkas_lengkap_count" => function ($query) use ($filters) {
                    $query->where("status_pengajuan", "berkas_lengkap")
                        ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
                        ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d));
                },
                "praPendaftaranPerkara as jadwal_dipilih_count" => function ($query) use ($filters) {
                    $query->where("status_pengajuan", "jadwal_dipilih")
                        ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
                        ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d));
                },
                "praPendaftaranPerkara as selesai_count" => function ($query) use ($filters) {
                    $query->where("status_pengajuan", "selesai")
                        ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
                        ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d));
                }
            ])
            ->orderBy("nama_kategori")
            ->get();

        return view(
            "admin.laporan.index",
            compact(
                "filters",
                "totalPengajuan",
                "pengajuanSelesai",
                "berkasLengkap",
                "bookingSelesai",
                "kategoriSummary"
            )
        );
    }

    public function praPendaftaran(Request $request): View
    {
        $filters = $request->validate([
            "tanggal_mulai" => ["nullable", "date"],
            "tanggal_selesai" => [
                "nullable",
                "date",
                "after_or_equal:tanggal_mulai",
            ],
            "status_pengajuan" => [
                "nullable",
                "string",
                Rule::in($this->statusPengajuan),
            ],
            "id_kategori" => [
                "nullable",
                "integer",
                "exists:kategori_perkara,id_kategori",
            ],
        ]);

        $laporan = PraPendaftaranPerkara::query()
            ->with(["kategori", "klien"])
            ->when($filters["tanggal_mulai"] ?? null, function (
                $query,
                $tanggal,
            ) {
                $query->whereDate("tanggal_pengajuan", ">=", $tanggal);
            })
            ->when($filters["tanggal_selesai"] ?? null, function (
                $query,
                $tanggal,
            ) {
                $query->whereDate("tanggal_pengajuan", "<=", $tanggal);
            })
            ->when($filters["status_pengajuan"] ?? null, function (
                $query,
                $status,
            ) {
                $query->where("status_pengajuan", $status);
            })
            ->when($filters["id_kategori"] ?? null, function (
                $query,
                $kategoriId,
            ) {
                $query->where("id_kategori", $kategoriId);
            })
            ->latest("tanggal_pengajuan")
            ->get();

        $kategoriPerkara = $this->kategoriPerkaraOptions();

        return view(
            "admin.laporan.pra-pendaftaran",
            compact("filters", "kategoriPerkara", "laporan"),
        );
    }

    public function verifikasiBerkas(Request $request): View
    {
        $filters = $request->validate([
            "tanggal_mulai" => ["nullable", "date"],
            "tanggal_selesai" => [
                "nullable",
                "date",
                "after_or_equal:tanggal_mulai",
            ],
            "status_verifikasi" => [
                "nullable",
                "string",
                Rule::in($this->statusVerifikasi),
            ],
            "id_staf_legal" => [
                "nullable",
                "integer",
                Rule::exists("users", "id_user")->where("role", "staf_legal"),
            ],
        ]);

        $laporan = VerifikasiBerkas::query()
            ->with([
                "praPendaftaranPerkara.kategori",
                "praPendaftaranPerkara.klien",
                "stafLegal",
            ])
            ->when($filters["tanggal_mulai"] ?? null, function (
                $query,
                $tanggal,
            ) {
                $query->whereDate("tanggal_verifikasi", ">=", $tanggal);
            })
            ->when($filters["tanggal_selesai"] ?? null, function (
                $query,
                $tanggal,
            ) {
                $query->whereDate("tanggal_verifikasi", "<=", $tanggal);
            })
            ->when($filters["status_verifikasi"] ?? null, function (
                $query,
                $status,
            ) {
                $query->where("status_verifikasi", $status);
            })
            ->when($filters["id_staf_legal"] ?? null, function (
                $query,
                $stafId,
            ) {
                $query->where("id_user", $stafId);
            })
            ->latest("tanggal_verifikasi")
            ->get();

        $stafLegal = $this->stafLegalOptions();

        return view(
            "admin.laporan.verifikasi-berkas",
            compact("filters", "laporan", "stafLegal"),
        );
    }

    public function bookingKonsultasi(Request $request): View
    {
        $filters = $request->validate([
            "tanggal_mulai" => ["nullable", "date"],
            "tanggal_selesai" => [
                "nullable",
                "date",
                "after_or_equal:tanggal_mulai",
            ],
            "status_booking" => [
                "nullable",
                "string",
                Rule::in($this->statusBooking),
            ],
            "metode_konsultasi" => [
                "nullable",
                "string",
                Rule::in($this->metodeKonsultasi),
            ],
            "status_konfirmasi_konsultasi" => [
                "nullable",
                "string",
                Rule::in($this->statusKonfirmasiKonsultasi),
            ],
        ]);

        $laporan = BookingKonsultasi::query()
            ->with([
                "jadwalKonsultasi",
                "klien",
                "praPendaftaranPerkara.kategori",
            ])
            ->when($filters["tanggal_mulai"] ?? null, function (
                $query,
                $tanggal,
            ) {
                $query->whereDate("tanggal_booking", ">=", $tanggal);
            })
            ->when($filters["tanggal_selesai"] ?? null, function (
                $query,
                $tanggal,
            ) {
                $query->whereDate("tanggal_booking", "<=", $tanggal);
            })
            ->when($filters["status_booking"] ?? null, function (
                $query,
                $status,
            ) {
                $query->where("status_booking", $status);
            })
            ->when($filters["metode_konsultasi"] ?? null, function (
                $query,
                $metode,
            ) {
                $query->where("metode_konsultasi", $metode);
            })
            ->when($filters["status_konfirmasi_konsultasi"] ?? null, function (
                $query,
                $status,
            ) {
                $query->where("status_konfirmasi_konsultasi", $status);
            })
            ->latest("tanggal_booking")
            ->get();

        return view(
            "admin.laporan.booking-konsultasi",
            compact("filters", "laporan"),
        );
    }

    public function rescheduleKonsultasi(Request $request): View
    {
        $filters = $request->validate([
            "tanggal_mulai" => ["nullable", "date"],
            "tanggal_selesai" => [
                "nullable",
                "date",
                "after_or_equal:tanggal_mulai",
            ],
            "status_reschedule" => [
                "nullable",
                "string",
                Rule::in($this->statusReschedule),
            ],
            "preferensi_metode" => [
                "nullable",
                "string",
                Rule::in($this->metodeKonsultasi),
            ],
        ]);

        $laporan = PermintaanReschedule::query()
            ->with([
                "bookingBaru.jadwalKonsultasi",
                "bookingLama.jadwalKonsultasi",
                "bookingLama.praPendaftaranPerkara.kategori",
                "klien",
            ])
            ->when($filters["tanggal_mulai"] ?? null, function (
                $query,
                $tanggal,
            ) {
                $query->whereDate("tanggal_pengajuan", ">=", $tanggal);
            })
            ->when($filters["tanggal_selesai"] ?? null, function (
                $query,
                $tanggal,
            ) {
                $query->whereDate("tanggal_pengajuan", "<=", $tanggal);
            })
            ->when($filters["status_reschedule"] ?? null, function (
                $query,
                $status,
            ) {
                $query->where("status_reschedule", $status);
            })
            ->when($filters["preferensi_metode"] ?? null, function (
                $query,
                $metode,
            ) {
                $query->where("preferensi_metode", $metode);
            })
            ->latest("tanggal_pengajuan")
            ->get();

        return view(
            "admin.laporan.reschedule-konsultasi",
            compact("filters", "laporan"),
        );
    }

    public function pengajuanSelesai(Request $request): View
    {
        $filters = $request->validate([
            "tanggal_mulai" => ["nullable", "date"],
            "tanggal_selesai" => [
                "nullable",
                "date",
                "after_or_equal:tanggal_mulai",
            ],
            "id_kategori" => [
                "nullable",
                "integer",
                "exists:kategori_perkara,id_kategori",
            ],
        ]);

        $laporan = PraPendaftaranPerkara::query()
            ->with([
                "kategori",
                "klien",
                "riwayatStatus" => fn($query) => $query
                    ->where("status", "selesai")
                    ->latest(),
            ])
            ->where("status_pengajuan", "selesai")
            ->when($filters["tanggal_mulai"] ?? null, function (
                $query,
                $tanggal,
            ) {
                $query->whereHas("riwayatStatus", function ($query) use (
                    $tanggal,
                ) {
                    $query
                        ->where("status", "selesai")
                        ->whereDate("created_at", ">=", $tanggal);
                });
            })
            ->when($filters["tanggal_selesai"] ?? null, function (
                $query,
                $tanggal,
            ) {
                $query->whereHas("riwayatStatus", function ($query) use (
                    $tanggal,
                ) {
                    $query
                        ->where("status", "selesai")
                        ->whereDate("created_at", "<=", $tanggal);
                });
            })
            ->when($filters["id_kategori"] ?? null, function (
                $query,
                $kategoriId,
            ) {
                $query->where("id_kategori", $kategoriId);
            })
            ->latest("tanggal_pengajuan")
            ->get();

        $kategoriPerkara = $this->kategoriPerkaraOptions();

        return view(
            "admin.laporan.pengajuan-selesai",
            compact("filters", "kategoriPerkara", "laporan"),
        );
    }

    public function cetakIndex(Request $request): View
    {
        $filters = $request->validate([
            "tanggal_mulai" => ["nullable", "date"],
            "tanggal_selesai" => [
                "nullable",
                "date",
                "after_or_equal:tanggal_mulai",
            ],
        ]);

        $totalPengajuan = PraPendaftaranPerkara::query()
            ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
            ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d))
            ->count();

        $pengajuanSelesai = PraPendaftaranPerkara::query()
            ->where("status_pengajuan", "selesai")
            ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
            ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d))
            ->count();

        $berkasLengkap = PraPendaftaranPerkara::query()
            ->where("status_pengajuan", "berkas_lengkap")
            ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
            ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d))
            ->count();

        $bookingSelesai = BookingKonsultasi::query()
            ->where("status_booking", "selesai")
            ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_booking", ">=", $d))
            ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_booking", "<=", $d))
            ->count();

        $kategoriSummary = KategoriPerkara::query()
            ->withCount([
                "praPendaftaranPerkara as total_count" => function ($query) use ($filters) {
                    $query->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
                        ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d));
                },
                "praPendaftaranPerkara as berkas_lengkap_count" => function ($query) use ($filters) {
                    $query->where("status_pengajuan", "berkas_lengkap")
                        ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
                        ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d));
                },
                "praPendaftaranPerkara as jadwal_dipilih_count" => function ($query) use ($filters) {
                    $query->where("status_pengajuan", "jadwal_dipilih")
                        ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
                        ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d));
                },
                "praPendaftaranPerkara as selesai_count" => function ($query) use ($filters) {
                    $query->where("status_pengajuan", "selesai")
                        ->when($filters["tanggal_mulai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", ">=", $d))
                        ->when($filters["tanggal_selesai"] ?? null, fn($q, $d) => $q->whereDate("tanggal_pengajuan", "<=", $d));
                }
            ])
            ->orderBy("nama_kategori")
            ->get();

        return view(
            "admin.laporan.cetak.index",
            compact(
                "filters",
                "totalPengajuan",
                "pengajuanSelesai",
                "berkasLengkap",
                "bookingSelesai",
                "kategoriSummary"
            )
        );
    }

    public function cetakPraPendaftaran(Request $request): View
    {
        $filters = $request->validate([
            "tanggal_mulai" => ["nullable", "date"],
            "tanggal_selesai" => [
                "nullable",
                "date",
                "after_or_equal:tanggal_mulai",
            ],
            "status_pengajuan" => [
                "nullable",
                "string",
                Rule::in($this->statusPengajuan),
            ],
            "id_kategori" => [
                "nullable",
                "integer",
                "exists:kategori_perkara,id_kategori",
            ],
        ]);

        $laporan = PraPendaftaranPerkara::query()
            ->with(["kategori", "klien"])
            ->when($filters["tanggal_mulai"] ?? null, function ($query, $tanggal) {
                $query->whereDate("tanggal_pengajuan", ">=", $tanggal);
            })
            ->when($filters["tanggal_selesai"] ?? null, function ($query, $tanggal) {
                $query->whereDate("tanggal_pengajuan", "<=", $tanggal);
            })
            ->when($filters["status_pengajuan"] ?? null, function ($query, $status) {
                $query->where("status_pengajuan", $status);
            })
            ->when($filters["id_kategori"] ?? null, function ($query, $kategoriId) {
                $query->where("id_kategori", $kategoriId);
            })
            ->latest("tanggal_pengajuan")
            ->get();

        $kategoriPerkara = $this->kategoriPerkaraOptions();
        $selectedKategori = ($filters["id_kategori"] ?? null) 
            ? KategoriPerkara::find($filters["id_kategori"]) 
            : null;

        return view(
            "admin.laporan.cetak.pra-pendaftaran",
            compact("filters", "kategoriPerkara", "selectedKategori", "laporan")
        );
    }

    public function cetakVerifikasiBerkas(Request $request): View
    {
        $filters = $request->validate([
            "tanggal_mulai" => ["nullable", "date"],
            "tanggal_selesai" => [
                "nullable",
                "date",
                "after_or_equal:tanggal_mulai",
            ],
            "status_verifikasi" => [
                "nullable",
                "string",
                Rule::in($this->statusVerifikasi),
            ],
            "id_staf_legal" => [
                "nullable",
                "integer",
                Rule::exists("users", "id_user")->where("role", "staf_legal"),
            ],
        ]);

        $laporan = VerifikasiBerkas::query()
            ->with([
                "praPendaftaranPerkara.kategori",
                "praPendaftaranPerkara.klien",
                "stafLegal",
            ])
            ->when($filters["tanggal_mulai"] ?? null, function ($query, $tanggal) {
                $query->whereDate("tanggal_verifikasi", ">=", $tanggal);
            })
            ->when($filters["tanggal_selesai"] ?? null, function ($query, $tanggal) {
                $query->whereDate("tanggal_verifikasi", "<=", $tanggal);
            })
            ->when($filters["status_verifikasi"] ?? null, function ($query, $status) {
                $query->where("status_verifikasi", $status);
            })
            ->when($filters["id_staf_legal"] ?? null, function ($query, $stafId) {
                $query->where("id_user", $stafId);
            })
            ->latest("tanggal_verifikasi")
            ->get();

        $selectedStafLegal = ($filters["id_staf_legal"] ?? null) 
            ? User::find($filters["id_staf_legal"]) 
            : null;

        return view(
            "admin.laporan.cetak.verifikasi-berkas",
            compact("filters", "laporan", "selectedStafLegal")
        );
    }

    public function cetakBookingKonsultasi(Request $request): View
    {
        $filters = $request->validate([
            "tanggal_mulai" => ["nullable", "date"],
            "tanggal_selesai" => [
                "nullable",
                "date",
                "after_or_equal:tanggal_mulai",
            ],
            "status_booking" => [
                "nullable",
                "string",
                Rule::in($this->statusBooking),
            ],
            "metode_konsultasi" => [
                "nullable",
                "string",
                Rule::in($this->metodeKonsultasi),
            ],
            "status_konfirmasi_konsultasi" => [
                "nullable",
                "string",
                Rule::in($this->statusKonfirmasiKonsultasi),
            ],
        ]);

        $laporan = BookingKonsultasi::query()
            ->with([
                "jadwalKonsultasi",
                "klien",
                "praPendaftaranPerkara.kategori",
            ])
            ->when($filters["tanggal_mulai"] ?? null, function ($query, $tanggal) {
                $query->whereDate("tanggal_booking", ">=", $tanggal);
            })
            ->when($filters["tanggal_selesai"] ?? null, function ($query, $tanggal) {
                $query->whereDate("tanggal_booking", "<=", $tanggal);
            })
            ->when($filters["status_booking"] ?? null, function ($query, $status) {
                $query->where("status_booking", $status);
            })
            ->when($filters["metode_konsultasi"] ?? null, function ($query, $metode) {
                $query->where("metode_konsultasi", $metode);
            })
            ->when($filters["status_konfirmasi_konsultasi"] ?? null, function ($query, $status) {
                $query->where("status_konfirmasi_konsultasi", $status);
            })
            ->latest("tanggal_booking")
            ->get();

        return view(
            "admin.laporan.cetak.booking-konsultasi",
            compact("filters", "laporan")
        );
    }

    public function cetakRescheduleKonsultasi(Request $request): View
    {
        $filters = $request->validate([
            "tanggal_mulai" => ["nullable", "date"],
            "tanggal_selesai" => [
                "nullable",
                "date",
                "after_or_equal:tanggal_mulai",
            ],
            "status_reschedule" => [
                "nullable",
                "string",
                Rule::in($this->statusReschedule),
            ],
            "preferensi_metode" => [
                "nullable",
                "string",
                Rule::in($this->metodeKonsultasi),
            ],
        ]);

        $laporan = PermintaanReschedule::query()
            ->with([
                "bookingBaru.jadwalKonsultasi",
                "bookingLama.jadwalKonsultasi",
                "bookingLama.praPendaftaranPerkara.kategori",
                "klien",
            ])
            ->when($filters["tanggal_mulai"] ?? null, function ($query, $tanggal) {
                $query->whereDate("tanggal_pengajuan", ">=", $tanggal);
            })
            ->when($filters["tanggal_selesai"] ?? null, function ($query, $tanggal) {
                $query->whereDate("tanggal_pengajuan", "<=", $tanggal);
            })
            ->when($filters["status_reschedule"] ?? null, function ($query, $status) {
                $query->where("status_reschedule", $status);
            })
            ->when($filters["preferensi_metode"] ?? null, function ($query, $metode) {
                $query->where("preferensi_metode", $metode);
            })
            ->latest("tanggal_pengajuan")
            ->get();

        return view(
            "admin.laporan.cetak.reschedule-konsultasi",
            compact("filters", "laporan")
        );
    }

    public function cetakPengajuanSelesai(Request $request): View
    {
        $filters = $request->validate([
            "tanggal_mulai" => ["nullable", "date"],
            "tanggal_selesai" => [
                "nullable",
                "date",
                "after_or_equal:tanggal_mulai",
            ],
            "id_kategori" => [
                "nullable",
                "integer",
                "exists:kategori_perkara,id_kategori",
            ],
        ]);

        $laporan = PraPendaftaranPerkara::query()
            ->with([
                "kategori",
                "klien",
                "riwayatStatus" => fn($query) => $query
                    ->where("status", "selesai")
                    ->latest(),
            ])
            ->where("status_pengajuan", "selesai")
            ->when($filters["tanggal_mulai"] ?? null, function ($query, $tanggal) {
                $query->whereHas("riwayatStatus", function ($query) use ($tanggal) {
                    $query
                        ->where("status", "selesai")
                        ->whereDate("created_at", ">=", $tanggal);
                });
            })
            ->when($filters["tanggal_selesai"] ?? null, function ($query, $tanggal) {
                $query->whereHas("riwayatStatus", function ($query) use ($tanggal) {
                    $query
                        ->where("status", "selesai")
                        ->whereDate("created_at", "<=", $tanggal);
                });
            })
            ->when($filters["id_kategori"] ?? null, function ($query, $kategoriId) {
                $query->where("id_kategori", $kategoriId);
            })
            ->latest("tanggal_pengajuan")
            ->get();

        $selectedKategori = ($filters["id_kategori"] ?? null) 
            ? KategoriPerkara::find($filters["id_kategori"]) 
            : null;

        return view(
            "admin.laporan.cetak.pengajuan-selesai",
            compact("filters", "selectedKategori", "laporan")
        );
    }

    private function kategoriPerkaraOptions()
    {
        return KategoriPerkara::query()->orderBy("nama_kategori")->get();
    }

    private function stafLegalOptions()
    {
        return User::query()
            ->where("role", "staf_legal")
            ->orderBy("nama")
            ->get();
    }
}
