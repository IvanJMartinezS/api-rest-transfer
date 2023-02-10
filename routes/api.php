<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\EventController;

Route::post('/reset', [ResetController::class, 'reset']);

Route::get('/balance', [BalanceController::class, 'show']);

Route::post('/event', [EventController::class, 'store']);