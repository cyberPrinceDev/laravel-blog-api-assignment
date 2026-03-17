<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PostApiController extends Controller
{
    //
    public function index()
    {
        return response()->json(Post::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|unique:posts,title|max:255',
            'content'   => 'required',
        ]);

        $post = Auth::user()->posts()->create($validated);

        return response()->json($post, 201);
    }

    public function show(Post $post)
    {
        return response()->json($post, 200);
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

        return response()->json($post, 200);
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
