<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\RedirectController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/{destination_id}', [DestinationController::class, 'show']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
    Route::post('/refresh', 'AuthController@refresh')->middleware('auth');
});

Route::prefix('customer')->group(function () {
    Route::post('/create', [CustomerController::class, 'store'])->middleware('auth');
    Route::get('/all', [CustomerController::class, 'index'])->middleware('auth');
    Route::get('/{customer_id}/show', [CustomerController::class, 'show'])->middleware('auth');
    Route::delete('/{customer_id}/delete', [CustomerController::class, 'destroy'])->middleware('auth');
});

Route::prefix('redirect')->group(function () {
    Route::post('/create', [RedirectController::class, 'store'])->middleware('auth');
    Route::get('/all', [RedirectController::class, 'index'])->middleware('auth');
    Route::get('/{redirect_id}/show', [RedirectController::class, 'show'])->middleware('auth');
    Route::delete('/{redirect_id}/delete', [RedirectController::class, 'destroy'])->middleware('auth');
    Route::post('/{redirect_id}/new-destination', [RedirectController::class, 'storeDestination'])->middleware('auth');
});

Route::prefix('destination')->group(function () {
    Route::delete('/{destination_id}/delete', [DestinationController::class, 'destroy']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
