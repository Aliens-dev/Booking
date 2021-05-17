<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = ['title','title_ar','description','description_ar'];
    protected $hidden = ['pivot'];
    public function properties() {
        return $this->belongsToMany(Property::class, 'amenity_properties');
    }
}
