<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function klien(): \Illuminate\View\View
    {
        return view("klien.dashboard");
    }

    public function admin(): \Illuminate\View\View
    {
        return view("admin.dashboard");
    }

    public function stafLegal(): \Illuminate\View\View
    {
        return view("staf-legal.dashboard");
    }

    private function logoutInvalidRole(Request $request): RedirectResponse
    {
        Auth::guard("web")->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("login")->withErrors([
            "email" => "Role akun tidak valid.",
        ]);
    }
}
