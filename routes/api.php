<?php
// File: routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\QuotationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
RateLimiter::for('client-api', fn(Request $request) =>
    Limit::perMinute(100)->by($request->user()?->id ?: $request->ip())
);

RateLimiter::for('admin-api', fn(Request $request) =>
    Limit::perMinute(120)->by($request->user()?->id ?: $request->ip())
);
// Authentication routes if needed
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->prefix('notifications')->group(function () {
    Route::get('statistics', [NotificationController::class, 'statistics']);
    Route::get('types', [NotificationController::class, 'types']);
    Route::post('test', [NotificationController::class, 'test']);
    Route::post('send', [NotificationController::class, 'send']);
    Route::post('clear-cache', [NotificationController::class, 'clearCache']);
});

// Project routes
Route::prefix('projects')->name('api.projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/featured', [ProjectController::class, 'featured'])->name('featured');
    Route::get('/categories', [ProjectController::class, 'categories'])->name('categories');
    Route::get('/years', [ProjectController::class, 'years'])->name('years');
    Route::get('/{slug}', [ProjectController::class, 'show'])->name('show');
    Route::get('/{slug}/related', [ProjectController::class, 'related'])->name('related');
});

// Service routes
Route::prefix('services')->name('api.services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/featured', [ServiceController::class, 'featured'])->name('featured');
    Route::get('/categories', [ServiceController::class, 'categories'])->name('categories');
    Route::get('/{slug}', [ServiceController::class, 'show'])->name('show');
    Route::get('/{slug}/related', [ServiceController::class, 'related'])->name('related');
});

// Blog post routes
Route::prefix('posts')->name('api.posts.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/recent', [PostController::class, 'recent'])->name('recent');
    Route::get('/featured', [PostController::class, 'featured'])->name('featured');
    Route::get('/categories', [PostController::class, 'categories'])->name('categories');
    Route::get('/archives', [PostController::class, 'archives'])->name('archives');
    Route::get('/{slug}', [PostController::class, 'show'])->name('show');
    Route::get('/{slug}/related', [PostController::class, 'related'])->name('related');
});

Route::prefix('chat')->middleware(['auth:sanctum'])->group(function () {
    // Session management
    Route::post('/start', [ChatController::class, 'start'])->name('api.chat.start');
    Route::get('/session', [ChatController::class, 'getSession'])->name('api.chat.session');
    Route::post('/close', [ChatController::class, 'close'])->name('api.chat.close');
    
    // Messaging
    Route::post('/send-message', [ChatController::class, 'sendMessage'])
        ->middleware('throttle:30,1')
        ->name('api.chat.send-message');
    
    // Typing indicators
    Route::post('/typing', [ChatController::class, 'sendTyping'])
        ->middleware('throttle:60,1')
        ->name('api.chat.typing');
    
    // Status
    Route::get('/online-status', [ChatController::class, 'onlineStatus'])->name('api.chat.online-status');
});

Route::prefix('chat')->group(function () {
    Route::get('/status', [ChatController::class, 'onlineStatus'])->name('api.chat.public-status');
});

Route::prefix('admin/chat')->middleware(['auth:sanctum', 'role:admin|super-admin'])->group(function () {
    // Session management
    Route::get('/sessions', [ChatController::class, 'getAdminSessions'])->name('api.admin.chat.sessions');
    Route::get('/statistics', [ChatController::class, 'getStatistics'])->name('api.admin.chat.statistics');
    
    // Session actions
    Route::post('/{chatSession}/reply', [ChatController::class, 'reply'])
        ->middleware('throttle:60,1')
        ->name('api.admin.chat.reply');
    Route::post('/{chatSession}/assign', [ChatController::class, 'assignToMe'])->name('api.admin.chat.assign');
    Route::post('/{chatSession}/close', [ChatController::class, 'closeSession'])->name('api.admin.chat.close');
    Route::post('/{chatSession}/typing', [ChatController::class, 'operatorTyping'])
        ->middleware('throttle:60,1')
        ->name('api.admin.chat.typing');
    
    // Operator management
    Route::post('/operator/status', [ChatController::class, 'setOperatorStatus'])->name('api.admin.chat.operator-status');
    Route::get('/operator/status', [ChatController::class, 'getOperatorStatus'])->name('api.admin.chat.get-operator-status');
});

// Contact and Quotation routes
Route::post('/contact', [ContactController::class, 'store'])->name('api.contact');
Route::post('/quotation', [QuotationController::class, 'store'])->name('api.quotation');

// Protected routes (if needed)
Route::middleware('auth:sanctum')->group(function () {
    // Protected API endpoints here
});