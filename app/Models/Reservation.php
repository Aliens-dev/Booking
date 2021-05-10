<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
