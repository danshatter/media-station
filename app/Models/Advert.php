<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    use HasFactory;

    /**
     * The top position
     */
    public const TOP = 'TOP';

    /**
     * The bottom position
     */
    public const BOTTOM = 'BOTTOM';

    /**
     * The left position
     */
    public const LEFT = 'LEFT';

    /**
     * The right position
     */
    public const RIGHT = 'RIGHT';

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'image_url',
        'file_driver',
        'url',
        'position'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'image',
        'file_driver',
        'impressions'
    ];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'impressions' => 0
    ];
}
