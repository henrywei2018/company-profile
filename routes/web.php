<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    PortfolioController,
    QuotationController,
    MessageController,
    BlogController,
    ContactController,
    AboutController,
    ServiceController,
    TeamController
};
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Client\{
    DashboardController as ClientDashboardController,
    ProfileController as ClientProfileController,
    NotificationPreferencesController
};

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
});

Route::prefix('team')->group(function () {
    Route::get('/', [TeamController::class, 'index'])->name('team.index');
    Route::get('/{slug}', [TeamController::class, 'show'])->name('team.show');
});

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');

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
Route::middleware('auth')->group(function () {
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

    Route::prefix('profile')->group(function () {
        Route::get('/', [ClientProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ClientProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ClientProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::post('/api/analytics/track', fn() => response()->json(['success' => true]))
        ->name('api.analytics.track');
});

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
