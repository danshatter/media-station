<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\{NoWhitespace, Phone, PhoneUnique};

class ProfileRequest extends FormRequest
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
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', "unique:users,email,{$this->user()->id}"],
            'phone' => ['required', new NoWhitespace, new Phone, new PhoneUnique($this->user()->id)]
        ];
    }
}
