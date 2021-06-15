<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RentPolicy
{
    use HandlesAuthorization;


    public function verify(User $user, Reservation $reservation)
    {
        return (int)$user->id === (int)$reservation->client_id || $user->user_role === 'admin';
    }

    public function approve(User $user, Reservation $reservation)
    {
        return (int)$user->id === (int)$reservation->client_id || $user->user_role === 'admin';
    }

    public function decline(User $user, Reservation $reservation)
    {
        return (int)$user->id === (int)$reservation->client_id || $user->user_role === 'admin';
    }
}
