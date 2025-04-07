<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $table = 'follows';

    protected $fillable = ['follower_id', 'followed_id', 'status'];

    public $timestamps = true;

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

}
