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

    public function ratings() {
        return $this->morphMany(Rating::class, 'rateable','rateable_type','rateable_id');
    }

    public function avg_ratings() {
        return $this->ratings()->avg('rating');
    }
    
    public function total_ratings() {
        return $this->ratings()->count('rating');
    }

}
