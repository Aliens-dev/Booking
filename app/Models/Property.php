<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory, Filterable;

    protected $hidden = ['pivot','updated_at'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    protected $fillable = ['title','state','city','street','description', 'price','type_id', 'rooms','bedrooms','bathrooms','beds','video'];


    public function type() {
        return $this->belongsTo(PropertyType::class,'type_id');
    }

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

    public function avg_ratings() {
        return $this->ratings()->avg('rating');
    }
    public function total_ratings() {
        return $this->ratings()->sum('rating');
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


    public function hasAttribute($attr)
    {
        return array_key_exists($attr,$this->attributes);
    }
}
