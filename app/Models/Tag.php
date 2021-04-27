<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The posts that belong to the tag
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'posts_tags_link');
    }

    /**
     * Gets tags by name
     */
    public function scopeByName($query, $value)
    {
        return $query->where('name', 'LIKE', '%' . $value . '%');
    }


}
