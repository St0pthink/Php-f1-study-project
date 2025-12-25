<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token_name' => 'nullable|string|max:255',
        ]);

        $tokenName = $request->input('token_name', 'api-token');
        $token = $request->user()->createToken($tokenName);

        return response()->json([
            'message' => 'Token created successfully',
            'token_name' => $tokenName,
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()->get(['id', 'name', 'created_at', 'last_used_at']);

        return response()->json([
            'tokens' => $tokens,
        ]);
    }

    public function destroy(Request $request, $tokenId): JsonResponse
    {
        $deleted = $request->user()->tokens()->where('id', $tokenId)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Token revoked successfully']);
        }

        return response()->json(['message' => 'Token not found'], 404);
    }
}
