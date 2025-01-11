<?php

use App\Http\Controllers\InstagramController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () { return response()->json(['message' => 'API is 
    working!']);
});

Route::get('/task',[TaskController::class,'index']);

Route::post('/login/google', [AuthController::class, 'googleLogin']);

Route::post('/upload-video', [InstagramController::class, 'uploadVideo']);

