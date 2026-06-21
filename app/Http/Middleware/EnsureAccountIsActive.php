<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->status_akun === "nonaktif") {
            Auth::guard("web")->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route("login")
                ->withErrors([
                    "email" => "Akun Anda nonaktif. Silakan hubungi admin.",
                ]);
        }

        return $next($request);
    }
}
