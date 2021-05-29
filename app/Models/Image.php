<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $fillable = ['url'];
    protected $hidden = ['created_at','updated_at','imageable_type','imageable_id'];

    public function getUrlAttribute($value)
    {
        return url('/') . '/' . $value;
    }

    public function imageable() {
        return $this->morphTo();
    }
}
