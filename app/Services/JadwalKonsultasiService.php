<?php

namespace App\Services;

use App\Models\JadwalKonsultasi;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class JadwalKonsultasiService
{
    /**
     * @param  array{tanggal: string, waktu_mulai: string, waktu_selesai: string}  $data
     */
    public function create(array $data, int $adminId): JadwalKonsultasi
    {
        $this->ensureStartIsFuture($data['tanggal'], $data['waktu_mulai']);
        $this->ensureNoOverlap(
            $data['tanggal'],
            $data['waktu_mulai'],
            $data['waktu_selesai'],
        );

        return JadwalKonsultasi::create([
            'id_user' => $adminId,
            'tanggal' => $data['tanggal'],
            'waktu_mulai' => $data['waktu_mulai'],
            'waktu_selesai' => $data['waktu_selesai'],
            'status_slot' => 'tersedia',
        ]);
    }

    /**
     * @param  array{tanggal: string, waktu_mulai: string, waktu_selesai: string, status_slot: string}  $data
     */
    public function update(
        JadwalKonsultasi $jadwalKonsultasi,
        array $data,
    ): void {
        $this->ensureNotTerisi($jadwalKonsultasi);

        if ($data['status_slot'] === 'tersedia') {
            $this->ensureStartIsFuture(
                $data['tanggal'],
                $data['waktu_mulai'],
            );
        }

        if ($data['status_slot'] === 'tersedia') {
            $this->ensureNoOverlap(
                $data['tanggal'],
                $data['waktu_mulai'],
                $data['waktu_selesai'],
                $jadwalKonsultasi->id_jadwal,
            );
        }

        $jadwalKonsultasi->update([
            'tanggal' => $data['tanggal'],
            'waktu_mulai' => $data['waktu_mulai'],
            'waktu_selesai' => $data['waktu_selesai'],
            'status_slot' => $data['status_slot'],
        ]);
    }

    public function updateStatus(
        JadwalKonsultasi $jadwalKonsultasi,
        string $statusSlot,
    ): void {
        $this->ensureNotTerisi($jadwalKonsultasi);

        if ($statusSlot === 'tersedia') {
            if (! $jadwalKonsultasi->belumDimulai()) {
                throw ValidationException::withMessages([
                    'status_slot' => 'Slot yang sudah dimulai atau telah lewat tidak dapat diaktifkan kembali.',
                ]);
            }

            $this->ensureNoOverlap(
                $jadwalKonsultasi->tanggal->toDateString(),
                substr((string) $jadwalKonsultasi->waktu_mulai, 0, 5),
                substr((string) $jadwalKonsultasi->waktu_selesai, 0, 5),
                $jadwalKonsultasi->id_jadwal,
            );
        }

        $jadwalKonsultasi->update([
            'status_slot' => $statusSlot,
        ]);
    }

    private function ensureNotTerisi(JadwalKonsultasi $jadwalKonsultasi): void
    {
        if ($jadwalKonsultasi->status_slot !== 'terisi') {
            return;
        }

        throw ValidationException::withMessages([
            'status_slot' => 'Slot jadwal yang sudah terisi tidak dapat diubah pada fase ini.',
        ]);
    }

    private function ensureNoOverlap(
        string $tanggal,
        string $waktuMulai,
        string $waktuSelesai,
        ?int $ignoreId = null,
    ): void {
        $hasOverlap = JadwalKonsultasi::query()
            ->whereDate('tanggal', $tanggal)
            ->whereIn('status_slot', ['tersedia', 'terisi'])
            ->when(
                $ignoreId !== null,
                fn ($query) => $query->where('id_jadwal', '!=', $ignoreId),
            )
            ->where('waktu_mulai', '<', $waktuSelesai)
            ->where('waktu_selesai', '>', $waktuMulai)
            ->exists();

        if (! $hasOverlap) {
            return;
        }

        throw ValidationException::withMessages([
            'waktu_mulai' => 'Jadwal konsultasi bentrok dengan slot tersedia atau terisi pada tanggal yang sama.',
        ]);
    }

    private function ensureStartIsFuture(
        string $tanggal,
        string $waktuMulai,
    ): void {
        $waktuMulai = strlen($waktuMulai) === 5
            ? $waktuMulai.':00'
            : substr($waktuMulai, 0, 8);
        $mulai = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $tanggal.' '.$waktuMulai,
            config('app.timezone'),
        );

        if ($mulai->isAfter(now(config('app.timezone')))) {
            return;
        }

        throw ValidationException::withMessages([
            'waktu_mulai' => 'Waktu mulai harus berada setelah waktu saat ini.',
        ]);
    }
}
