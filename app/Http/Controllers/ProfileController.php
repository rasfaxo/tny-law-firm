<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\SessionRevocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(
        ProfileUpdateRequest $request,
        SessionRevocationService $sessions,
    ): RedirectResponse {
        $user = $request->user();

        $emailChanged = false;

        DB::transaction(function () use ($user, $request, &$emailChanged) {
            $user->fill($request->safe()->only(['nama', 'email', 'no_telepon']));

            $emailChanged = $user->isDirty('email');
            if ($emailChanged) {
                $user->email_verified_at = null;
            }

            $user->save();

            if ($user->role === 'klien') {
                $user->profilKlien()->updateOrCreate(
                    ['id_user' => $user->id_user],
                    $request->safe()->only(['alamat', 'jenis_kelamin', 'pekerjaan', 'no_identitas'])
                );
            }
        });

        if ($emailChanged && $user->isKlien()) {
            $sessions->revokeAllFor($user, $request->session()->getId());
            $user->sendEmailVerificationNotification();

            return Redirect::route('verification.notice')->with(
                'status',
                'verification-link-sent',
            );
        }

        return Redirect::route('profile.edit')->with(
            'status',
            'profile-updated',
        );
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
