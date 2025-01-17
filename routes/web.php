<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstagramCallbackController;
use App\Http\Controllers\InstagramLoginController;

Route::get('/hello', [App\Http\Controllers\HelloWorldController::class, 'index']);
Route::get('/auth/callback', [AuthController::class, 'handleInstagramCallback']);
Route::get('/', [App\Http\Controllers\HelloWorldController::class, 'kekaWelcomeMessage']);

Route::get('/auth/instagram/callback', [InstagramCallbackController::class, 'handleCallback'])->name('instagram.callback');

Route::get('/auth/instagram', [InstagramLoginController::class, 'redirectToInstagram'])->name('instagram.login');
