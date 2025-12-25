<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function browse(Request $request)
    {
        if ($request->filled('username')) {
            if ($request->username === 'all') {
                return redirect()->route('drivers.index');
            }

            if ($request->username === 'feed') {
                return redirect()->route('feed.index');
            }

            return redirect()->route('users.drivers.index', [
                'user' => $request->username,
            ]);
        }

        $users = User::orderBy('name')->get();

        return view('users.browse', compact('users'));
    }
}
