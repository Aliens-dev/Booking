<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['title','state','city','street','description', 'price', 'type', 'rooms','video'];

    public function images() {
        return $this->morphMany(Image::class,'imageable');
    }

    public function renter(){
        return $this->belongsTo(User::class,'user_id');
    }
}
