<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
class Rule extends Model
{
    use HasFactory;

    protected $fillable = ['title','title_fr','description','description_fr'];

    protected $hidden = ['pivot','created_at','updated_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }
    public function properties()
    {
        return $this->belongsToMany(Property::class,'property_rules');
    }
}
