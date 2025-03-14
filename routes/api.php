<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\ParkingController;
use App\Http\Controllers\api\ReservationController;

Route::apiResource('/parkings', ParkingController::class);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/parkings/{id}', [ParkingController::class, 'show']);
Route::get('/parkings/search', [ParkingController::class, 'search']);
// Route::get('/parkings/search', [ParkingController::class, 'search']);

Route::post('/parkings/{id}/reserve', [ReservationController::class, 'reserve']);
Route::delete('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);
Route::middleware('auth:sanctum')->get('/reservations/my-reservations', [ReservationController::class, 'myReservations']);

Route::middleware(['auth:sanctum'])->get('/admin/statistics', [AdminController::class, 'parkingStatistics']);

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
