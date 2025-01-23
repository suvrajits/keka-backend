<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstagramCallbackController;
use App\Http\Controllers\InstagramLoginController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminUserController;

Route::get('/hello', [App\Http\Controllers\HelloWorldController::class, 'index']);
Route::get('/auth/callback', [AuthController::class, 'handleInstagramCallback']);
Route::get('/', [App\Http\Controllers\HelloWorldController::class, 'kekaWelcomeMessage']);

Route::get('/auth/instagram/callback', [InstagramCallbackController::class, 'handleCallback'])->name('instagram.callback');

Route::get('/auth/instagram', [InstagramLoginController::class, 'redirectToInstagram'])->name('instagram.login');

Route::get('/showLeaderboard', [LeaderboardController::class, 'showLeaderboard'])->name('leaderboard.show');

// Public Routes
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// Protected Admin Routes (requires authentication)
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/profile', [AdminProfileController::class, 'index'])->name('admin.profile');
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::get('/admin/leaderboards', [LeaderboardController::class, 'showLeaderboard'])->name('admin.leaderboards');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});


Route::get('admin/register', [AdminAuthController::class, 'showRegistrationForm'])->name('admin.register');
Route::post('admin/register', [AdminAuthController::class, 'register']);

Route::get('admin/verify', [AdminAuthController::class, 'showVerificationForm'])->name('admin.verify');
Route::post('admin/verify', [AdminAuthController::class, 'verify']);
Route::post('admin/resend', [AdminAuthController::class, 'resendVerificationCode'])->name('admin.resend');