<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmenityProperty extends Model
{
    use HasFactory;
    protected $hidden = ['pivot','created_at','updated_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }
}
