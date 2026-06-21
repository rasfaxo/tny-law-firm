<?php

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
