<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
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
        'file_driver'
    ];

    /**
     * The relationship with the Podcast model
     */
    public function podcasts()
    {
        return $this->hasMany(Podcast::class);
    }

    /**
     * The relationship with the Show model
     */
    public function shows()
    {
        return $this->hasMany(Show::class);
    }
}
