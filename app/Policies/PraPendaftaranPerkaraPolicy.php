<?php

namespace App\Policies;

use App\Models\PraPendaftaranPerkara;
use App\Models\User;

class PraPendaftaranPerkaraPolicy
{
    public function view(User $user, PraPendaftaranPerkara $pengajuan): bool
    {
        return $user->isAdmin()
            || $user->isStafLegal()
            || ($user->isKlien() && $pengajuan->id_user === $user->id_user);
    }
}
