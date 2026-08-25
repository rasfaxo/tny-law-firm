<?php

namespace App\Http\Controllers;

use App\Models\BookingKonsultasi;
use App\Models\DokumenPerkara;
use App\Models\JadwalKonsultasi;
use App\Models\KategoriPerkara;
use App\Models\PermintaanReschedule;
use App\Models\PraPendaftaranPerkara;
use App\Models\User;
use App\Models\VerifikasiBerkas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        return match ($request->user()->role) {
            "klien" => redirect()->route("klien.dashboard"),
            "admin" => redirect()->route("admin.dashboard"),
            "staf_legal" => redirect()->route("staf-legal.dashboard"),
            default => $this->logoutInvalidRole($request),
        };
    }

    public function klien(Request $request): View
    {
        $userId = $request->user()->id_user;

        $statistics = [
            "Total Pengajuan Saya" => PraPendaftaranPerkara::query()
                ->where("id_user", $userId)
                ->count(),
            "Pengajuan Menunggu Verifikasi" => PraPendaftaranPerkara::query()
                ->where("id_user", $userId)
                ->where("status_pengajuan", "menunggu_verifikasi")
                ->count(),
            "Pengajuan Berkas Lengkap" => PraPendaftaranPerkara::query()
                ->where("id_user", $userId)
                ->where("status_pengajuan", "berkas_lengkap")
                ->count(),
            "Pengajuan Jadwal Dipilih" => PraPendaftaranPerkara::query()
                ->where("id_user", $userId)
                ->where("status_pengajuan", "jadwal_dipilih")
                ->count(),
            "Pengajuan Selesai" => PraPendaftaranPerkara::query()
                ->where("id_user", $userId)
                ->where("status_pengajuan", "selesai")
                ->count(),
            "Booking Aktif" => BookingKonsultasi::query()
                ->where("id_user", $userId)
                ->where("status_booking", "aktif")
                ->count(),
            "Reschedule Menunggu Persetujuan" => PermintaanReschedule::query()
                ->where("id_user", $userId)
                ->where("status_reschedule", "menunggu_persetujuan")
                ->count(),
        ];

        $pengajuanTerbaru = PraPendaftaranPerkara::query()
            ->with("kategori")
            ->where("id_user", $userId)
            ->latest("tanggal_pengajuan")
            ->limit(5)
            ->get();

        $bookingAktif = BookingKonsultasi::query()
            ->with(["jadwalKonsultasi", "praPendaftaranPerkara.kategori"])
            ->where("id_user", $userId)
            ->where("status_booking", "aktif")
            ->latest("tanggal_booking")
            ->limit(5)
            ->get();

        $permintaanRescheduleSaya = PermintaanReschedule::query()
            ->with(["bookingLama.praPendaftaranPerkara.kategori"])
            ->where("id_user", $userId)
            ->latest("tanggal_pengajuan")
            ->limit(5)
            ->get();

        return view(
            "klien.dashboard",
            compact(
                "statistics",
                "pengajuanTerbaru",
                "bookingAktif",
                "permintaanRescheduleSaya",
            ),
        );
    }

    public function admin(): View
    {
        $statistics = [
            "Total Klien" => User::query()->where("role", "klien")->count(),
            "Total Staf Legal" => User::query()
                ->where("role", "staf_legal")
                ->count(),
            "Total Kategori Perkara" => KategoriPerkara::query()->count(),
            "Total Pengajuan" => PraPendaftaranPerkara::query()->count(),
            "Pengajuan Menunggu Verifikasi" => PraPendaftaranPerkara::query()
                ->where("status_pengajuan", "menunggu_verifikasi")
                ->count(),
            "Pengajuan Berkas Lengkap" => PraPendaftaranPerkara::query()
                ->where("status_pengajuan", "berkas_lengkap")
                ->count(),
            "Pengajuan Jadwal Dipilih" => PraPendaftaranPerkara::query()
                ->where("status_pengajuan", "jadwal_dipilih")
                ->count(),
            "Pengajuan Selesai" => PraPendaftaranPerkara::query()
                ->where("status_pengajuan", "selesai")
                ->count(),
            "Booking Aktif" => BookingKonsultasi::query()
                ->where("status_booking", "aktif")
                ->count(),
            "Booking Selesai" => BookingKonsultasi::query()
                ->where("status_booking", "selesai")
                ->count(),
            "Reschedule Menunggu Persetujuan" => PermintaanReschedule::query()
                ->where("status_reschedule", "menunggu_persetujuan")
                ->count(),
            "Jadwal Tersedia" => JadwalKonsultasi::query()
                ->where("status_slot", "tersedia")
                ->count(),
        ];

        $pengajuanTerbaru = PraPendaftaranPerkara::query()
            ->with(["kategori", "klien"])
            ->latest("tanggal_pengajuan")
            ->limit(5)
            ->get();

        $bookingTerbaru = BookingKonsultasi::query()
            ->with([
                "jadwalKonsultasi",
                "klien",
                "praPendaftaranPerkara.kategori",
            ])
            ->latest("tanggal_booking")
            ->limit(5)
            ->get();

        $rescheduleMenunggu = PermintaanReschedule::query()
            ->with(["bookingLama.praPendaftaranPerkara.kategori", "klien"])
            ->where("status_reschedule", "menunggu_persetujuan")
            ->latest("tanggal_pengajuan")
            ->limit(5)
            ->get();

        return view(
            "admin.dashboard",
            compact(
                "statistics",
                "pengajuanTerbaru",
                "bookingTerbaru",
                "rescheduleMenunggu",
            ),
        );
    }

    public function stafLegal(Request $request): View
    {
        $statistics = [
            "Pengajuan Menunggu Verifikasi" => PraPendaftaranPerkara::query()
                ->where("status_pengajuan", "menunggu_verifikasi")
                ->count(),
            "Pengajuan Menunggu Verifikasi Ulang" => PraPendaftaranPerkara::query()
                ->where("status_pengajuan", "menunggu_verifikasi_ulang")
                ->count(),
            "Pengajuan Berkas Lengkap" => PraPendaftaranPerkara::query()
                ->where("status_pengajuan", "berkas_lengkap")
                ->count(),
            "Pengajuan Berkas Tidak Lengkap" => PraPendaftaranPerkara::query()
                ->where("status_pengajuan", "berkas_tidak_lengkap")
                ->count(),
            "Dokumen Terkirim" => DokumenPerkara::query()
                ->where("status_dokumen", "terkirim")
                ->count(),
            "Dokumen Perlu Perbaikan" => DokumenPerkara::query()
                ->where("status_dokumen", "perlu_perbaikan")
                ->count(),
            "Dokumen Valid" => DokumenPerkara::query()
                ->where("status_dokumen", "valid")
                ->count(),
        ];

        $pengajuanPerluVerifikasi = PraPendaftaranPerkara::query()
            ->with(["kategori", "klien"])
            ->whereIn("status_pengajuan", [
                "menunggu_verifikasi",
                "menunggu_verifikasi_ulang",
            ])
            ->latest("tanggal_pengajuan")
            ->limit(5)
            ->get();

        $verifikasiTerakhir = VerifikasiBerkas::query()
            ->with("praPendaftaranPerkara.kategori")
            ->where("id_user", $request->user()->id_user)
            ->latest("tanggal_verifikasi")
            ->limit(5)
            ->get();

        return view(
            "staf-legal.dashboard",
            compact(
                "statistics",
                "pengajuanPerluVerifikasi",
                "verifikasiTerakhir",
            ),
        );
    }

    private function logoutInvalidRole(Request $request): RedirectResponse
    {
        Auth::guard("web")->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route("login")
            ->withErrors([
                "email" => "Role akun tidak valid.",
            ]);
    }
}
