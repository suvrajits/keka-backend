<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/hello', [App\Http\Controllers\HelloWorldController::class, 'index']);
Route::get('/auth/callback', [AuthController::class, 'handleInstagramCallback']);
Route::get('/', [App\Http\Controllers\HelloWorldController::class, 'kekaWelcomeMessage']);