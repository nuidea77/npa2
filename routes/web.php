<?php

use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\EventsController;
use App\Http\Controllers\Admin\InboxController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StampsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API (session-д суурилсан, Vue SPA-с дуудагдана)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {

    // --- Нийтэд нээлттэй ---
    Route::get('/home', [PublicController::class, 'home']);
    Route::get('/settings', [PublicController::class, 'settings']);
    Route::get('/parks', [PublicController::class, 'parks']);
    Route::get('/parks/{id}', [PublicController::class, 'park']);
    Route::get('/orgs', [PublicController::class, 'orgs']);
    Route::get('/news', [PublicController::class, 'news']);
    Route::get('/news/{id}', [PublicController::class, 'newsOne']);
    Route::get('/jobs', [PublicController::class, 'jobs']);
    Route::get('/jobs/{id}', [PublicController::class, 'job']);
    Route::get('/faqs', [PublicController::class, 'faqs']);
    Route::get('/events', [PublicController::class, 'events']);
    Route::get('/events/{id}', [PublicController::class, 'event']);

    Route::post('/feedback', [FormController::class, 'feedback']);
    Route::get('/volunteer/search', [FormController::class, 'volunteerSearch']);
    Route::post('/volunteer/request', [FormController::class, 'volunteerRequest']);
    Route::post('/events/{id}/register', [FormController::class, 'eventRegister']);

    // --- Нэвтрэлт ---
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/forgot', [AuthController::class, 'forgot']);
    Route::get('/me', [AuthController::class, 'me']);

    // --- Нэвтэрсэн хэрэглэгч ---
    Route::middleware('auth')->group(function () {
        Route::put('/me', [AuthController::class, 'updateMe']);
        Route::post('/me/photo', [AuthController::class, 'uploadPhoto']);
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/stamps/{id}', [DashboardController::class, 'stampDetail']);
    });

    // --- Админ панел (CMS) ---
    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
        Route::get('/stats', [SettingsController::class, 'stats']);

        // Хэрэглэгчид
        Route::get('/users', [UsersController::class, 'index']);
        Route::post('/users/{id}/approve', [UsersController::class, 'approve']);
        Route::post('/users/{id}/reject', [UsersController::class, 'reject']);
        Route::put('/users/{id}', [UsersController::class, 'update']);
        Route::delete('/users/{id}', [UsersController::class, 'destroy']);
        Route::post('/users/{id}/reset-password', [UsersController::class, 'resetPassword']);

        // Мэдэгдэл, и-мэйл лог
        Route::get('/notifications', [UsersController::class, 'notifications']);
        Route::post('/notifications/{id}/read', [UsersController::class, 'markNotificationRead']);
        Route::post('/notifications/read-all', [UsersController::class, 'markAllNotificationsRead']);
        Route::get('/outbox', [UsersController::class, 'outbox']);

        // Агуулга: мэдээ, ажлын байр, ХЗ, ТХГ, FAQ, сургалт
        Route::get('/news', [ContentController::class, 'newsIndex']);
        Route::post('/news', [ContentController::class, 'newsSave']);
        Route::delete('/news/{id}', [ContentController::class, 'newsDelete']);

        Route::get('/jobs', [ContentController::class, 'jobsIndex']);
        Route::post('/jobs', [ContentController::class, 'jobsSave']);
        Route::delete('/jobs/{id}', [ContentController::class, 'jobsDelete']);

        Route::get('/orgs', [ContentController::class, 'orgsIndex']);
        Route::post('/orgs', [ContentController::class, 'orgsSave']);
        Route::delete('/orgs/{id}', [ContentController::class, 'orgsDelete']);

        Route::get('/parks', [ContentController::class, 'parksIndex']);
        Route::post('/parks', [ContentController::class, 'parksSave']);
        Route::delete('/parks/{id}', [ContentController::class, 'parksDelete']);

        Route::get('/faqs', [ContentController::class, 'faqIndex']);
        Route::post('/faqs', [ContentController::class, 'faqSave']);
        Route::delete('/faqs/{id}', [ContentController::class, 'faqDelete']);

        Route::get('/trainings', [ContentController::class, 'trainingsIndex']);
        Route::post('/trainings', [ContentController::class, 'trainingsSave']);
        Route::delete('/trainings/{id}', [ContentController::class, 'trainingsDelete']);

        // NPA тамга
        Route::get('/orgs/{org}/users', [UsersController::class, 'byOrg']);
        Route::get('/stamps', [StampsController::class, 'index']);
        Route::post('/stamps', [StampsController::class, 'store']);
        Route::get('/stamps/{id}', [StampsController::class, 'show']);
        Route::put('/stamps/{id}', [StampsController::class, 'update']);
        Route::post('/stamps/{id}/delete', [StampsController::class, 'destroy']);
        Route::post('/stamp-years', [StampsController::class, 'yearsSave']);
        Route::delete('/stamp-years/{id}', [StampsController::class, 'yearsDelete']);

        // Сургалт, арга хэмжээ (асуулга)
        Route::get('/events', [EventsController::class, 'index']);
        Route::post('/events', [EventsController::class, 'save']);
        Route::delete('/events/{id}', [EventsController::class, 'delete']);
        Route::get('/events/{id}/registrations', [EventsController::class, 'registrations']);

        // Санал хүсэлт, сайн дурын хүсэлтүүд
        Route::get('/feedback', [InboxController::class, 'feedback']);
        Route::put('/feedback/{id}', [InboxController::class, 'feedbackUpdate']);
        Route::delete('/feedback/{id}', [InboxController::class, 'feedbackDelete']);
        Route::get('/volunteers', [InboxController::class, 'volunteers']);
        Route::put('/volunteers/{id}', [InboxController::class, 'volunteerUpdate']);

        // Тохиргоо
        Route::get('/settings', [SettingsController::class, 'index']);
        Route::post('/settings', [SettingsController::class, 'save']);

        // Супер админ: админ эрхийн удирдлага
        Route::middleware('admin:superadmin')->group(function () {
            Route::get('/admins', [SettingsController::class, 'admins']);
            Route::post('/users/{id}/role', [SettingsController::class, 'setRole']);
        });
    });
});

/*
|--------------------------------------------------------------------------
| SPA (Vue) — бусад бүх зам Vue Router-т очно
|--------------------------------------------------------------------------
*/
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api|storage|build).*$');
