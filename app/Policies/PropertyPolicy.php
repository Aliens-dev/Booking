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
     * @param User $user
     * @param Property $property
     * @return mixed
     */
    public function create(User $user, Property $property)
    {
        return $user->id === (int)$property->user_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Property $property
     * @return mixed
     */
    public function update(User $user, Property $property)
    {
        return  $user->id === (int)$property->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Property $property
     * @return mixed
     */
    public function delete(User $user, Property $property)
    {
        return  $user->id === (int)$property->user_id || $user->user_role === 'admin';
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
        return  $user->properties->contains($property) || (int)$property->user_id === $user->id || $user->user_role === 'admin';
    }


}
