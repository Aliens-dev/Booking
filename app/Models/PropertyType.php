<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
class PropertyType extends Model
{
    use HasFactory;

    protected $fillable = ['type'];
    protected $hidden = ['pivot','created_at','updated_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }
}
