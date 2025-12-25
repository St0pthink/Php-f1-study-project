<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{

    public function index(): View
    {
        $user = Auth::user();

        $friendIds = $user->friends()->pluck('users.id');

        $drivers = Driver::whereIn('user_id', $friendIds)
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('feed.index', compact('drivers'));
    }
}
