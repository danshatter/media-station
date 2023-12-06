<?php

namespace App\Http\Requests;

use App\Rules\ValidContentable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContentRequest extends FormRequest
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
            'show_or_podcast_id' => ['required', 'integer', new ValidContentable],
            'title' => ['required'],
            'description' => ['required'],
            'media_url' => ['required', 'url', 'unique:contents,enclosure_url'],
            'author' => ['required'],
            'subtitle' => ['required'],
            'summary' => ['required'],
            'duration_in_minutes' => ['required', 'integer', 'min:1'],
            'type' => ['required'],
            'upload_as' => ['required', Rule::in([
                'PODCAST',
                'SHOW'
            ])],
            'explicit' => ['nullable'],
            'season' => ['nullable', 'integer'],
            'episode_type' => ['nullable'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['nullable', 'integer', 'distinct', 'exists:tags,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $uploadAs = $this->input('upload_as');

        if (is_string($uploadAs)) {
            $this->merge([
                'upload_as' => strtoupper($uploadAs)
            ]);
        }
    }
}
