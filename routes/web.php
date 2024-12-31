<?php

use Illuminate\Support\Facades\Route;

Route::get('/hello', [App\Http\Controllers\HelloWorldController::class, 'index']);
