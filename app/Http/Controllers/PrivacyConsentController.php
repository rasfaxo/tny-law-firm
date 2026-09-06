<?php

namespace App\Http\Controllers;

use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PrivacyConsentController extends Controller
{
    public function policy(): View
    {
        return view('privacy.policy');
    }

    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasAcceptedPrivacyPolicy()) {
            return redirect()->route('dashboard');
        }

        return view('privacy.consent');
    }

    public function store(Request $request, AuditLogService $auditLog): RedirectResponse
    {
        abort_unless(config('privacy.ready'), 503, 'Kebijakan privasi belum siap digunakan.');

        $validated = $request->validate([
            'privacy_consent' => ['accepted'],
        ]);

        unset($validated);
        $version = (string) config('privacy.policy_version');

        abort_if($version === '', 503, 'Versi kebijakan privasi belum dikonfigurasi.');

        DB::transaction(function () use ($request, $auditLog, $version): void {
            $consent = $request->user()->privacyConsents()->firstOrCreate(
                ['policy_version' => $version],
                ['agreed_at' => now()],
            );

            if ($consent->wasRecentlyCreated) {
                $auditLog->record('privacy.consent.accepted', $consent, $request->user(), [
                    'policy_version' => $version,
                ]);
            }
        });

        return redirect()->route('dashboard')->with('success', 'Persetujuan kebijakan privasi tersimpan.');
    }
}
