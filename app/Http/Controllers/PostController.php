<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PostController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private readonly PostService $postService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min($request->integer('per_page', 20), 100);

        $posts = Post::with('user')
                     ->withCount('comments')
                     ->latest()
                     ->paginate($perPage);

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        // user_id diambil dari token — BUKAN dari input user (cegah IDOR)
        $post = $this->postService->create(
            data: $request->validated(),
            userId: $request->user()->id,
        );

        return (new PostResource($post->load('user')))->response()->setStatusCode(201);
    }

    public function show(Post $post): PostResource
    {
        // Route Model Binding — otomatis 404 jika tidak ada
        $post->load('user', 'comments');
        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, Post $post): PostResource
    {
        $this->authorize('update', $post);
        $post->update($request->validated());
        return new PostResource($post->fresh('user'));
    }

    public function destroy(Post $post): Response
    {
        $this->authorize('delete', $post);
        $post->delete();
        return response()->noContent(); // HTTP 204
    }
}
