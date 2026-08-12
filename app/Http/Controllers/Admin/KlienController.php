<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateKlienPasswordRequest;
use App\Http\Requests\Admin\UpdateKlienRequest;
use App\Http\Requests\Admin\UpdateKlienStatusRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class KlienController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->where("role", "klien");

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $klien = $query->orderBy("nama")->paginate(10)->withQueryString();

        return view("admin.klien.index", compact("klien"));
    }

    public function show(User $user): View
    {
        $this->ensureKlien($user);

        $user->load(['profilKlien']);
        $pengajuan = \App\Models\PraPendaftaranPerkara::query()
            ->with(['kategori'])
            ->where('id_user', $user->id_user)
            ->latest('tanggal_pengajuan')
            ->get();

        return view("admin.klien.show", ["klien" => $user, "pengajuan" => $pengajuan]);
    }

    public function edit(User $user): View
    {
        $this->ensureKlien($user);

        return view("admin.klien.edit", ["klien" => $user]);
    }

    public function update(
        UpdateKlienRequest $request,
        User $user,
    ): RedirectResponse {
        $this->ensureKlien($user);

        $validated = $request->validated();

        $user->update([
            "nama" => $validated["nama"],
            "email" => $validated["email"],
            "no_telepon" => $validated["no_telepon"] ?? null,
            "status_akun" => $validated["status_akun"],
        ]);

        return redirect()
            ->route("admin.klien.show", $user)
            ->with("success", "Data dasar Klien berhasil diperbarui.");
    }

    public function updateStatus(
        UpdateKlienStatusRequest $request,
        User $user,
    ): RedirectResponse {
        $this->ensureKlien($user);

        $user->update([
            "status_akun" => $request->validated()["status_akun"],
        ]);

        return back()->with("success", "Status akun Klien berhasil diperbarui.");
    }

    public function updatePassword(
        UpdateKlienPasswordRequest $request,
        User $user,
    ): RedirectResponse {
        $this->ensureKlien($user);

        $user->update([
            "password" => Hash::make($request->validated()["password"]),
        ]);

        return back()->with("success", "Password Klien berhasil di-reset.");
    }

    private function ensureKlien(User $user): void
    {
        abort_unless($user->role === "klien", 404);
    }
}
