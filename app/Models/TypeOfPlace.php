<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfPlace extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['title', 'title_fr', 'description', 'description_fr'];
}
