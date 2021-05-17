<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = ['title','title_ar','description','description_ar'];

    protected $hidden = ['pivot','created_at','updated_at'];

    public function properties()
    {
        return $this->belongsToMany(Property::class,'property_rules');
    }
}
