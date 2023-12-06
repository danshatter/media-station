<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    /**
     * For audio content
     */
    public const AUDIO = 'AUDIO';

    /**
     * For video content
     */
    public const VIDEO = 'VIDEO';

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'guid',
        'published_at',
        'enclosure_url',
        'type',
        'author',
        'subtitle',
        'summary',
        'duration',
        'explicit',
        'season',
        'episode_type',
        'image',
        'image_url',
        'file_driver',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'image',
        'file_driver',
        'pivot'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'season' => 'integer'
    ];

    /**
     * Load necessary relationships used by the application
     */
    public function loadRelationships($user = null)
    {
        return $this->loadCount($this->countRelationships($user));
    }

    /**
     * Load necessary relationships used by the application by the administrator
     */
    public function loadAdminRelationships()
    {
        return $this->loadCount($this->adminCountRelationships());
    }

    /**
     * Query with the necessary relationships used by the application
     */
    public function scopeWithRelationships($query, $user = null)
    {
        return $query->withCount($this->countRelationships($user));
    }

    /**
     * Query with the necessary relationships used by the application by the administrator
     */
    public function scopeWithAdminRelationships($query)
    {
        return $query->withCount($this->adminCountRelationships());
    }

    /**
     * The relationship with the Tag model
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class)
                    ->using(ContentTag::class)
                    ->withTimestamps();
    }

    /**
     * The relationship with the User model
     */
    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')
                    ->using(Like::class)
                    ->withTimestamps();
    }

    /**
     * The relationship with the Playlist model
     */
    public function playlists()
    {
        return $this->belongsToMany(Playlist::class)
                    ->using(ContentPlaylist::class)
                    ->withTimestamps();
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
        return $this->belongsToMany(User::class, 'views')
                    ->using(View::class)
                    ->withTimestamps();
    }

    /**
     * The relationship with the Comment model
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The relationship with the Favourite model
     */
    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    /**
     * The morph relationship
     */
    public function contentable()
    {
        return $this->morphTo();
    }

    /**
     * The relationships checked for existence
     */
    private function existenceRelationships($user)
    {
        return [
            'favourites as favourite' => fn($query) => $query->where('user_id', $user?->id),
            'likes as liked' => fn($query) => $query->where('user_id', $user?->id),
            'queues as queued' => fn($query) => $query->where('user_id', $user?->id),
            'views as viewed' => fn($query) => $query->where('user_id', $user?->id)
        ];
    }

    /**
     * The relationships checked for count
     */
    private function countRelationships($user)
    {
        return [
            'views',
            'likes'
        ];
    }

    /**
     * The relationships checked for count for the administrator
     */
    private function adminCountRelationships()
    {
        return [
            'views',
            'likes'
        ];
    }
}
