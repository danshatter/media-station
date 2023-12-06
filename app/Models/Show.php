<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Show extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'link',
        'owner',
        'subtitle',
        'summary',
        'explicit',
        'type',
        'image',
        'image_url',
        'file_driver'
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
        'category_id' => 'integer',
        'owner' => 'array',
        'published_at' => 'datetime'
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
     * The relationship with the User model
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->using(ShowUser::class)
                    ->withTimestamps();
    }

    /**
     * The relationship with the View model
     */
    public function views()
    {
        return $this->hasManyThrough(View::class, Content::class, 'contentable_id', 'content_id', 'id', 'contentable_id');
    }

    /**
     * The relationship with the Category model
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The relationship with the Content model
     */
    public function contents()
    {
        return $this->morphMany(Content::class, 'contentable');
    }

    /**
     * The relationships checked for existence
     */
    private function existenceRelationships($user)
    {
        return [
            'users as following' => fn($query) => $query->where('user_id', $user?->id),
        ];
    }

    /**
     * The relationships checked for count
     */
    private function countRelationships($user)
    {
        return [
            'users as followers_count',
            'views as total_views_count'
        ];
    }

    /**
     * The relationships checked for count for the administrator
     */
    private function adminCountRelationships()
    {
        return [
            'users as followers_count'
        ];
    }
}
