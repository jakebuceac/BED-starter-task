<?php

namespace App\Rules;

use App\Models\Tag;
use Illuminate\Contracts\Validation\Rule;

class TagNotReferenced implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public Tag $tag;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
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
        $referenced = Tag::whereHas('posts', function($query) use ($value) {
            $query->where('tag_id', '=', $this->tag->id);
        })->first() !== null;

        return $referenced === false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This tag is being referenced by a post.';
    }
}
