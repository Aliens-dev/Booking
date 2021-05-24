<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
class Rating extends Model
{
    use HasFactory;

    protected $fillable = ['rating','updated_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    public function property() {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
