<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Content;

class ValidPodcastEpisode implements Rule
{
    /**
     * The valid podcast episodes type
     */
    private $types = [
        Content::AUDIO
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Get the content
        $content = Content::find($value);

        return in_array($content->type, $this->types);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The content type with the :attribute must be any of '.collect($this->types)->map(fn($type) => "\"{$type}\"")->implode(', ').'.';
    }
}
