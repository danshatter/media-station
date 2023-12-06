<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class View extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'views';

    /**
     * The relationship with the Content model
     */
    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * The relationship with the User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
