<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\{Rule, DataAwareRule};
use App\Models\{Podcast, Show};

class ValidContentable implements Rule, DataAwareRule
{
    /**
     * The content
     */
    private $content;

    /**
     * All of the data under validation.
     *
     * @var array
     */
    protected $data;

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        switch ($this->data['upload_as']) {
            case 'PODCAST':
                return Podcast::where('id', $value)->exists();
            break;

            case 'SHOW':
                return Show::where('id', $value)->exists();
            break;

            default:
                return false;
            break;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute does not exist.';
    }
}
