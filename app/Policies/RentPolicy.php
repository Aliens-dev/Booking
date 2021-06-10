<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RentPolicy
{
    use HandlesAuthorization;

    public function verify(User $user, Reservation $reservation)
    {

        return (int)$user->id === (int)$reservation->client_id;
    }

}
