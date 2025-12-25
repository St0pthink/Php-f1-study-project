<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\FeedController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check())
    {
        return redirect()->route('users.drivers.index',  Auth::user());
    }
    return redirect()->route('login');
})->name('home');


Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('users.drivers.index', Auth::user());
    })->name('dashboard');

    Route::get('/drivers/manage', [DriverController::class, 'manage'])
        ->name('drivers.manage');

    Route::resource('drivers', DriverController::class);

    Route::post('/drivers/{id}/restore', [DriverController::class, 'restore'])
    ->name('drivers.restore');

    Route::delete('/drivers/{id}/force-delete', [DriverController::class, 'forceDelete'])
    ->name('drivers.force-delete');

    Route::get('/users/{user}/drivers', [DriverController::class, 'userDrivers'])
        ->name('users.drivers.index');
    
    Route::get('/users/browse', [UserController::class, 'browse'])
        ->name('users.browse');

    Route::get('/users/{user}/drivers', [DriverController::class, 'userDrivers'])
        ->name('users.drivers.index');

    Route::post('/drivers/{driver}/comments', [CommentController::class, 'store'])
        ->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');

    Route::post('/friendships/{userId}', [FriendshipController::class, 'store'])
        ->name('friendships.store');
    Route::delete('/friendships/{userId}', [FriendshipController::class, 'destroy'])
        ->name('friendships.destroy');

    Route::get('/feed', [FeedController::class, 'index'])
        ->name('feed.index');

});
require __DIR__.'/auth.php';
