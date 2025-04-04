<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hashtag extends Model
{
    protected $fillable = ['name'];

    public function places(): BelongsToMany
    {
        return $this->belongsToMany(Place::class, 'hashtag_places');
    }
}
