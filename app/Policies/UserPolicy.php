<?php

namespace App\Policies;

use App\Models\Renter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;


    /**
     * Determine whether the renter can create models.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function create(User $user, User $model)
    {
        return $user->id == $model->id;
    }

    /**
     * Determine whether the renter can update the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        return $user->id == $model->id;
    }

    /**
     * Determine whether the renter can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return $user->id == $model->id;
    }

    /**
     * Determine whether the renter can restore the model.
     *
     * @param Renter $renter
     * @param Renter $model
     * @return mixed
     */
    public function restore(Renter $renter, Renter $model)
    {
        //
    }

    /**
     * Determine whether the renter can permanently delete the model.
     *
     * @param Renter $renter
     * @param Renter $model
     * @return mixed
     */
    public function forceDelete(Renter $renter, Renter $model)
    {
        //
    }
}
