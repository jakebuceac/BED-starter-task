<?php

namespace App\Http\Requests;

use App\Rules\TagNotReferenced;
use Illuminate\Foundation\Http\FormRequest;

class TagNotReferencedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                new TagNotReferenced($this->route('tag'))
            ]
        ];
    }
}
