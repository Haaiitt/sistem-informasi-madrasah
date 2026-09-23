<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\Admin\PasswordResetLinkController;
use App\Http\Controllers\Admin\PostController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class)->except(['show', 'destroy']);
    Route::patch('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::patch('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::post('users/{user}/reset-link', [PasswordResetLinkController::class, 'store'])->name('users.reset-link');
    Route::resource('posts', PostController::class)->except(['show']);
});

Route::middleware('throttle:6,1')->group(function () {
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password-reset.show');
    Route::post('/reset-password/{token}', [ResetPasswordController::class, 'update'])->name('password-reset.update');
});

Route::middleware('auth')->get('/profile', function () {
    return view('profile.edit');
})->name('profile.edit');
