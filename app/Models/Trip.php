<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model {
    use HasFactory;

    protected $fillable = ['title', 'start_date', 'end_date', 'image'];

    public function places() {
        return $this->hasMany(Place::class);
    }
}
