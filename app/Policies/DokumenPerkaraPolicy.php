<?php

namespace App\Policies;

use App\Models\DokumenPerkara;
use App\Models\User;

class DokumenPerkaraPolicy
{
    public function view(User $user, DokumenPerkara $dokumen): bool
    {
        if ($user->isAdmin() || $user->isStafLegal()) {
            return $dokumen->praPendaftaranPerkara()->exists();
        }

        return $user->isKlien()
            && $dokumen->praPendaftaranPerkara()->where('id_user', $user->id_user)->exists();
    }
}
