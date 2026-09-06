<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $primaryKey = 'id_audit';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_actor',
        'actor_role',
        'event',
        'auditable_type',
        'auditable_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new \LogicException('Audit log bersifat append-only.'));
        static::deleting(fn () => throw new \LogicException('Audit log bersifat append-only.'));
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'id_actor', 'id_user');
    }
}
