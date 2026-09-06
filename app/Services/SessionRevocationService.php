<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class SessionRevocationService
{
    public function revokeAllFor(User $user, ?string $exceptSessionId = null): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        $query = DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id_user);

        if ($exceptSessionId !== null) {
            $query->where('id', '!=', $exceptSessionId);
        }

        $query->delete();
    }
}
