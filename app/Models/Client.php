<?php

namespace App\Models;


use App\Scopes\ClientScope;

class Client extends User
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ClientScope());
    }

    public function properties() {
        return $this->belongsToMany(Property::class, 'reservations');
    }

    public function ratings() {
        return $this->hasMany(Rating::class, 'property_id');
    }
}
