<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $comments = Comment::with(['user', 'driver.user'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return CommentResource::collection($comments);
    }

    public function show(Comment $comment): CommentResource
    {
        $comment->load(['user', 'driver.user']);
        return new CommentResource($comment);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'driver_id' => 'required|integer|exists:drivers,id',
            'body' => 'required|string|max:1000',
        ]);

        $validated['user_id'] = Auth::id();
        $comment = Comment::create($validated);
        $comment->load(['user', 'driver.user']);

        return response()->json([
            'message' => 'Comment created successfully',
            'data' => new CommentResource($comment),
        ], 201);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        $user = Auth::user();

        if ($comment->user_id !== $user->id && !$user->is_admin) {
            return response()->json([
                'message' => 'Forbidden. You can only edit your own comments.',
            ], 403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment->update($validated);
        $comment->load(['user', 'driver.user']);

        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => new CommentResource($comment),
        ], 200);
    }
}
