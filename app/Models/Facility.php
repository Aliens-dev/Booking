<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;
    protected $hidden = ['pivot'];

    protected $fillable = ['name'];
    public function properties() {
        return $this->belongsToMany(Property::class, 'facility_properties');
    }

}
