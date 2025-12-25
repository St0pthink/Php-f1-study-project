<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FriendshipController extends Controller
{
    public function store($userId): RedirectResponse
    {
        $authUser = Auth::user();
        $user = User::findOrFail($userId);
        if ($authUser->id === $user->id) {
            return back()->with('error', 'Нельзя добавить себя в друзья');
        }

        if ($authUser->isFriend($user)) {
            return back()->with('error', 'Вы уже друзья');
        }

        DB::transaction(function () use ($authUser, $user) {
            $authUser->friends()->syncWithoutDetaching([$user->id]);
            $user->friends()->syncWithoutDetaching([$authUser->id]);
        });

        return back()->with('success', "Пользователь {$user->name} добавлен в друзья");
    }


    public function destroy($userId): RedirectResponse
    {
        $authUser = Auth::user();
        $user = User::findOrFail($userId);

        if ($authUser->id === $user->id) {
            return back()->with('error', 'Некорректная операция');
        }

        DB::transaction(function () use ($authUser, $user) {
            $authUser->friends()->detach($user->id);
            $user->friends()->detach($authUser->id);
        });

        return back()->with('success', "Пользователь {$user->name} удалён из друзей");
    }
}
