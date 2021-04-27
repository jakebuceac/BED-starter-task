<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagStoreRequest;
use App\Http\Requests\TagNotReferencedRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\TagResource;
use App\Models\Post;
use App\Models\Tag;
use App\Rules\TagNotReferenced;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class TagController extends Controller
{
    /**
     * Creates a tag for authorised users
     *
     * @param TagStoreRequest $request
     * @return TagResource
     * @throws Throwable
     */
    public function store(TagStoreRequest $request): TagResource
    {
        $newTag = new Tag();
        $newTag->name = $request->name;

        $newTag->saveOrFail();

        return TagResource::make($newTag);
    }

    /**
     * Displays all the tags created from all users
     *
     * @param Request $request
     * @return JsonResource
     */
    public function index(Request $request): JsonResource
    {
        // stores index into the cache
        return Cache::remember('tags_' . $request->name, 10, function () use ($request) {
            $query = Tag::query();

            // if name is specified as a param show tags with name otherwise show all posts
            if (!empty($request->name)) {
                return TagResource::collection($query->byName($request->name)->orderBy('id', 'desc')->offset($request->name)->limit(5)->get());
            } else {
                return TagResource::collection($query->orderBy('id', 'desc')->offset($request->name)->limit(5)->get());
            }
        });
    }

    /**
     * Gets a tag from its id
     *
     * @param Request $request
     * @param Tag $tag
     * @return TagResource
     */
    public function show(Request $request, Tag $tag): TagResource
    {
        return TagResource::make($tag);
    }

    /**
     * Updates a tag for the authorised user
     *
     * @param TagNotReferencedRequest $request
     * @param Tag $tag
     * @return TagResource
     * @throws Throwable
     */
    public function update(TagNotReferencedRequest $request, Tag $tag): JSONResource
    {
        $tag->name = $request->name;

        $tag->saveOrFail();

        return TagResource::make($tag);
    }

    /**
     * Deletes a tag for the authorised user
     *
     * @param Request $request
     * @param Tag $tag
     * @return JsonResponse
     * @throws ValidationException
     */
    public function delete(Request $request, Tag $tag): JsonResponse
    {
        // creates a custom validator
        $validator = Validator::make([
            'tag_id' => $tag->id,
        ], [
            // checks if tag is being used by a post
            'tag_id' => [
                new TagNotReferenced($tag)
            ],
        ]);

        $validator->validate();

        $tag->delete();

        return new JsonResponse([], 202);
    }
}
