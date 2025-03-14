<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\ParkingController;
use App\Http\Controllers\api\ReservationController;


// authentification routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


// Parkings routes
Route::get('/parkings/search', [ParkingController::class, 'search']);
Route::apiResource('/parkings', ParkingController::class);

// reservation routes
Route::post('/parkings/{id}/reserve', [ReservationController::class, 'reserve']);
Route::delete('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);

// historique of reservations
Route::middleware('auth:sanctum')->get('/reservations/my-reservations', [ReservationController::class, 'myReservations']);


// statistics
Route::middleware(['auth:sanctum'])->get('/admin/statistics', [AdminController::class, 'parkingStatistics']);

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
