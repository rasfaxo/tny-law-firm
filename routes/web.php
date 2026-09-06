<?php

use App\Http\Controllers\Admin\BookingKonsultasiController as AdminBookingKonsultasiController;
use App\Http\Controllers\Admin\JadwalKonsultasiController;
use App\Http\Controllers\Admin\KategoriPerkaraController;
use App\Http\Controllers\Admin\KlienController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PermintaanRescheduleController as AdminPermintaanRescheduleController;
use App\Http\Controllers\Admin\PraPendaftaranController as AdminPraPendaftaranController;
use App\Http\Controllers\Admin\StafLegalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Klien\BookingKonsultasiController;
use App\Http\Controllers\Klien\DokumenPerkaraController;
use App\Http\Controllers\Klien\PerbaikanDokumenController;
use App\Http\Controllers\Klien\PermintaanRescheduleController;
use App\Http\Controllers\Klien\PraPendaftaranPerkaraController;
use App\Http\Controllers\PrivacyConsentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StafLegal\VerifikasiBerkasController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', function () {
    return response()->json(['ok' => true]);
});

Route::get('/kebijakan-privasi', [PrivacyConsentController::class, 'policy'])
    ->name('privacy.policy');

Route::get('/dashboard', [DashboardController::class, 'redirect'])
    ->middleware(['auth', 'active_account'])
    ->name('dashboard');

Route::middleware(['auth', 'active_account'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name(
        'profile.edit',
    );
    Route::patch('/profile', [ProfileController::class, 'update'])->name(
        'profile.update',
    )->middleware('throttle:10,1');
});

Route::middleware(['auth', 'active_account', 'role:klien', 'verified'])
    ->group(function () {
        Route::get('/persetujuan-privasi', [PrivacyConsentController::class, 'show'])
            ->name('privacy.consent.show');
        Route::post('/persetujuan-privasi', [PrivacyConsentController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('privacy.consent.store');
    });

Route::middleware(['auth', 'active_account', 'role:klien', 'verified', 'privacy_consent'])
    ->prefix('klien')
    ->name('klien.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'klien'])->name(
            'dashboard',
        );

        Route::get('/pra-pendaftaran', [
            PraPendaftaranPerkaraController::class,
            'index',
        ])->name('pra-pendaftaran.index');
        Route::get('/pra-pendaftaran/create', [
            PraPendaftaranPerkaraController::class,
            'create',
        ])->name('pra-pendaftaran.create');
        Route::post('/pra-pendaftaran', [
            PraPendaftaranPerkaraController::class,
            'store',
        ])->middleware('throttle:10,1')->name('pra-pendaftaran.store');
        Route::get('/pra-pendaftaran/{praPendaftaranPerkara}', [
            PraPendaftaranPerkaraController::class,
            'show',
        ])->name('pra-pendaftaran.show');

        Route::get('/dokumen/{dokumenPerkara}', [
            DokumenPerkaraController::class,
            'show',
        ])->middleware('throttle:60,1')->name('dokumen.show');

        Route::get('/catatan-verifikasi/{catatanVerifikasi}/perbaikan', [
            PerbaikanDokumenController::class,
            'create',
        ])->name('perbaikan-dokumen.create');
        Route::post('/catatan-verifikasi/{catatanVerifikasi}/perbaikan', [
            PerbaikanDokumenController::class,
            'store',
        ])->middleware('throttle:20,1')->name('perbaikan-dokumen.store');

        Route::get('/pra-pendaftaran/{praPendaftaranPerkara}/booking/create', [
            BookingKonsultasiController::class,
            'create',
        ])->name('booking-konsultasi.create');
        Route::post('/pra-pendaftaran/{praPendaftaranPerkara}/booking', [
            BookingKonsultasiController::class,
            'store',
        ])->middleware('throttle:10,1')->name('booking-konsultasi.store');
        Route::get('/booking-konsultasi', [
            BookingKonsultasiController::class,
            'index',
        ])->name('booking-konsultasi.index');
        Route::get('/booking-konsultasi/{bookingKonsultasi}', [
            BookingKonsultasiController::class,
            'show',
        ])->name('booking-konsultasi.show');

        Route::get(
            '/booking-konsultasi/{bookingKonsultasi}/reschedule/create',
            [PermintaanRescheduleController::class, 'create'],
        )->name('permintaan-reschedule.create');
        Route::post('/booking-konsultasi/{bookingKonsultasi}/reschedule', [
            PermintaanRescheduleController::class,
            'store',
        ])->middleware('throttle:10,1')->name('permintaan-reschedule.store');
        Route::get('/permintaan-reschedule/{permintaanReschedule}', [
            PermintaanRescheduleController::class,
            'show',
        ])->name('permintaan-reschedule.show');
    });

