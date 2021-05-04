<?php

namespace App\Models;


use App\Scopes\RenterScope;

class Renter extends User
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new RenterScope());
    }

    public function properties() {
        return $this->hasMany(Property::class,'user_id');
    }
}
