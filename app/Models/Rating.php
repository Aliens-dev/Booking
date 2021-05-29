<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
class Rating extends Model
{
    use HasFactory;

    protected $fillable = ['rating','client_id'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    public function rateable()
    {
        $this->morphTo("rateable",'rateable_type','rateable_id');
    }
}
