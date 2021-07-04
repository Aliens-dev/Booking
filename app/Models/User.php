<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasFactory;

    protected $table = 'users';
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','updated_at', 'email_verified_at', 'identity_pic'
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
    protected $fillable = [
        'fname',
        'lname',
        'email',
        'password',
        'dob',
        'phone_number',
        'user_role',
        'profile_pic',
        'identity_pic',
        'verified'
    ];


    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    public function getProfilePicAttribute($value)
    {
        return url('/') . '/' . $value;
    }

    public function scopeBasic($query)
    {
        return $query->where('user_role', '!=', 'admin');
    }

    public function scopeVerified($query){
        return $query->where('verified',1);
    }
    public function scopeNotVerified($query){
        return $query->where('verified',0);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
