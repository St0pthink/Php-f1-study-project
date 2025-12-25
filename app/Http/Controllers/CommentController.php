<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(Request $request, Driver $driver): RedirectResponse
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $driver->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        return back()->with('success', 'Комментарий добавлен');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $user = Auth::user();

        $isAdmin = $user->is_admin ?? false;

        if (!$isAdmin) {
            abort(403, 'Только администратор может удалять комментарии');
        }

        $comment->delete();

        return back()->with('success', 'Комментарий удалён');
    }
}
