<?php


namespace App;

use App\Models\Post;
use Illuminate\Support\Str;

function makeSlug($title): string
{
    $slug = Str::slug($title);

    $allSlugs = Post::bySlug($slug)->get();

    if (! $allSlugs->contains('slug', $slug))
    {
        return $slug;
    }

    for ($index = 1; $index <= $index; $index++)
    {
        $newSlug = $slug . '-' . $index;
        $allSlugs = Post::bySlug($newSlug)->get();
        if (! $allSlugs->contains('slug', $newSlug))
        {
            return $newSlug;
        }
    }
}
