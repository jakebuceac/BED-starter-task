<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\JsonResponse;

class PostsController extends Controller
{
    /**
     * Displays all the posts that users created
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        // gets all the posts that are in the Post model
        $posts = Posts::all();

        // returns the all posts
        return response()->json(['posts' => $posts]);
    }
}
