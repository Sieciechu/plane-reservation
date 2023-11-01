<?php

use App\Http\Controllers\PlaneController;
use App\Http\Controllers\PlaneReservationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

 
Route::get('/plane/', [PlaneController::class, 'index']);
Route::get('/plane/{plane_registration}', [PlaneController::class, 'show']);
Route::post('/plane/', [PlaneController::class, 'store']);

Route::get('/user/', [UserController::class, 'index']);
Route::post('/user/', [UserController::class, 'register']);

Route::get('/plane/{plane_registration}/reservation/{starts_at_date}', [PlaneReservationController::class, 'listByDate']);
Route::post('/plane/{plane_registration}/reservation/{starts_at_date}', [PlaneReservationController::class, 'make']);
Route::delete('/plane/{plane_registration}/reservation', [PlaneReservationController::class, 'removeReservation']);

Route::post('/user/login', [UserController::class, 'login']);