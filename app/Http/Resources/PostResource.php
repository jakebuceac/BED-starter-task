<?php

namespace App\Http\Resources;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'type' => 'Post',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                'body' => $this->body_to_html,
                'slug' => $this->slug,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'user' => UserResource::make($this->user),
            'tags' => TagResource::collection($this->tags),
        ];
    }
}
