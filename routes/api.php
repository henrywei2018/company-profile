<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use App\Http\Controllers\Api\{
    ProjectController, ServiceController, PostController,
    ContactController, QuotationController, NotificationController
};
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Rate Limiters
|--------------------------------------------------------------------------
*/
RateLimiter::for('client-api', fn(Request $request) =>
    Limit::perMinute(100)->by($request->user()?->id ?: $request->ip())
);
RateLimiter::for('admin-api', fn(Request $request) =>
    Limit::perMinute(120)->by($request->user()?->id ?: $request->ip())
);

/*
|--------------------------------------------------------------------------
| Authenticated User Endpoint
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());

/*
|--------------------------------------------------------------------------
| Notification API
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    Route::get('statistics', [NotificationController::class, 'statistics']);
    Route::get('types', [NotificationController::class, 'types']);
    Route::post('test', [NotificationController::class, 'test']);
    Route::post('send', [NotificationController::class, 'send']);
    Route::post('clear-cache', [NotificationController::class, 'clearCache']);
});


// Project
Route::prefix('projects')->name('api.projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/featured', [ProjectController::class, 'featured'])->name('featured');
    Route::get('/categories', [ProjectController::class, 'categories'])->name('categories');
    Route::get('/years', [ProjectController::class, 'years'])->name('years');
    Route::get('/{slug}', [ProjectController::class, 'show'])->name('show');
    Route::get('/{slug}/related', [ProjectController::class, 'related'])->name('related');
});

// Services
Route::prefix('services')->name('api.services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/featured', [ServiceController::class, 'featured'])->name('featured');
    Route::get('/categories', [ServiceController::class, 'categories'])->name('categories');
    Route::get('/{slug}', [ServiceController::class, 'show'])->name('show');
    Route::get('/{slug}/related', [ServiceController::class, 'related'])->name('related');
});

// Blog posts
Route::prefix('posts')->name('api.posts.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/recent', [PostController::class, 'recent'])->name('recent');
    Route::get('/featured', [PostController::class, 'featured'])->name('featured');
    Route::get('/categories', [PostController::class, 'categories'])->name('categories');
    Route::get('/archives', [PostController::class, 'archives'])->name('archives');
    Route::get('/{slug}', [PostController::class, 'show'])->name('show');
    Route::get('/{slug}/related', [PostController::class, 'related'])->name('related');
});

// Public Contact and Quotation
Route::post('/contact', [ContactController::class, 'store'])->name('api.contact');
Route::post('/quotation', [QuotationController::class, 'store'])->name('api.quotation');

Route::prefix('chat')->group(function () {
    Route::get('/online-status', [ChatController::class, 'onlineStatus'])->name('api.chat.online-status');
});

Route::prefix('chat')->middleware(['auth'])->group(function () {
    Route::post('/start', [ChatController::class, 'start'])->name('api.chat.start');
    Route::get('/session', [ChatController::class, 'getSession'])->name('api.chat.session');
    Route::post('/send-message', [ChatController::class, 'sendMessage'])
        ->middleware('throttle:30,1')
        ->name('api.chat.send-message');
    Route::get('/messages', [ChatController::class, 'getMessages'])->name('api.chat.messages');
    Route::post('/typing', [ChatController::class, 'sendTyping'])
        ->middleware('throttle:60,1')
        ->name('api.chat.typing');
    Route::post('/close', [ChatController::class, 'close'])->name('api.chat.close');
    Route::get('/online-status', [ChatController::class, 'onlineStatus'])->name('api.chat.online-status');
});
Route::prefix('admin/chat')
    ->middleware(['auth:sanctum,web', 'admin']) // Support both Sanctum and Web auth
    ->name('api.admin.chat.')
    ->group(function () {
        Route::get('/sessions', [ChatController::class, 'getAdminSessions'])->name('sessions');
        Route::get('/statistics', [ChatController::class, 'getStatistics'])->name('statistics');
        Route::post('/{chatSession}/reply', [ChatController::class, 'reply'])
            ->middleware('throttle:60,1')
            ->name('reply');
        Route::post('/{chatSession}/assign', [ChatController::class, 'assignToMe'])->name('assign');
        Route::post('/{chatSession}/close', [ChatController::class, 'closeSession'])->name('close');
        Route::post('/{chatSession}/typing', [ChatController::class, 'operatorTyping'])
            ->middleware('throttle:60,1')
            ->name('typing');
        Route::post('/operator/status', [ChatController::class, 'setOperatorStatus'])->name('operator-status');
        Route::get('/operator/status', [ChatController::class, 'getOperatorStatus'])->name('get-operator-status');
        Route::get('/operators/available', [ChatController::class, 'getAvailableOperators'])->name('operators.available');
        Route::get('/{chatSession}/messages', [ChatController::class, 'getChatMessages'])->name('messages');
    });
