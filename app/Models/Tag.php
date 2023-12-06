<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'pivot'
    ];

    /**
     * The relationship with the Content model
     */
    public function contents()
    {
        return $this->belongsToMany(Content::class)
                    ->using(ContentTag::class)
                    ->withTimestamps();
    }
}
