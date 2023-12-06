<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePodcastRequest extends FormRequest
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
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'unique:podcasts'],
            'description' => ['required'],
            'link' => ['required', 'url', 'unique:podcasts'],
            'subtitle' => ['required'],
            'summary' => ['required'],
            'owner_name' => ['required'],
            'owner_email' => ['nullable', 'email'],
            'explicit' => ['nullable'],
            'type' => ['nullable'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:1024']
        ];
    }
}
