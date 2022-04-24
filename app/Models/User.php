<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'slug',
        'visibility'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tweets() {
        return $this->hasMany(Tweet::class);
    }

    public function replies() {
        return $this->hasMany(Reply::class);
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class, 'receiver_id');
    }

    public function followers() {
        return $this->hasMany(Follower::class, 'followed_id');
    }

    public function following() {
        return $this->hasMany(Follower::class, 'follower_id');
    }

    public function favourites() {
        return $this->hasMany(Favourite::class);
    }
}
