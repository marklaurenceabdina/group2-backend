<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\ReservationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Accommodation Routes
|--------------------------------------------------------------------------
*/
Route::apiResource('accommodations', AccommodationController::class);
Route::post('accommodations/available', [AccommodationController::class, 'availableForDates']);

/*
|--------------------------------------------------------------------------
| Reservation Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('reservations', ReservationController::class);
    Route::post('reservations/{reservation}/check-in', [ReservationController::class, 'checkIn']);
    Route::post('reservations/{reservation}/check-out', [ReservationController::class, 'checkOut']);
    Route::post('reservations/{reservation}/confirm', [ReservationController::class, 'confirm']);
});
