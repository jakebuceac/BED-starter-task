<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachTagToPostRequest;
use App\Http\Requests\PostStoreRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class PostController extends Controller
{
    /**
     * Creates a post for authorised users
     *
     * @param PostStoreRequest $request
     * @return PostResource
     * @throws ValidationException
     * @throws Throwable
     */
    public function store(PostStoreRequest $request): PostResource
    {
        // creates new post object
        $newPost = new Post();
        $newPost->title = $request->title;
        $newPost->body = $request->body;
        $newPost->slug = Str::slug($newPost->title);

        // gets post by the slug that was created
        $duplicatePost = Post::where('slug', $newPost->slug)->first();

        // if the slug already exists then it will throw an exception
        if ($duplicatePost)
        {
            throw ValidationException::withMessages([
                'slug' => ['The slug has already been taken.']
            ]);
        }

        // creates the rest of the post object fields and stores them to the database
        $newPost->user_id = $request->user()->id;
        $newPost->saveOrFail();


        // returns the post that was created
        return PostResource::make($newPost);
    }

    /**
     * Displays all the posts created from all users
     *
     * @param Request $request
     * @return Post[]|Collection
     */
    public function index(Request $request)
    {
        // displays all the posts that have been created so far
        return Post::all();
    }

    /**
     * Gets a post from its id
     *
     * @param Request $request
     * @param Post $post
     * @return PostResource
     */
    public function show(Request $request, Post $post): PostResource
    {
        // finds and returns the post with the matching id
        return PostResource::make($post);
    }

    /**
     * Updates a post for the authorised owner
     *
     * @param PostStoreRequest $request
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(PostStoreRequest $request, Post $post): JsonResponse
    {
        // checks if the user is the owner of the post
        $this->authorize('update', $post);

        // finds and updates the post with the matching id
        $post->update($request->all());

        // returns 202 if the request was successful
        return new JsonResponse([], 202);

    }

    /**
     * Deletes a post for the authorised owner
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function delete(Request $request, Post $post): JsonResponse
    {
        // checks if the user is the owner of the post
        $this->authorize('delete', $post);

        $post->tags()->detach();

        // finds and deletes the post with the matching id
        $post->delete();

        // returns 202 if the request was successful
        return new JsonResponse([], 202);
    }

    /**
     * Adds a tag to the post
     *
     * @param AttachTagToPostRequest $request
     * @param Post $post
     * @return mixed
     */
    public function attach(AttachTagToPostRequest $request, Post $post)
    {
        // gets the tag id from the request
        $tag = Tag::find($request->tag_id);

        // attaches the tag to the post
        return $post->tags()->attach($tag);
    }
}
