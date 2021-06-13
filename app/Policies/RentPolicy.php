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
        return (int)$user->id === (int)$reservation->client_id;
    }

    public function approve(User $user, Reservation $reservation)
    {
        $property = Property::find($reservation->property_id)->first();
        $renter_id = $property->user_id;
        return (int)$user->id === (int)$renter_id;
    }

    public function decline(User $user, Reservation $reservation)
    {
        $property = Property::find($reservation->property_id)->first();
        $renter_id = $property->user_id;
        return (int)$user->id === (int)$renter_id;
    }
}
