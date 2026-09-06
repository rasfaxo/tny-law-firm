<?php

namespace App\Policies;

use App\Models\BookingKonsultasi;
use App\Models\User;

class BookingKonsultasiPolicy
{
    public function view(User $user, BookingKonsultasi $booking): bool
    {
        return $user->isAdmin()
            || ($user->isKlien() && $booking->id_user === $user->id_user);
    }
}
