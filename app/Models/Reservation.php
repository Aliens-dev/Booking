<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
class Reservation extends Model
{
    use HasFactory;
    protected $hidden = ['pivot'];
    protected $dates = [
        'created_at',
        'updated_at',
        'start_time',
        'end_time'
    ];
    protected $fillable = ['start_time','end_time','client_id', 'property_id'];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }
}
