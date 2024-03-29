<?php

use App\Http\Controllers\Api\PlaneController;
use App\Http\Controllers\Api\PlaneReservationController;
use App\Http\Controllers\Api\SunTimeController;
use App\Http\Controllers\Api\UserController;
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

Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/', [UserController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/plane/', [PlaneController::class, 'index']);
    Route::get('/plane/{plane_registration}', [PlaneController::class, 'show']);
    Route::post('/plane/', [PlaneController::class, 'store']);

    Route::get('/user/', [UserController::class, 'index']);
    Route::get('/user/logout', [UserController::class, 'logout']);
    
    Route::get('/plane/{plane_registration}/reservation/{starts_at_date}', [PlaneReservationController::class, 'listByDate']);
    Route::post('/plane/{plane_registration}/reservation/{starts_at_date}', [PlaneReservationController::class, 'make']);
    Route::delete('/plane/reservation/{reservation_id}', [PlaneReservationController::class, 'removeReservation']);
    Route::patch('/plane/reservation/{reservation_id}/confirm', [PlaneReservationController::class, 'confirmReservation']);
    Route::get('/plane/reservation/date/{date}', [PlaneReservationController::class, 'getAllReservationsForDate']);
    Route::get('/plane/reservation/user/starting_from_date/{date}', [PlaneReservationController::class, 'getAllUpcomingReservationsStartingFromDate']);

    Route::get('/suntimes/{starts_at_date}', [SunTimeController::class, 'getSunriseAndSunsetTimes']);
});