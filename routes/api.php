<?php

use App\Http\Controllers\Api\RfidController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/check-card', [RfidController::class, 'checkCard']);
Route::post('/transactions', [TransactionController::class, 'store']);
