<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePrivacyConsent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isKlien()) {
            return $next($request);
        }

        $version = config('privacy.policy_version');

        if (! config('privacy.ready') || ! is_string($version) || $version === '') {
            abort(503, 'Kebijakan privasi belum siap digunakan.');
        }

        if (! $user->hasAcceptedPrivacyPolicy($version)) {
            return redirect()->route('privacy.consent.show');
        }

        return $next($request);
    }
}
