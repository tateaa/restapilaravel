<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**    
     * GET /posts
     * Ambil semua posts
     */
    public function index(): JsonResponse
    {
        $posts = Post::all();
        return response()->json($posts);
    }

    /**
     * POST /posts
     * Tambah post baru
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'status'  => 'required|string',
            'content' => 'nullable|string',
            'user_id' => 'nullable|integer',
        ]);

        $post = Post::create($validated);

        return response()->json([
            ...$post->toArray(),
            'link' => "/posts/{$post->id}",
        ], 201);
    }

    /**
     * GET /posts/{id}
     * Ambil satu post berdasarkan ID
     */
    public function show(Post $post): JsonResponse
    {
        return response()->json($post);
    }

    /**
     * PUT /posts/{id}
     * Update post berdasarkan ID
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'title'   => 'sometimes|string|max:255',
            'status'  => 'sometimes|string',
            'content' => 'sometimes|nullable|string',
            'user_id' => 'sometimes|nullable|integer',
        ]);

        $post->update($validated);

        return response()->json($post->fresh());
    }

    /**
     * DELETE /posts/{id}
     * Hapus post berdasarkan ID
     */
    public function destroy(Post $post): JsonResponse
    {
        $postId = $post->id;
        $post->delete();

        return response()->json([
            'id'      => $postId,
            'deleted' => 'true',
        ]);
    }
}
