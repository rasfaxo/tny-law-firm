<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateKlienPasswordRequest;
use App\Http\Requests\Admin\UpdateKlienRequest;
use App\Http\Requests\Admin\UpdateKlienStatusRequest;
use App\Http\Requests\IndexFilterRequest;
use App\Models\PraPendaftaranPerkara;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\SessionRevocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class KlienController extends Controller
{
    public function index(IndexFilterRequest $request): View
    {
        $query = User::query()->where('role', 'klien');
        $filters = $request->validated();

        if (filled($filters['search'] ?? null)) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $klien = $query->orderBy('nama')->paginate(10)->withQueryString();

        return view('admin.klien.index', compact('klien'));
    }

    public function show(User $user): View
    {
        $this->ensureKlien($user);

        $user->load(['profilKlien']);
        $pengajuan = PraPendaftaranPerkara::query()
            ->with(['kategori'])
            ->where('id_user', $user->id_user)
            ->latest('tanggal_pengajuan')
            ->get();

        return view('admin.klien.show', ['klien' => $user, 'pengajuan' => $pengajuan]);
    }

    public function edit(User $user): View
    {
        $this->ensureKlien($user);

        return view('admin.klien.edit', ['klien' => $user]);
    }

    public function update(
        UpdateKlienRequest $request,
        User $user,
        AuditLogService $auditLog,
        SessionRevocationService $sessions,
    ): RedirectResponse {
        $this->ensureKlien($user);

        $validated = $request->validated();

        $emailChanged = $user->email !== $validated['email'];
        $statusChanged = $user->status_akun !== $validated['status_akun'];

        DB::transaction(function () use ($request, $user, $validated, $emailChanged, $auditLog): void {
            $user->update([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
                'no_telepon' => $validated['no_telepon'] ?? null,
                'status_akun' => $validated['status_akun'],
            ]);

            $auditLog->record('account.updated', $user, $request->user(), [
                'email_changed' => $emailChanged,
                'status_changed' => $user->wasChanged('status_akun'),
                'status' => $user->status_akun,
            ]);
        });

        if ($emailChanged || ($statusChanged && $user->status_akun !== 'aktif')) {
            $sessions->revokeAllFor($user);
        }

        if ($emailChanged && $user->status_akun === 'aktif') {
            $user->sendEmailVerificationNotification();
        }

        return redirect()
            ->route('admin.klien.show', $user)
            ->with('success', 'Data dasar Klien berhasil diperbarui.');
    }

    public function updateStatus(
        UpdateKlienStatusRequest $request,
        User $user,
        AuditLogService $auditLog,
        SessionRevocationService $sessions,
    ): RedirectResponse {
        $this->ensureKlien($user);

        DB::transaction(function () use ($request, $user, $auditLog): void {
            $user->update([
                'status_akun' => $request->validated()['status_akun'],
            ]);

            $auditLog->record('account.status_changed', $user, $request->user(), [
                'status' => $user->status_akun,
            ]);
        });

        if ($user->status_akun !== 'aktif') {
            $sessions->revokeAllFor($user);
        }

        return back()->with('success', 'Status akun Klien berhasil diperbarui.');
    }

    public function updatePassword(
        UpdateKlienPasswordRequest $request,
        User $user,
        AuditLogService $auditLog,
        SessionRevocationService $sessions,
    ): RedirectResponse {
        $this->ensureKlien($user);

        DB::transaction(function () use ($request, $user, $auditLog): void {
            $user->update([
                'password' => Hash::make($request->validated()['password']),
            ]);

            $auditLog->record('account.password_reset_by_admin', $user, $request->user());
        });

        $sessions->revokeAllFor($user);

        return back()->with('success', 'Password Klien berhasil di-reset.');
    }

    private function ensureKlien(User $user): void
    {
        abort_unless($user->role === 'klien', 404);
    }
}
