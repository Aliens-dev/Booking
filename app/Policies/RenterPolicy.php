<?php

namespace App\Policies;

use App\Models\Renter;
use Illuminate\Auth\Access\HandlesAuthorization;

class RenterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the renter can view any models.
     *
     * @param Renter $renter
     * @return mixed
     */
    public function viewAny(Renter $renter)
    {
        //
    }

    /**
     * Determine whether the renter can view the model.
     *
     * @param Renter $renter
     * @param Renter $model
     * @return mixed
     */
    public function view(Renter $renter, Renter $model)
    {
        //
    }

    /**
     * Determine whether the renter can create models.
     *
     * @param Renter $renter
     * @param Renter $model
     * @return mixed
     */
    public function create(Renter $renter, Renter $model)
    {
        return $renter->id == $model->id;
    }

    /**
     * Determine whether the renter can update the model.
     *
     * @param Renter $renter
     * @param Renter $model
     * @return mixed
     */
    public function update(Renter $renter, Renter $model)
    {
        return $renter->id == $model->id;
    }

    /**
     * Determine whether the renter can delete the model.
     *
     * @param Renter $renter
     * @param Renter $model
     * @return mixed
     */
    public function delete(Renter $renter, Renter $model)
    {
        return $renter->id == $model->id;
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
