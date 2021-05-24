<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
class Facility extends Model
{
    use HasFactory;
    protected $hidden = ['pivot','created_at','updated_at'];

    protected $fillable = ['title','title_fr','description','description_ar'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    public function properties() {
        return $this->belongsToMany(Property::class, 'facility_properties');
    }

}
