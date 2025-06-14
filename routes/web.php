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
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
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

Route::prefix('services')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/{slug}', [ServiceController::class, 'show'])->name('services.show');
});

Route::prefix('portfolio')->group(function () {
    Route::get('/', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::get('/{slug}', [PortfolioController::class, 'show'])->name('portfolio.show');
    Route::prefix('projects')->group(function () {
        Route::get('/{slug}', [ProjectController::class, 'show'])->name('portfolio.projects.show');
    });
});


Route::prefix('team')->group(function () {
    Route::get('/', [TeamController::class, 'index'])->name('team.index');
    Route::get('/{slug}', [TeamController::class, 'show'])->name('team.show');
});

Route::prefix('blog')->name('blog.')->group(function () {
    // Main blog page
    Route::get('/', [App\Http\Controllers\BlogController::class, 'index'])->name('index');
    
    // Category pages
    Route::get('/category/{category:slug}', [App\Http\Controllers\BlogController::class, 'category'])->name('category');
    
    // Archive pages
    Route::get('/archive/{year}/{month?}', [App\Http\Controllers\BlogController::class, 'archive'])->name('archive');
    
    // Search
    Route::get('/search', [App\Http\Controllers\BlogController::class, 'search'])->name('search');
    
    // RSS Feed
    Route::get('/feed', [App\Http\Controllers\BlogController::class, 'feed'])->name('feed');
    
    // Individual post (should be last to avoid conflicts)
    Route::get('/{post:slug}', [App\Http\Controllers\BlogController::class, 'show'])->name('show');
});

Route::prefix('contact')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/', [ContactController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('contact.store');
});

Route::prefix('quotation')->group(function () {
    Route::get('/', [QuotationController::class, 'create'])->name('quotation.create');
    Route::post('/', [QuotationController::class, 'store'])->middleware('throttle:5,1')->name('quotation.store');
    Route::get('/thank-you', [QuotationController::class, 'thankYou'])->name('quotation.thank-you');
});

Route::post('/messages', [MessageController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('messages.store');


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
