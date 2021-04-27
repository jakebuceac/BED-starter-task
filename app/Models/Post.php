<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Mail\Markdown;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'body',
        'slug',
    ];

    /**
     * Get the user that owns the post
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The tags that belong to the post
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'posts_tags_link');
    }

    /**
     * Converts from markdown to HTML
     */
    public function getBodyToHtmlAttribute(): string
    {
        return Markdown::parse($this->body)->toHtml();
    }

    /**
     * Gets post by slug
     */
    public function scopeBySlug($query, $type)
    {
        return $query->where('slug', $type);
    }

    /**
     * Gets post by name
     */
    public function scopeByName($query, $value)
    {
        return $query->where('title', 'LIKE', '%' . $value . '%');
    }
}
