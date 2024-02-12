<?php

use App\Http\Controllers\Web\PlaneReservationController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/login', [UserController::class, 'login'])->name('login');
Route::get('/register', [UserController::class, 'register']);

Route::get('/reservation', [PlaneReservationController::class, 'reservation']);
Route::get('/dashboard', [PlaneReservationController::class, 'dashboard']);
Route::get('/', [PlaneReservationController::class, 'empty']);
