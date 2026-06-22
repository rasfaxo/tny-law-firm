<?php

use App\Http\Controllers\Admin\StafLegalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
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
    });

Route::middleware(["auth", "active_account", "role:staf_legal"])
    ->prefix("staf-legal")
    ->name("staf-legal.")
    ->group(function () {
        Route::get("/dashboard", [
            DashboardController::class,
            "stafLegal",
        ])->name("dashboard");
    });

require __DIR__ . "/auth.php";
