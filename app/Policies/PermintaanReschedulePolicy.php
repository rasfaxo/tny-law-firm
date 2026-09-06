<?php

namespace App\Policies;

use App\Models\PermintaanReschedule;
use App\Models\User;

class PermintaanReschedulePolicy
{
    public function view(User $user, PermintaanReschedule $permintaan): bool
    {
        return $user->isAdmin()
            || ($user->isKlien() && $permintaan->id_user === $user->id_user);
    }
}
