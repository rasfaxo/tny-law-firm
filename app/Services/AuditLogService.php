<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    /**
     * Metadata is deliberately filtered to prevent sensitive case data,
     * credentials, file references, and verification notes entering the log.
     */
    public function record(
        string $event,
        Model $auditable,
        ?User $actor = null,
        array $metadata = [],
    ): AuditLog {
        return AuditLog::create([
            'id_actor' => $actor?->id_user,
            'actor_role' => $actor?->role,
            'event' => $event,
            'auditable_type' => $auditable::class,
            'auditable_id' => $auditable->getKey(),
            'metadata' => $this->sanitize($metadata),
        ]);
    }

    private function sanitize(array $metadata): array
    {
        $blockedFragments = [
            'password', 'token', 'secret', 'key', 'file', 'path', 'document',
            'dokumen', 'kronologi', 'identitas', 'nik', 'catatan', 'note',
        ];

        return collect($metadata)
            ->reject(function (mixed $value, string|int $key) use ($blockedFragments): bool {
                $normalized = strtolower((string) $key);

                return collect($blockedFragments)->contains(
                    fn (string $fragment): bool => str_contains($normalized, $fragment),
                );
            })
            ->map(fn (mixed $value): mixed => is_array($value) ? $this->sanitize($value) : $value)
            ->all();
    }
}
