<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\Admin\PasswordResetLinkController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SettingController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/berita', [PostController::class, 'index'])
    ->name('posts.index');

Route::get('/berita/{slug}', [PostController::class, 'show'])
    ->name('posts.show');

Route::get('/halaman/{slug}', [PageController::class, 'show'])
    ->name('pages.show');

Route::get('/agenda', [EventController::class, 'index'])
    ->name('events.index');

Route::get('/galeri', [GalleryController::class, 'index'])
    ->name('galleries.index');

Route::get('/galeri/{id}', [GalleryController::class, 'show'])
    ->name('galleries.show');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class)
    ->except(['show', 'destroy']);

    Route::patch('users/{user}/deactivate', [UserController::class, 'deactivate'])
    ->name('users.deactivate');

    Route::patch('users/{user}/activate', [UserController::class, 'activate'])
    ->name('users.activate');

    Route::post('users/{user}/reset-link', [PasswordResetLinkController::class, 'store'])
    ->name('users.reset-link');

    Route::resource('posts', AdminPostController::class)
    ->except(['show']);

    Route::resource('pages', AdminPageController::class)
    ->except(['show']);

    Route::resource('events', AdminEventController::class)
    ->except(['show']);

    Route::resource('galleries', AdminGalleryController::class)
    ->except(['show']);

    Route::post('galleries/{gallery}/photos', [GalleryController::class, 'storePhotos'])
    ->name('galleries.photos.store');

    Route::delete('gallery-photos/{photo}', [GalleryController::class, 'destroyPhoto'])
    ->name('galleries.photos.destroy');

    Route::resource('banners', BannerController::class)
    ->except(['show']);

    Route::get('settings', [SettingController::class, 'edit'])
    ->name('settings.edit');

    Route::put('settings', [SettingController::class, 'update'])
    ->name('settings.update');
});

Route::middleware('throttle:6,1')->group(function () {
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])
    ->name('password-reset.show');

    Route::post('/reset-password/{token}', [ResetPasswordController::class, 'update'])
    ->name('password-reset.update');
});

Route::middleware('auth')->get('/profile', function () {
    return view('profile.edit');
})->name('profile.edit');
