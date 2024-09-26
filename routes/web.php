<?php

use App\Http\Controllers\DestinationController;
use Illuminate\Support\Facades\Route;

Route::get('/go-to/{destination_id}', [DestinationController::class, 'goToRedirect']);