Route::middleware(['auth', 'active_account', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name(
            'dashboard',
        );

        Route::get('/laporan', [LaporanController::class, 'index'])->name(
            'laporan.index',
        );
        Route::get('/laporan/cetak', [
            LaporanController::class,
            'cetakIndex',
        ])->name('laporan.index.cetak');

        Route::get('/laporan/pra-pendaftaran', [
            LaporanController::class,
            'praPendaftaran',
        ])->name('laporan.pra-pendaftaran');
        Route::get('/laporan/pra-pendaftaran/cetak', [
            LaporanController::class,
            'cetakPraPendaftaran',
        ])->name('laporan.pra-pendaftaran.cetak');

        Route::get('/laporan/verifikasi-berkas', [
            LaporanController::class,
            'verifikasiBerkas',
        ])->name('laporan.verifikasi-berkas');
        Route::get('/laporan/verifikasi-berkas/cetak', [
            LaporanController::class,
            'cetakVerifikasiBerkas',
        ])->name('laporan.verifikasi-berkas.cetak');

        Route::get('/laporan/booking-konsultasi', [
            LaporanController::class,
            'bookingKonsultasi',
        ])->name('laporan.booking-konsultasi');
        Route::get('/laporan/booking-konsultasi/cetak', [
            LaporanController::class,
            'cetakBookingKonsultasi',
        ])->name('laporan.booking-konsultasi.cetak');

        Route::get('/laporan/reschedule-konsultasi', [
            LaporanController::class,
            'rescheduleKonsultasi',
        ])->name('laporan.reschedule-konsultasi');
        Route::get('/laporan/reschedule-konsultasi/cetak', [
            LaporanController::class,
            'cetakRescheduleKonsultasi',
        ])->name('laporan.reschedule-konsultasi.cetak');

        Route::get('/laporan/pengajuan-selesai', [
            LaporanController::class,
            'pengajuanSelesai',
        ])->name('laporan.pengajuan-selesai');
        Route::get('/laporan/pengajuan-selesai/cetak', [
            LaporanController::class,
            'cetakPengajuanSelesai',
        ])->name('laporan.pengajuan-selesai.cetak');

        Route::get('/klien', [KlienController::class, 'index'])->name(
            'klien.index',
        );
        Route::get('/klien/{user}', [
            KlienController::class,
            'show',
        ])->name('klien.show');
        Route::get('/klien/{user}/edit', [
            KlienController::class,
            'edit',
        ])->name('klien.edit');
        Route::put('/klien/{user}', [
            KlienController::class,
            'update',
        ])->middleware('throttle:20,1')->name('klien.update');
        Route::patch('/klien/{user}/status', [
            KlienController::class,
            'updateStatus',
        ])->middleware('throttle:20,1')->name('klien.status');
        Route::patch('/klien/{user}/password', [
            KlienController::class,
            'updatePassword',
        ])->middleware('throttle:10,1')->name('klien.password');

        Route::get('/staf-legal', [StafLegalController::class, 'index'])->name(
            'staf-legal.index',
        );
        Route::get('/staf-legal/create', [
            StafLegalController::class,
            'create',
        ])->name('staf-legal.create');
        Route::post('/staf-legal', [StafLegalController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('staf-legal.store');
        Route::get('/staf-legal/{user}', [
            StafLegalController::class,
            'show',
        ])->name('staf-legal.show');
        Route::get('/staf-legal/{user}/edit', [
            StafLegalController::class,
            'edit',
        ])->name('staf-legal.edit');
        Route::put('/staf-legal/{user}', [
            StafLegalController::class,
            'update',
        ])->middleware('throttle:20,1')->name('staf-legal.update');
        Route::patch('/staf-legal/{user}/status', [
            StafLegalController::class,
            'updateStatus',
        ])->middleware('throttle:20,1')->name('staf-legal.status');
        Route::patch('/staf-legal/{user}/password', [
            StafLegalController::class,
            'updatePassword',
        ])->middleware('throttle:10,1')->name('staf-legal.password');

        Route::get('/kategori-perkara', [
            KategoriPerkaraController::class,
            'index',
        ])->name('kategori-perkara.index');
        Route::get('/kategori-perkara/create', [
            KategoriPerkaraController::class,
            'create',
        ])->name('kategori-perkara.create');
        Route::post('/kategori-perkara', [
            KategoriPerkaraController::class,
            'store',
        ])->middleware('throttle:20,1')->name('kategori-perkara.store');
        Route::get('/kategori-perkara/{kategoriPerkara}', [
            KategoriPerkaraController::class,
            'show',
        ])->name('kategori-perkara.show');
        Route::get('/kategori-perkara/{kategoriPerkara}/edit', [
            KategoriPerkaraController::class,
            'edit',
        ])->name('kategori-perkara.edit');
        Route::put('/kategori-perkara/{kategoriPerkara}', [
            KategoriPerkaraController::class,
            'update',
        ])->middleware('throttle:20,1')->name('kategori-perkara.update');
        Route::delete('/kategori-perkara/{kategoriPerkara}', [
            KategoriPerkaraController::class,
            'destroy',
        ])->middleware('throttle:10,1')->name('kategori-perkara.destroy');

        Route::get('/pra-pendaftaran', [
            AdminPraPendaftaranController::class,
            'index',
        ])->name('pra-pendaftaran.index');
        Route::get('/pra-pendaftaran/{praPendaftaranPerkara}', [
            AdminPraPendaftaranController::class,
            'show',
        ])->name('pra-pendaftaran.show');
        Route::get('/dokumen/{dokumenPerkara}', [
            AdminPraPendaftaranController::class,
            'showDokumen',
        ])->middleware('throttle:60,1')->name('dokumen.show');

        Route::get('/jadwal-konsultasi', [
            JadwalKonsultasiController::class,
            'index',
        ])->name('jadwal-konsultasi.index');
        Route::get('/jadwal-konsultasi/create', [
            JadwalKonsultasiController::class,
            'create',
        ])->name('jadwal-konsultasi.create');
        Route::post('/jadwal-konsultasi', [
            JadwalKonsultasiController::class,
            'store',
        ])->middleware('throttle:20,1')->name('jadwal-konsultasi.store');
        Route::get('/jadwal-konsultasi/{jadwalKonsultasi}', [
            JadwalKonsultasiController::class,
            'show',
        ])->name('jadwal-konsultasi.show');
        Route::get('/jadwal-konsultasi/{jadwalKonsultasi}/edit', [
            JadwalKonsultasiController::class,
            'edit',
        ])->name('jadwal-konsultasi.edit');
        Route::put('/jadwal-konsultasi/{jadwalKonsultasi}', [
            JadwalKonsultasiController::class,
            'update',
        ])->middleware('throttle:20,1')->name('jadwal-konsultasi.update');
        Route::patch('/jadwal-konsultasi/{jadwalKonsultasi}/status', [
            JadwalKonsultasiController::class,
            'updateStatus',
        ])->middleware('throttle:20,1')->name('jadwal-konsultasi.status');

        Route::get('/booking-konsultasi', [
            AdminBookingKonsultasiController::class,
            'index',
        ])->name('booking-konsultasi.index');
        Route::get('/booking-konsultasi/{bookingKonsultasi}', [
            AdminBookingKonsultasiController::class,
            'show',
        ])->name('booking-konsultasi.show');
        Route::patch('/booking-konsultasi/{bookingKonsultasi}/konfirmasi', [
            AdminBookingKonsultasiController::class,
            'confirm',
        ])->middleware('throttle:20,1')->name('booking-konsultasi.konfirmasi');
        Route::patch('/booking-konsultasi/{bookingKonsultasi}/selesai', [
            AdminBookingKonsultasiController::class,
            'selesai',
        ])->middleware('throttle:20,1')->name('booking-konsultasi.selesai');

        Route::get('/permintaan-reschedule', [
            AdminPermintaanRescheduleController::class,
            'index',
        ])->name('permintaan-reschedule.index');
        Route::get('/permintaan-reschedule/{permintaanReschedule}', [
            AdminPermintaanRescheduleController::class,
            'show',
        ])->name('permintaan-reschedule.show');
        Route::patch('/permintaan-reschedule/{permintaanReschedule}/setujui', [
            AdminPermintaanRescheduleController::class,
            'approve',
        ])->middleware('throttle:20,1')->name('permintaan-reschedule.setujui');
        Route::patch('/permintaan-reschedule/{permintaanReschedule}/tolak', [
            AdminPermintaanRescheduleController::class,
            'reject',
        ])->middleware('throttle:20,1')->name('permintaan-reschedule.tolak');
    });

Route::middleware(['auth', 'active_account', 'role:staf_legal'])
    ->prefix('staf-legal')
    ->name('staf-legal.')
    ->group(function () {
        Route::get('/dashboard', [
            DashboardController::class,
            'stafLegal',
        ])->name('dashboard');

        Route::get('/verifikasi-berkas', [
            VerifikasiBerkasController::class,
            'index',
        ])->name('verifikasi-berkas.index');
        Route::get('/riwayat-verifikasi', [
            VerifikasiBerkasController::class,
            'riwayat',
        ])->name('verifikasi-berkas.riwayat');
        Route::get('/verifikasi-berkas/{praPendaftaranPerkara}', [
            VerifikasiBerkasController::class,
            'show',
        ])->name('verifikasi-berkas.show');
        Route::get('/verifikasi-berkas/{praPendaftaranPerkara}/verifikasi', [
            VerifikasiBerkasController::class,
            'verifikasi',
        ])->name('verifikasi-berkas.verifikasi');
        Route::post('/verifikasi-berkas/{praPendaftaranPerkara}', [
            VerifikasiBerkasController::class,
            'store',
        ])->middleware('throttle:20,1')->name('verifikasi-berkas.store');
        Route::get('/dokumen/{dokumenPerkara}', [
            VerifikasiBerkasController::class,
            'showDokumen',
        ])->middleware('throttle:60,1')->name('dokumen.show');
    });

require __DIR__.'/auth.php';
