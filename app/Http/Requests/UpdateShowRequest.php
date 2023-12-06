<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShowRequest extends FormRequest
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
            'name' => ['required', "unique:shows,name,{$this->show->id}"],
            'description' => ['required'],
            'link' => ['required', 'url', "unique:shows,link,{$this->show->id}"],
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
