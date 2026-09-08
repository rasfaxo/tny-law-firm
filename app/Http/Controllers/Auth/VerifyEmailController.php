<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectAfterVerification();
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->redirectAfterVerification();
    }

    private function redirectAfterVerification(): RedirectResponse
    {
        if (! config('privacy.ready')) {
            return redirect()
                ->route('privacy.policy')
                ->with('status', 'email-verified-policy-pending');
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
