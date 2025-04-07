<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'partner_name',
        'type',
        'email',
        'password',
        'username',
        'email_verified_at',
        'theme_id',
        'map_pointer_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function mapPointer()
    {
        return $this->belongsTo(MapPointer::class, 'map_pointer_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')
                    ->withTimestamps()
                    ->wherePivot('status', 'accepted');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')
                    ->withTimestamps()
                    ->wherePivot('status', 'accepted');
    }

    public function followRequests()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')
                    ->withTimestamps()
                    ->wherePivot('status', 'pending');
    }

    public function isFollowing(User $user)
    {
        return $this->following()->where('users.id', $user->id)->exists();
    }

    public function hasRequestedToFollow(User $user)
    {
        return $this->followRequests()->where('users.id', $user->id)->exists();
    }



}
