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
        return $this
            ->belongsToMany(Property::class, 'reservations', 'client_id','property_id')
            ->withPivot('start_time','end_time');
    }

    public function ratings() {
        return $this->hasMany(Rating::class, 'property_id');
    }



}
