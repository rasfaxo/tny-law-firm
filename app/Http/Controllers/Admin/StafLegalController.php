<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStafLegalRequest;
use App\Http\Requests\Admin\UpdateStafLegalPasswordRequest;
use App\Http\Requests\Admin\UpdateStafLegalRequest;
use App\Http\Requests\Admin\UpdateStafLegalStatusRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StafLegalController extends Controller
{
    public function index(): View
    {
        $stafLegal = User::query()
            ->where("role", "staf_legal")
            ->orderBy("nama")
            ->paginate(10);

        return view("admin.staf-legal.index", compact("stafLegal"));
    }

    public function create(): View
    {
        return view("admin.staf-legal.create");
    }

    public function store(StoreStafLegalRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            "nama" => $validated["nama"],
            "email" => $validated["email"],
            "no_telepon" => $validated["no_telepon"] ?? null,
            "password" => Hash::make($validated["password"]),
            "role" => "staf_legal",
            "status_akun" => $validated["status_akun"],
        ]);

        return redirect()
            ->route("admin.staf-legal.show", $user)
            ->with("success", "Akun Staf Legal berhasil dibuat.");
    }

    public function show(User $user): View
    {
        $this->ensureStafLegal($user);

        return view("admin.staf-legal.show", ["stafLegal" => $user]);
    }

    public function edit(User $user): View
    {
        $this->ensureStafLegal($user);

        return view("admin.staf-legal.edit", ["stafLegal" => $user]);
    }

    public function update(
        UpdateStafLegalRequest $request,
        User $user,
    ): RedirectResponse {
        $this->ensureStafLegal($user);

        $validated = $request->validated();

        $user->update([
            "nama" => $validated["nama"],
            "email" => $validated["email"],
            "no_telepon" => $validated["no_telepon"] ?? null,
            "status_akun" => $validated["status_akun"],
            "role" => "staf_legal",
        ]);

        return redirect()
            ->route("admin.staf-legal.show", $user)
            ->with("success", "Data Staf Legal berhasil diperbarui.");
    }

    public function updateStatus(
        UpdateStafLegalStatusRequest $request,
        User $user,
    ): RedirectResponse {
        $this->ensureStafLegal($user);

        $user->update([
            "status_akun" => $request->validated()["status_akun"],
            "role" => "staf_legal",
        ]);

        return back()->with("success", "Status akun Staf Legal berhasil diperbarui.");
    }

    public function updatePassword(
        UpdateStafLegalPasswordRequest $request,
        User $user,
    ): RedirectResponse {
        $this->ensureStafLegal($user);

        $user->update([
            "password" => Hash::make($request->validated()["password"]),
            "role" => "staf_legal",
        ]);

        return back()->with("success", "Password Staf Legal berhasil diperbarui.");
    }

    private function ensureStafLegal(User $user): void
    {
        abort_unless($user->role === "staf_legal", 404);
    }
}
