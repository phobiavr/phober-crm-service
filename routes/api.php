<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.server')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    Route::put('/customers/{id}/loyalty-card', [CustomerController::class, 'setLoyaltyCard']);
    Route::get('/customers/upcoming-birthdays', [CustomerController::class, 'upcomingBirthdays']);
});

Route::middleware('private')->group(function () {
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
});
