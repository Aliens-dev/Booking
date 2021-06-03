<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RatingPolicy
{
    use HandlesAuthorization;


    /**
     * Determine whether the renter can update the model.
     *
     * @param User $user
     * @param Rating $rating
     * @return mixed
     */
    public function update(User $user, Rating $rating)
    {
        return $rating->client_id == $user->id;
    }

    /**
     * Determine whether the renter can delete the model.
     *
     * @param User $user
     * @param Rating $rating
     * @return mixed
     */
    public function delete(User $user, Rating $rating)
    {
        return $rating->client_id == $user->id;
    }
}
