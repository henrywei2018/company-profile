<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    PortfolioController,
    QuotationController,
    MessageController,
    ProfileController,
    ProjectController,
    BlogController,
    ContactController,
    AboutController,
    ServiceController,
    TeamController,
    ChatController
};
use App\Http\Controllers\Client\{
    DashboardController as ClientDashboardController,
    ProfileController as ClientProfileController,
    NotificationPreferencesController
};
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\UnifiedProfileController;

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('about')->group(function () {
    Route::get('/', [AboutController::class, 'index'])->name('about');
    Route::get('/team', [AboutController::class, 'team'])->name('about.team');
});

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/{service:slug}', [ServiceController::class, 'show'])->name('show'); // ✅ Fix parameter
});

Route::prefix('portfolio')->name('portfolio.')->group(function () {
    Route::get('/', [PortfolioController::class, 'index'])->name('index');
    Route::get('/{project:slug}', [PortfolioController::class, 'show'])->name('show');
    
});


Route::prefix('team')->group(function () {
    Route::get('/', [TeamController::class, 'index'])->name('team.index');
    Route::get('/{slug}', [TeamController::class, 'show'])->name('team.show');
});

Route::prefix('blog')->name('blog.')->group(function () {
    // Main blog page
    Route::get('/', [App\Http\Controllers\BlogController::class, 'index'])->name('index');
    Route::get('/{post:slug}', [App\Http\Controllers\BlogController::class, 'show'])->name('show');
});

Route::prefix('contact')->name('contact.')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('index');
    Route::post('/', [ContactController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('store');
});

Route::prefix('quotation')->group(function () {
    Route::get('/', [QuotationController::class, 'create'])->name('quotation.create');
    Route::post('/', [QuotationController::class, 'store'])->middleware('throttle:5,1')->name('quotation.store');
    Route::get('/thank-you', [QuotationController::class, 'thankYou'])->name('quotation.thank-you');
});

Route::post('/messages', [MessageController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('messages.store');

Route::prefix('api/chat')->group(function () {
    Route::get('/online-status', [App\Http\Controllers\ChatController::class, 'onlineStatus'])->name('api.chat.public-online-status');
    Route::get('/status', [App\Http\Controllers\ChatController::class, 'onlineStatus'])->name('api.chat.public-status');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth','admin')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Prevent redirect loop
        if (request()->routeIs('admin.dashboard') || request()->routeIs('client.dashboard')) {
            abort(404);
        }

        if ($user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('client')) {
            return redirect()->route('client.dashboard');
        }

        abort(403, 'Unauthorized');
    })->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        
        Route::get('/', [UnifiedProfileController::class, 'show'])->name('show');
        Route::get('/edit', [UnifiedProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [UnifiedProfileController::class, 'update'])->name('update');
        Route::get('/change-password', [UnifiedProfileController::class, 'showChangePasswordForm'])->name('change-password');
        Route::patch('/password', [UnifiedProfileController::class, 'updatePassword'])->name('password.update');
        Route::get('/preferences', [UnifiedProfileController::class, 'preferences'])->name('preferences');
        Route::patch('/preferences', [UnifiedProfileController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('/completion', [UnifiedProfileController::class, 'completion'])->name('completion');
        Route::get('/export', [UnifiedProfileController::class, 'export'])->name('export');
        Route::get('/delete', [UnifiedProfileController::class, 'showDeleteForm'])->name('delete');
        Route::delete('/', [UnifiedProfileController::class, 'destroy'])->name('destroy');
        Route::get('/completion-status', [UnifiedProfileController::class, 'completionStatus'])->name('completion-status');
        Route::get('/activity-summary', [UnifiedProfileController::class, 'activitySummary'])->name('activity-summary');
        
    });
    

    Route::get('/chat/notifications', function () {
        $user = auth()->user();
        
        if ($user->hasAdminAccess()) {
            // Get chat notifications for operators
            $notifications = $user->notifications()
                ->where('type', 'like', '%Chat%')
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } else {
            // Get chat notifications for clients
            $notifications = $user->notifications()
                ->where('type', 'like', '%chat%')
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $notifications->count()
        ]);
    })->name('chat.notifications');

    Route::post('/api/analytics/track', fn() => response()->json(['success' => true]))
        ->name('api.analytics.track');

    Route::get('/chat/websocket-auth', function () {
        return response()->json([
            'auth' => auth()->check(),
            'user_id' => auth()->id(),
            'is_admin' => auth()->check() && auth()->user()->hasAdminAccess(),
        ]);
    })->name('chat.websocket-auth');
    
    Route::prefix('api/chat')->group(function () {
        Route::post('/start', [App\Http\Controllers\ChatController::class, 'start'])->name('api.chat.start');
        Route::get('/session', [App\Http\Controllers\ChatController::class, 'getSession'])->name('api.chat.session');
        Route::post('/close', [App\Http\Controllers\ChatController::class, 'close'])->name('api.chat.close');
        Route::post('/send-message', [App\Http\Controllers\ChatController::class, 'sendMessage'])
            ->middleware('throttle:30,1')
            ->name('api.chat.send-message');
        Route::post('/typing', [App\Http\Controllers\ChatController::class, 'sendTyping'])
            ->middleware('throttle:60,1')
            ->name('api.chat.typing');
        Route::get('/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('api.chat.messages');
        Route::post('/update-info', [App\Http\Controllers\ChatController::class, 'updateClientInfo'])->name('api.chat.update-info');
        Route::get('/history', [App\Http\Controllers\ChatController::class, 'history'])->name('api.chat.history');
        Route::get('/online-status', [App\Http\Controllers\ChatController::class, 'onlineStatus'])->name('api.chat.online-status');
    });
});

Route::get('/robots.txt', [RobotsController::class, 'robots'])->name('robots');


/*
|--------------------------------------------------------------------------
| Role-Based Area Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/client.php';
require __DIR__ . '/admin.php';

/*
|--------------------------------------------------------------------------
| Redirect Routes
|--------------------------------------------------------------------------
*/
Route::redirect('/admin', '/admin/dashboard');
Route::redirect('/client', '/client/dashboard');
