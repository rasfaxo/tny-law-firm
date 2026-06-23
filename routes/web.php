<?php

use App\Http\Controllers\Admin\KategoriPerkaraController;
use App\Http\Controllers\Admin\StafLegalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Klien\DokumenPerkaraController;
use App\Http\Controllers\Klien\PraPendaftaranPerkaraController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StafLegal\VerifikasiBerkasController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/dashboard", [DashboardController::class, "redirect"])
    ->middleware(["auth", "active_account"])
    ->name("dashboard");

Route::middleware(["auth", "active_account"])->group(function () {
    Route::get("/profile", [ProfileController::class, "edit"])->name(
        "profile.edit",
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update",
    );
});

Route::middleware(["auth", "active_account", "role:klien"])
    ->prefix("klien")
    ->name("klien.")
    ->group(function () {
        Route::get("/dashboard", [DashboardController::class, "klien"])->name(
            "dashboard",
        );

        Route::get("/pra-pendaftaran", [
            PraPendaftaranPerkaraController::class,
            "index",
        ])->name("pra-pendaftaran.index");
        Route::get("/pra-pendaftaran/create", [
            PraPendaftaranPerkaraController::class,
            "create",
        ])->name("pra-pendaftaran.create");
        Route::post("/pra-pendaftaran", [
            PraPendaftaranPerkaraController::class,
            "store",
        ])->name("pra-pendaftaran.store");
        Route::get("/pra-pendaftaran/{praPendaftaranPerkara}", [
            PraPendaftaranPerkaraController::class,
            "show",
        ])->name("pra-pendaftaran.show");

        Route::get("/pra-pendaftaran/{praPendaftaranPerkara}/dokumen/create", [
            DokumenPerkaraController::class,
            "create",
        ])->name("dokumen.create");
        Route::post("/pra-pendaftaran/{praPendaftaranPerkara}/dokumen", [
            DokumenPerkaraController::class,
            "store",
        ])->name("dokumen.store");
        Route::get("/dokumen/{dokumenPerkara}", [
            DokumenPerkaraController::class,
            "show",
        ])->name("dokumen.show");
    });

Route::middleware(["auth", "active_account", "role:admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::get("/dashboard", [DashboardController::class, "admin"])->name(
            "dashboard",
        );

        Route::get("/staf-legal", [StafLegalController::class, "index"])->name(
            "staf-legal.index",
        );
        Route::get("/staf-legal/create", [
            StafLegalController::class,
            "create",
        ])->name("staf-legal.create");
        Route::post("/staf-legal", [StafLegalController::class, "store"])->name(
            "staf-legal.store",
        );
        Route::get("/staf-legal/{user}", [
            StafLegalController::class,
            "show",
        ])->name("staf-legal.show");
        Route::get("/staf-legal/{user}/edit", [
            StafLegalController::class,
            "edit",
        ])->name("staf-legal.edit");
        Route::put("/staf-legal/{user}", [
            StafLegalController::class,
            "update",
        ])->name("staf-legal.update");
        Route::patch("/staf-legal/{user}/status", [
            StafLegalController::class,
            "updateStatus",
        ])->name("staf-legal.status");
        Route::patch("/staf-legal/{user}/password", [
            StafLegalController::class,
            "updatePassword",
        ])->name("staf-legal.password");

        Route::get("/kategori-perkara", [
            KategoriPerkaraController::class,
            "index",
        ])->name("kategori-perkara.index");
        Route::get("/kategori-perkara/create", [
            KategoriPerkaraController::class,
            "create",
        ])->name("kategori-perkara.create");
        Route::post("/kategori-perkara", [
            KategoriPerkaraController::class,
            "store",
        ])->name("kategori-perkara.store");
        Route::get("/kategori-perkara/{kategoriPerkara}", [
            KategoriPerkaraController::class,
            "show",
        ])->name("kategori-perkara.show");
        Route::get("/kategori-perkara/{kategoriPerkara}/edit", [
            KategoriPerkaraController::class,
            "edit",
        ])->name("kategori-perkara.edit");
        Route::put("/kategori-perkara/{kategoriPerkara}", [
            KategoriPerkaraController::class,
            "update",
        ])->name("kategori-perkara.update");
        Route::delete("/kategori-perkara/{kategoriPerkara}", [
            KategoriPerkaraController::class,
            "destroy",
        ])->name("kategori-perkara.destroy");
    });

Route::middleware(["auth", "active_account", "role:staf_legal"])
    ->prefix("staf-legal")
    ->name("staf-legal.")
    ->group(function () {
        Route::get("/dashboard", [
            DashboardController::class,
            "stafLegal",
        ])->name("dashboard");

        Route::get("/verifikasi-berkas", [
            VerifikasiBerkasController::class,
            "index",
        ])->name("verifikasi-berkas.index");
        Route::get("/verifikasi-berkas/{praPendaftaranPerkara}", [
            VerifikasiBerkasController::class,
            "show",
        ])->name("verifikasi-berkas.show");
        Route::post("/verifikasi-berkas/{praPendaftaranPerkara}", [
            VerifikasiBerkasController::class,
            "store",
        ])->name("verifikasi-berkas.store");
        Route::get("/dokumen/{dokumenPerkara}", [
            VerifikasiBerkasController::class,
            "showDokumen",
        ])->name("dokumen.show");
    });

require __DIR__ . "/auth.php";
