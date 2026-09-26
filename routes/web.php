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
use App\Http\Controllers\Admin\AdmissionWaveController;
use App\Http\Controllers\Admin\ApplicantReviewController;
use App\Http\Controllers\Admin\ConversationController as AdminConversationController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\PublicMessageController as AdminPublicMessageController;

use App\Http\Controllers\Ppdb\ApplicantController;
use App\Http\Controllers\Ppdb\ApplicantDocumentController;
use App\Http\Controllers\Ppdb\ConversationController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GuardianRegistrationController;
use App\Http\Controllers\PublicMessageController;

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

    Route::post('galleries/{gallery}/photos', [AdminGalleryController::class, 'storePhotos'])
    ->name('galleries.photos.store');

    Route::delete('gallery-photos/{photo}', [AdminGalleryController::class, 'destroyPhoto'])
    ->name('galleries.photos.destroy');

    Route::resource('banners', BannerController::class)
    ->except(['show']);

    Route::get('settings', [SettingController::class, 'edit'])
    ->name('settings.edit');

    Route::put('settings', [SettingController::class, 'update'])
    ->name('settings.update');

    Route::resource('admission-waves', AdmissionWaveController::class)
    ->except(['show', 'destroy']);

    Route::patch('admission-waves/{admission_wave}/open', [AdmissionWaveController::class, 'open'])
    ->name('admission-waves.open');

    Route::patch('admission-waves/{admission_wave}/close', [AdmissionWaveController::class, 'close'])
    ->name('admission-waves.close');

    Route::get('admission-waves/{wave}/applicants', [ApplicantReviewController::class, 'index'])
    ->name('admission-waves.applicants');

    Route::patch('admission-waves/{wave}/announce', [ApplicantReviewController::class, 'announce'])
    ->name('admission-waves.announce');

    Route::get('applicants/{applicant}', [ApplicantReviewController::class, 'show'])
    ->name('applicants.show');

    Route::post('applicants/{applicant}/verify', [ApplicantReviewController::class, 'verify'])
    ->name('applicants.verify');

    Route::post('applicants/{applicant}/decide', [ApplicantReviewController::class, 'decide'])
    ->name('applicants.decide');

    Route::post('applicants/{applicant}/revise-decision', [ApplicantReviewController::class, 'reviseDecision'])
    ->name('applicants.revise-decision');

    Route::post('applicants/{applicant}/reenrollment', [ApplicantReviewController::class, 'recordReenrollment'])
    ->name('applicants.reenrollment');

    Route::post('applicants/{applicant}/convert', [ApplicantReviewController::class, 'convert'])
    ->name('applicants.convert');

    Route::get('admission-waves/{wave}/applicants/export', [ApplicantReviewController::class, 'export'])
    ->name('admission-waves.applicants.export');

    Route::get('pesan', [AdminConversationController::class, 'index'])
    ->name('conversations.index');

    Route::get('pesan/baru', [AdminConversationController::class, 'create'])
    ->name('conversations.create');

    Route::post('pesan', [AdminConversationController::class, 'store'])
    ->name('conversations.store');

    Route::get('pesan/{conversation}', [AdminConversationController::class, 'show'])
    ->name('conversations.show');

    Route::post('pesan/{conversation}/balas', [AdminConversationController::class, 'reply'])
    ->name('conversations.reply');

    Route::get('pengumuman', [AnnouncementController::class, 'index'])
    ->name('announcements.index');

    Route::get('pengumuman/baru', [AnnouncementController::class, 'create'])
    ->name('announcements.create');

    Route::post('pengumuman', [AnnouncementController::class, 'store'])
    ->name('announcements.store');

    Route::get('pesan-publik', [AdminPublicMessageController::class, 'index'])
    ->name('public-messages.index');

    Route::post('pesan-publik/{publicMessage}/tangani', [AdminPublicMessageController::class, 'handle'])
    ->name('public-messages.handle');
});

Route::middleware('throttle:6,1')->group(function () {
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])
    ->name('password-reset.show');

    Route::post('/reset-password/{token}', [ResetPasswordController::class, 'update'])
    ->name('password-reset.update');
});

Route::middleware('throttle:10,1')->group(function () {
    Route::get('/ppdb/daftar-akun', [GuardianRegistrationController::class, 'create'])
    ->name('ppdb.register');

    Route::post('/ppdb/daftar-akun', [GuardianRegistrationController::class, 'store']);
});

Route::middleware('throttle:10,1')->group(function () {
    Route::get('/kontak', [PublicMessageController::class, 'create'])->name('contact.create');
    Route::post('/kontak', [PublicMessageController::class, 'store'])->name('contact.store');
});

Route::middleware(['auth'])->prefix('ppdb')->name('ppdb.')->group(function () {
    Route::get('pendaftaran', [ApplicantController::class, 'index'])
    ->name('applicants.index');

    Route::get('pendaftaran/baru', [ApplicantController::class, 'create'])
    ->name('applicants.create');

    Route::post('pendaftaran', [ApplicantController::class, 'store'])
    ->name('applicants.store');

    Route::get('pendaftaran/{applicant}', [ApplicantController::class, 'edit'])
    ->name('applicants.edit');

    Route::put('pendaftaran/{applicant}', [ApplicantController::class, 'update'])
    ->name('applicants.update');

    Route::middleware('throttle:10,1')->post('cek-nisn', [ApplicantController::class, 'checkNisn'])
    ->name('applicants.check-nisn');

    Route::post('pendaftaran/{applicant}/berkas', [ApplicantDocumentController::class, 'store'])
    ->name('applicants.documents.store');

    Route::get('berkas/{document}', [ApplicantDocumentController::class, 'show'])
    ->name('applicants.documents.show');

    Route::post('pendaftaran/{applicant}/kirim', [ApplicantController::class, 'submit'])
    ->name('applicants.submit');

    Route::get('pendaftaran/{applicant}/bukti', [ApplicantController::class, 'receipt'])
    ->name('applicants.receipt');

    Route::get('pesan', [ConversationController::class, 'index'])
    ->name('conversations.index');

    Route::get('pesan/baru', [ConversationController::class, 'create'])
    ->name('conversations.create');

    Route::post('pesan', [ConversationController::class, 'store'])
    ->name('conversations.store');

    Route::get('pesan/{conversation}', [ConversationController::class, 'show'])
    ->name('conversations.show');

    Route::post('pesan/{conversation}/balas', [ConversationController::class, 'reply'])
    ->name('conversations.reply');
});

Route::middleware('auth')->get('/profile', function () {
    return view('profile.edit');
})->name('profile.edit');
