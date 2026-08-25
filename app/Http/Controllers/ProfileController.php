<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view("profile.edit", [
            "user" => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $request) {
            $user->fill($request->safe()->only(['nama', 'email', 'no_telepon']));
            $user->save();

            if ($user->role === 'klien') {
                $user->profilKlien()->updateOrCreate(
                    ['id_user' => $user->id_user],
                    $request->safe()->only(['alamat', 'jenis_kelamin', 'pekerjaan', 'no_identitas'])
                );
            }
        });

        return Redirect::route("profile.edit")->with(
            "status",
            "profile-updated",
        );
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag("userDeletion", [
            "password" => ["required", "current_password"],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to("/");
    }
}
