<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Property;
use App\Models\Renter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param Renter $user
     * @return mixed
     */
    public function viewAny(Renter $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param Renter $user
     * @param Property $property
     * @return mixed
     */
    public function view(Renter $user, Property $property)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param Renter $renter
     * @return mixed
     */
    public function create(Renter $renter)
    {
        return $renter instanceof Renter;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param Renter $renter
     * @param Property $property
     * @return mixed
     */
    public function update(Renter $renter, Property $property)
    {
        return  $renter->id === (int)$property->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param Renter $renter
     * @param Property $property
     * @return mixed
     */
    public function delete(Renter $renter, Property $property)
    {
        return  $renter->id === (int)$property->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Property $property
     * @return mixed
     */
    public function cancelRent(User $user, Property $property)
    {
        return  $user->properties->contains($property) || (int)$property->user_id === $user->id;
    }


}