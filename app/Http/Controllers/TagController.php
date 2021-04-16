<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagStoreRequest;
use App\Models\Tag;
use App\Rules\TagNotReferenced;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class TagController extends Controller
{
    /**
     * Creates a tag for authorised users
     *
     * @param TagStoreRequest $request
     * @return Tag
     * @throws ValidationException
     * @throws Throwable
     */
    public function store(TagStoreRequest $request): Tag
    {
        // creates new tag object
        $newTag = new Tag();
        $newTag->name = $request->name;


        // gets tag by the name that was created
        $duplicateTag = Tag::where('name', $newTag->name)->first();

        // if the name already exists then it will throw an exception
        if ($duplicateTag)
        {
            throw ValidationException::withMessages([
                'name' => ['The name has already been taken.']
            ]);
        }

        // stores tag to the database
        $newTag->saveOrFail();


        // returns the tag that was created
        return $newTag;
    }

    /**
     * Displays all the tags created from all users
     *
     * @param Request $request
     * @return Tag|Collection
     */
    public function index(Request $request)
    {
        // displays all the tags that have been created so far
        return Tag::all();
    }

    /**
     * Gets a tag from its id
     *
     * @param Request $request
     * @param Tag $tag
     * @return Tag
     */
    public function show(Request $request, Tag $tag): Tag
    {
        // finds and returns the post with the matching id
        return $tag;
    }

    /**
     * Updates a tag for the authorised user
     *
     * @param TagStoreRequest $request
     * @param Tag $tag
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(TagStoreRequest $request, Tag $tag): JsonResponse
    {
        // creates a custom validator
        $validator = Validator::make([
            'tag_id' => $tag->id,
        ], [
            // checks if tag is being used by a post
            'tag_id' => [
                new TagNotReferenced()
            ],
        ]);

        $validator->validate();

        // finds and updates the post with the matching id
        $tag->update($request->all());

        // returns 202 if the request was successful
        return new JsonResponse([], 202);
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
                new TagNotReferenced()
            ],
        ]);

        $validator->validate();

        // deletes tag if it is not being used by a post
        $tag->delete();

        // returns 202 if the request was successful
        return new JsonResponse([], 202);
    }
}
