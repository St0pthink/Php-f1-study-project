<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\UserController;
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
        return redirect()->route('drivers.index');
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

});
require __DIR__.'/auth.php';
