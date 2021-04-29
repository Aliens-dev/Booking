<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guarded = ['created_at', 'updated_at'];


    public function scopeClient($query) {
        return $query->where('user_role', 'client');
    }

    public function scopeRenter($query) {
        return $query->where('user_role', 'renter');
    }

    public function properties() {
        return $this->hasMany(Property::class,'user_id');
    }
}
