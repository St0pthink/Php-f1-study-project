<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ApiTokenController extends Controller
{
    public function create(): RedirectResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('api-token');

        return back()
            ->with('new_token', $token->plainTextToken)
            ->with('success', 'Токен создан!');
    }

    public function revoke(): RedirectResponse
    {
        Auth::user()->tokens()->delete();
        return back()->with('success', 'Токен удалён.');
    }
}
