<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachTagToPostRequest;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\TagResource;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Throwable;
use function App\makeSlug;

class PostController extends Controller
{
    /**
     * Creates a post for authorised users
     *
     * @param PostStoreRequest $request
     * @return PostResource
     * @throws Throwable
     */
    public function store(PostStoreRequest $request): JsonResource
    {
        $newPost = $request->user()->posts()->create([
            'title' => $request->title,
            'body' => $request->body,
            'slug' => makeSlug($request->title),
        ]);

        return PostResource::make($newPost);
    }

    /**
     * Displays all the posts created from all users
     *
     * @param Request $request
     * @return JsonResource
     */
    public function index(Request $request)
    {
        // stores index into the cache
        return Cache::remember('posts_' . $request->offset, 10, function () use ($request) {
            $query = Post::query();

            // if title is specified as a param show posts with title otherwise show all posts
            if (!empty($request->title)) {
                return PostResource::collection($query->byName($request->title)->orderBy('id', 'desc')->offset($request->offset)->limit(5)->get());
            } else {
                return PostResource::collection($query->orderBy('id', 'desc')->offset($request->offset)->limit(5)->get());
            }
        });
    }

    /**
     * Gets a post from its id
     *
     * @param Request $request
     * @param Post $post
     * @return PostResource
     */
    public function show(Request $request, Post $post): JsonResource
    {
        return PostResource::make($post);
    }

    /**
     * Updates a post for the authorised owner
     *
     * @param UpdatePostRequest $request
     * @param Post $post
     * @return PostResource
     * @throws Throwable
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResource
    {
        $post->title = $request->title;
        $post->body = $request->body;
        $post->slug = makeSlug($request->title);
        $post->saveOrFail();

        return PostResource::make($post);
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

        $post->delete();

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
        $tag = Tag::find($request->tag_id);

        return $post->tags()->attach($tag);
    }
}
