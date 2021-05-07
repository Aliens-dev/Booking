<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['title','state','city','street','description', 'price','type_id', 'rooms','bedrooms','bathrooms','beds','video'];

    public function images() {
        return $this->morphMany(Image::class,'imageable');
    }

    public function renter(){
        return $this->belongsTo(Renter::class,'user_id');
    }

    public function client() {
        return $this->belongsToMany(Client::class,'client_properties');
    }

    public function ratings() {
        return $this->hasMany(Rating::class, 'property_id');
    }

    public function rules()
    {
        return $this->belongsToMany(Rule::class,'property_rules');
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class,'facility_properties');
    }
    public function amenities()
    {
        return $this->belongsToMany(Amenity::class,'amenity_properties');
    }
}
