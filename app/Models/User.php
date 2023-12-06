<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'pivot',
        'verification',
        'reset_password_verification',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role_id' => 'integer',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'role_id' => Role::USER
    ];

    /**
     * Scope for users
     */
    public function scopeUsers($query)
    {
        return $query->where('role_id', Role::USER);
    }

    /**
     * Scope for administrators
     */
    public function scopeAdministrators($query)
    {
        return $query->where('role_id', Role::ADMINISTRATOR);
    }

    /**
     * The relationship with the Role model
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * The relationship with the Content model
     */
    public function likes()
    {
        return $this->belongsToMany(Content::class, 'likes')
                    ->using(Like::class)
                    ->withTimestamps();
    }

    /**
     * The relationship with the Playlist model
     */
    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    /**
     * The relationship with the Queue model
     */
    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    /**
     * The relationship with the View model
     */
    public function views()
    {
        return $this->belongsToMany(Content::class, 'views')
                    ->using(View::class)
                    ->withTimestamps();
    }

    /**
     * The relationship with the Favourite model
     */
    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    /**
     * The relationship with the Podcast model
     */
    public function podcasts()
    {
        return $this->belongsToMany(Podcast::class)
                    ->using(PodcastUser::class)
                    ->withTimestamps();
    }

    /**
     * The relationship with the Show model
     */
    public function shows()
    {
        return $this->belongsToMany(Show::class)
                    ->using(ShowUser::class)
                    ->withTimestamps();
    }
}
