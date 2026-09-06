<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SessionRevocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SessionRevocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_sessions_can_be_revoked_for_a_user(): void
    {
        config()->set('session.driver', 'database');
        $user = User::factory()->create();

        DB::table('sessions')->insert([
            ['id' => 'first-session', 'user_id' => $user->getKey(), 'payload' => 'payload', 'last_activity' => now()->timestamp],
            ['id' => 'second-session', 'user_id' => $user->getKey(), 'payload' => 'payload', 'last_activity' => now()->timestamp],
        ]);

        app(SessionRevocationService::class)->revokeAllFor($user, 'first-session');

        $this->assertDatabaseHas('sessions', ['id' => 'first-session']);
        $this->assertDatabaseMissing('sessions', ['id' => 'second-session']);

        app(SessionRevocationService::class)->revokeAllFor($user);
        $this->assertDatabaseMissing('sessions', ['id' => 'first-session']);
    }
}
