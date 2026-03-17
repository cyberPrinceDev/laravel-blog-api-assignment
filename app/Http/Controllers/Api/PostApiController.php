<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Resources\PostResource;

class PostApiController extends Controller
{
    //
    public function index()
    {
        $posts = Post::with('user')->get();
        
        return PostResource::collection($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|unique:posts,title|max:255',
            'content'   => 'required',
        ]);

        $post = Auth::user()->posts()->create($validated);

        return new PostResource($post);
    }

    public function show(Post $post)
    {
        return new PostResource($post);
    }

    public function update(Request $request, Post $post)
    {
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            // Unique except for the current post ID
            'title'   => ['required', 'max:255', Rule::unique('posts')->ignore($post->id)],
            'content' => 'required',
        ]);

        $post->update($validated);

        return new PostResource($post);
    }

    public function destroy(Request $request, Post $post)
    {
        // Authorization: Ensure only the owner can delete
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
