<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The ID of a user
     */
    public const USER = 1;

    /**
     * The ID of an administrator
     */
    public const ADMINISTRATOR = 2;

    /**
     * The relationship with the User model
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
