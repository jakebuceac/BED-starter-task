<?php

namespace App\Http\Requests;

use App\Rules\TagNotReferencedByPost;
use Illuminate\Foundation\Http\FormRequest;

class AttachTagToPostRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'tag_id' => [new TagNotReferencedByPost(request()->route('post'))],
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'tag_id.required' => 'Tag ID is required',
        ];
    }
}
