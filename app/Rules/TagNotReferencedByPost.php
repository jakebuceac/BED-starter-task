<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class TagNotReferencedByPost implements Rule
{
    /**
     * @var Model
     */
    private Model $model;

    /**
     * Create a new rule instance.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
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
        $referenced = $this->model->tags()->where('tag_id', '=', $value)->first() !== null;

        return $referenced === false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This tag is already referenced by this post.';
    }
}
