<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Accommodation Routes
|--------------------------------------------------------------------------
*/
Route::apiResource('accommodations', AccommodationController::class);
Route::get('accommodations/history/all', [AccommodationController::class, 'history']);
Route::get('accommodations/history/search', [AccommodationController::class, 'historySearch']);
Route::post('accommodations/available', [AccommodationController::class, 'availableForDates']);

/*
|--------------------------------------------------------------------------
| Reservation Routes (Protected)
|--------------------------------------------------------------------------
*/
// For demo purposes, disable auth middleware so frontend can call endpoints withoutLaravel-sanctum authentication
Route::apiResource('reservations', ReservationController::class);
Route::post('reservations/{reservation}/check-in', [ReservationController::class, 'checkIn']);
Route::post('reservations/{reservation}/check-out', [ReservationController::class, 'checkOut']);
Route::post('reservations/{reservation}/confirm', [ReservationController::class, 'confirm']);

// additional resources for full persistence
Route::apiResource('customers', CustomerController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('inventory', InventoryController::class);
Route::apiResource('payments', PaymentController::class);
