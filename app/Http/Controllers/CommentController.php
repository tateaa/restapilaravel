<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    use AuthorizesRequests;
    public function index(Post $post, Request $request): AnonymousResourceCollection
    {
        $perPage = min($request->integer('per_page', 20), 100);

        $comments = $post->comments()
                        ->with('user')
                        ->latest()
                        ->paginate($perPage);

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, Post $post): CommentResource
    {
        $comment = $post->comments()->create([
            'comment' => $request->validated('comment'),
            'user_id' => $request->user()->id,
        ]);

        return (new CommentResource($comment->load('user')))->response()->setStatusCode(201);
    }

    public function show(Post $post, Comment $comment): CommentResource
    {
        $this->authorize('view', $comment);
        $comment->load('user', 'post');
        return new CommentResource($comment);
    }

    public function update(UpdateCommentRequest $request, Post $post, Comment $comment): CommentResource
    {
        $this->authorize('update', $comment);
        $comment->update($request->validated());
        return new CommentResource($comment->fresh('user'));
    }

    public function destroy(Post $post, Comment $comment): Response
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return response()->noContent();
    }
}
