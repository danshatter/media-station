<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'content_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'content_id' => 'integer'
    ];

    /**
     * The relationship with the User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The relationship with the Content model
     */
    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
