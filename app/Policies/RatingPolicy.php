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
     * @param Client $client
     * @param Rating $rating
     * @return mixed
     */
    public function update(Client $client, Rating $rating)
    {
        return $rating->client_id == $client->id;
    }

    /**
     * Determine whether the renter can delete the model.
     *
     * @param Client $client
     * @param Rating $rating
     * @return mixed
     */
    public function delete(Client $client, Rating $rating)
    {
        return $rating->client_id == $client->id;
    }
}
