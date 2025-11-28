<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DriverController;

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
Route::get('/', [DriverController::class, 'index'])->name('home');
Route::get('/drivers/manage', [DriverController::class, 'manage'])->name('drivers.manage');
Route::resource('drivers', DriverController::class);

