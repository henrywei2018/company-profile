<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController as ProjectAdminController;
use App\Http\Controllers\Admin\QuotationController as QuotationAdminController;
use App\Http\Controllers\Admin\MessageController as MessageAdminController;
use App\Http\Controllers\Admin\TeamController as TeamAdminController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use App\Http\Controllers\Client\ProfileController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// About routes
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/about/team', [AboutController::class, 'team'])->name('about.team');

// Services routes
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');


// Alternative portfolio routes if needed
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [PortfolioController::class, 'show'])->name('portfolio.show');

// Team routes
Route::get('/team', [TeamController::class, 'index'])->name('team.index');
Route::get('/team/{slug}', [TeamController::class, 'show'])->name('team.show');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Quotation
Route::get('/quotation', [QuotationController::class, 'create'])->name('quotation.create');
Route::post('/quotation', [QuotationController::class, 'store'])->name('quotation.store');
Route::get('/quotation/thank-you', [QuotationController::class, 'thankYou'])->name('quotation.thank-you');

// Messages
Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

// Authentication routes
Auth::routes(['register' => false]); // Disable registration if not needed

// Client routes
Route::prefix('client')->name('client.')->middleware(['auth', 'client'])->group(function () {
    // Dashboard
    Route::get('/', [ClientDashboardController::class, 'index'])->name('dashboard');
    
    // Projects
    Route::get('/projects', [ClientProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ClientProjectController::class, 'show'])->name('projects.show');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Projects management
    Route::resource('projects', ProjectAdminController::class);
    Route::post('/projects/{project}/images', [ProjectAdminController::class, 'storeImage'])->name('projects.images.store');
    Route::delete('/projects/{project}/images/{image}', [ProjectAdminController::class, 'destroyImage'])->name('projects.images.destroy');
    Route::post('/projects/{project}/files', [ProjectAdminController::class, 'storeFile'])->name('projects.files.store');
    Route::delete('/projects/{project}/files/{file}', [ProjectAdminController::class, 'destroyFile'])->name('projects.files.destroy');
    
    // Quotations management
    Route::resource('quotations', QuotationAdminController::class);
    Route::get('/quotations/{quotation}/download', [QuotationAdminController::class, 'download'])->name('quotations.download');
    
    // Messages management
    Route::resource('messages', MessageAdminController::class)->except(['create', 'store', 'edit', 'update']);
    Route::post('/messages/{message}/reply', [MessageAdminController::class, 'reply'])->name('messages.reply');
    
    // Team management
    Route::resource('team', TeamAdminController::class);
    
    // Testimonials management
    Route::resource('testimonials', TestimonialController::class);
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    
    // Users management
    Route::resource('users', UserController::class);
});
