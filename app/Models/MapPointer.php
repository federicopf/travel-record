<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapPointer extends Model
{
    use HasFactory;

    protected $table = 'map_pointers'; // Nome della tabella nel DB

    protected $fillable = [
        'name',
        'url',
    ];
}
