<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\PraPendaftaranPerkara;
use App\Services\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_audit_metadata_removes_sensitive_fields_recursively(): void
    {
        $actor = $this->createAdmin();
        $resource = $this->createPengajuan();

        app(AuditLogService::class)->record('test.sanitization', $resource, $actor, [
            'status' => 'aman',
            'password' => 'rahasia',
            'nested' => ['nik' => '123', 'result' => 'ok'],
            'file_path' => 'private/path.pdf',
        ]);

        $log = AuditLog::query()->firstOrFail();

        $this->assertSame(['status' => 'aman', 'nested' => ['result' => 'ok']], $log->metadata);
        $this->assertSame($actor->getKey(), $log->id_actor);
        $this->assertSame(PraPendaftaranPerkara::class, $log->auditable_type);
    }

    public function test_audit_log_cannot_be_changed_or_deleted_through_model(): void
    {
        $actor = $this->createAdmin();
        $log = app(AuditLogService::class)->record('test.append_only', $actor, $actor);

        try {
            $log->update(['event' => 'test.changed']);
            $this->fail('Audit log update seharusnya ditolak.');
        } catch (\LogicException) {
            $this->assertDatabaseHas('audit_logs', ['event' => 'test.append_only']);
        }

        $this->expectException(\LogicException::class);
        $log->delete();
    }
}
