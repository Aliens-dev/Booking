<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
class Reservation extends Model
{
    use HasFactory;
    protected $hidden = ['updated_at'];
    protected $dates = [
        'created_at',
        'updated_at',
        'start_time',
        'end_time'
    ];
    protected $fillable = ['start_time','end_time','receipt','client_id', 'property_id'];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    public function getReceiptAttribute($value)
    {
        return url('/') . '/' . $value;
    }
}
