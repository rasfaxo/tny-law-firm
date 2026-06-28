<?php

namespace App\Http\Controllers\Admin;

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
    private array $statusPengajuan = [
        "menunggu_verifikasi",
        "berkas_tidak_lengkap",
        "menunggu_verifikasi_ulang",
        "berkas_lengkap",
        "jadwal_dipilih",
        "selesai",
    ];

    private array $statusVerifikasi = ["berkas_lengkap", "berkas_tidak_lengkap"];

    private array $statusBooking = ["aktif", "dibatalkan", "selesai"];

    private array $metodeKonsultasi = ["online", "offline"];

    private array $statusKonfirmasiKonsultasi = [
        "menunggu_konfirmasi",
        "terkonfirmasi",
    ];

    private array $statusReschedule = [
        "menunggu_persetujuan",
        "disetujui",
        "ditolak",
    ];

    public function index(): View
    {
        return view("admin.laporan.index");
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
            ->with(["praPendaftaranPerkara.kategori", "praPendaftaranPerkara.klien", "stafLegal"])
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
            ->with(["jadwalKonsultasi", "klien", "praPendaftaranPerkara.kategori"])
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

        $kategoriPerkara = $this->kategoriPerkaraOptions();

        return view(
            "admin.laporan.pengajuan-selesai",
            compact("filters", "kategoriPerkara", "laporan"),
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
