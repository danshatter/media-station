<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Advert;

class StoreAdvertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
            'url' => ['required', 'url'],
            'position'=> ['required', Rule::in([
                Advert::TOP,
                Advert::BOTTOM,
                Advert::LEFT,
                Advert::RIGHT
            ])]
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $position = $this->input('position');

        if (is_string($position)) {
            $this->merge([
                'position' => strtoupper($position)
            ]);
        }
    }
}
