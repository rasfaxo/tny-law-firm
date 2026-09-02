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

        // Consolidate 5 PraPendaftaranPerkara counts into 1 query
        $pengajuanCounts = PraPendaftaranPerkara::query()
            ->where("id_user", $userId)
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(status_pengajuan = 'menunggu_verifikasi') as menunggu_verifikasi")
            ->selectRaw("SUM(status_pengajuan = 'berkas_lengkap') as berkas_lengkap")
            ->selectRaw("SUM(status_pengajuan = 'jadwal_dipilih') as jadwal_dipilih")
            ->selectRaw("SUM(status_pengajuan = 'selesai') as selesai")
            ->first();

        $statistics = [
            "Total Pengajuan Saya" => (int) $pengajuanCounts->total,
            "Pengajuan Menunggu Verifikasi" => (int) $pengajuanCounts->menunggu_verifikasi,
            "Pengajuan Berkas Lengkap" => (int) $pengajuanCounts->berkas_lengkap,
            "Pengajuan Jadwal Dipilih" => (int) $pengajuanCounts->jadwal_dipilih,
            "Pengajuan Selesai" => (int) $pengajuanCounts->selesai,
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
        // Consolidate User counts (2 → 1 query)
        $userCounts = User::query()
            ->whereIn("role", ["klien", "staf_legal"])
            ->selectRaw("SUM(role = 'klien') as klien")
            ->selectRaw("SUM(role = 'staf_legal') as staf_legal")
            ->first();

        // Consolidate PraPendaftaranPerkara counts (5 → 1 query)
        $pengajuanCounts = PraPendaftaranPerkara::query()
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(status_pengajuan = 'menunggu_verifikasi') as menunggu_verifikasi")
            ->selectRaw("SUM(status_pengajuan = 'berkas_lengkap') as berkas_lengkap")
            ->selectRaw("SUM(status_pengajuan = 'jadwal_dipilih') as jadwal_dipilih")
            ->selectRaw("SUM(status_pengajuan = 'selesai') as selesai")
            ->first();

        // Consolidate BookingKonsultasi counts (2 → 1 query)
        $bookingCounts = BookingKonsultasi::query()
            ->selectRaw("SUM(status_booking = 'aktif') as aktif")
            ->selectRaw("SUM(status_booking = 'selesai') as selesai")
            ->first();

        $statistics = [
            "Total Klien" => (int) $userCounts->klien,
            "Total Staf Legal" => (int) $userCounts->staf_legal,
            "Total Kategori Perkara" => KategoriPerkara::query()->count(),
            "Total Pengajuan" => (int) $pengajuanCounts->total,
            "Pengajuan Menunggu Verifikasi" => (int) $pengajuanCounts->menunggu_verifikasi,
            "Pengajuan Berkas Lengkap" => (int) $pengajuanCounts->berkas_lengkap,
            "Pengajuan Jadwal Dipilih" => (int) $pengajuanCounts->jadwal_dipilih,
            "Pengajuan Selesai" => (int) $pengajuanCounts->selesai,
            "Booking Aktif" => (int) $bookingCounts->aktif,
            "Booking Selesai" => (int) $bookingCounts->selesai,
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
        // Consolidate 4 PraPendaftaranPerkara counts into 1 query
        $pengajuanCounts = PraPendaftaranPerkara::query()
            ->selectRaw("SUM(status_pengajuan = 'menunggu_verifikasi') as menunggu_verifikasi")
            ->selectRaw("SUM(status_pengajuan = 'menunggu_verifikasi_ulang') as menunggu_verifikasi_ulang")
            ->selectRaw("SUM(status_pengajuan = 'berkas_lengkap') as berkas_lengkap")
            ->selectRaw("SUM(status_pengajuan = 'berkas_tidak_lengkap') as berkas_tidak_lengkap")
            ->first();

        // Consolidate 3 DokumenPerkara counts into 1 query
        $dokumenCounts = DokumenPerkara::query()
            ->selectRaw("SUM(status_dokumen = 'terkirim') as terkirim")
            ->selectRaw("SUM(status_dokumen = 'perlu_perbaikan') as perlu_perbaikan")
            ->selectRaw("SUM(status_dokumen = 'valid') as valid")
            ->first();

        $statistics = [
            "Pengajuan Menunggu Verifikasi" => (int) $pengajuanCounts->menunggu_verifikasi,
            "Pengajuan Menunggu Verifikasi Ulang" => (int) $pengajuanCounts->menunggu_verifikasi_ulang,
            "Pengajuan Berkas Lengkap" => (int) $pengajuanCounts->berkas_lengkap,
            "Pengajuan Berkas Tidak Lengkap" => (int) $pengajuanCounts->berkas_tidak_lengkap,
            "Dokumen Terkirim" => (int) $dokumenCounts->terkirim,
            "Dokumen Perlu Perbaikan" => (int) $dokumenCounts->perlu_perbaikan,
            "Dokumen Valid" => (int) $dokumenCounts->valid,
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
