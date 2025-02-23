<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model {
    use HasFactory;

    protected $fillable = ['trip_id', 'name', 'address', 'lat', 'lng'];

    public function trip() {
        return $this->belongsTo(Trip::class);
    }

    public function photos() {
        return $this->hasMany(Photo::class);
    }
}