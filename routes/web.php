<?php
// File: routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\QuotationController as AdminQuotationController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\CertificationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use App\Http\Controllers\Client\QuotationController as ClientQuotationController;
use App\Http\Controllers\Client\MessageController as ClientMessageController;
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

// Portfolio routes
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [PortfolioController::class, 'show'])->name('portfolio.show');

// Team routes
Route::get('/team', [TeamController::class, 'index'])->name('team.index');
Route::get('/team/{slug}', [TeamController::class, 'show'])->name('team.show');

// Blog routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/archive/{year}/{month?}', [BlogController::class, 'archive'])->name('blog.archive');
Route::get('/blog/search', [BlogController::class, 'search'])->name('blog.search');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Quotation
Route::get('/quotation', [QuotationController::class, 'create'])->name('quotation.create');
Route::post('/quotation', [QuotationController::class, 'store'])->name('quotation.store');
Route::get('/quotation/thank-you', [QuotationController::class, 'thankYou'])->name('quotation.thank-you');

// Messages
Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

Route::middleware('guest')->group(function () {
    Route::get('login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
    
    // Password Reset Routes
    Route::get('forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    Route::get('reset-password/{token}', [App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
        ->name('password.store');    
    Route::get('register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [App\Http\Controllers\Auth\EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');        
    Route::post('logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// Client routes
Route::prefix('client')->name('client.')->middleware(['auth', 'client'])->group(function () {
    // Dashboard
    Route::get('/', [ClientDashboardController::class, 'index'])->name('dashboard');
    
    // Projects
    Route::get('/projects', [ClientProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ClientProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/testimonial', [ClientProjectController::class, 'showTestimonialForm'])->name('projects.testimonial.create');
    Route::post('/projects/{project}/testimonial', [ClientProjectController::class, 'storeTestimonial'])->name('projects.testimonial.store');
    Route::get('/projects/{project}/files/{file}/download', [ClientProjectController::class, 'downloadFile'])->name('projects.files.download');
    
    // Quotations
    Route::get('/quotations', [ClientQuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [ClientQuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [ClientQuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [ClientQuotationController::class, 'show'])->name('quotations.show');
    Route::get('/quotations/{quotation}/additional-info', [ClientQuotationController::class, 'showAdditionalInfoForm'])->name('quotations.additional-info.form');
    Route::post('/quotations/{quotation}/additional-info', [ClientQuotationController::class, 'updateAdditionalInfo'])->name('quotations.additional-info.update');
    Route::get('/quotations/{quotation}/attachments/{attachmentId}/download', [ClientQuotationController::class, 'downloadAttachment'])->name('quotations.attachments.download');
    Route::post('/quotations/{quotation}/approve', [ClientQuotationController::class, 'approve'])->name('quotations.approve');
    Route::get('/quotations/{quotation}/decline', [ClientQuotationController::class, 'showDeclineForm'])->name('quotations.decline.form');
    Route::post('/quotations/{quotation}/decline', [ClientQuotationController::class, 'decline'])->name('quotations.decline');
    
    // Messages
    Route::get('/messages', [ClientMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [ClientMessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [ClientMessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [ClientMessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [ClientMessageController::class, 'reply'])->name('messages.reply');
    Route::get('/messages/{message}/attachments/{attachmentId}/download', [ClientMessageController::class, 'downloadAttachment'])->name('messages.attachments.download');
    Route::post('/messages/{message}/mark-read', [ClientMessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::post('/messages/{message}/mark-unread', [ClientMessageController::class, 'markAsUnread'])->name('messages.mark-unread');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.password.form');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.password.update');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Projects management
    Route::resource('projects', AdminProjectController::class);
    Route::post('/projects/{project}/toggle-featured', [AdminProjectController::class, 'toggleFeatured'])->name('projects.toggle-featured');
    Route::post('/projects/update-order', [AdminProjectController::class, 'updateOrder'])->name('projects.update-order');
    
    // Services management
    Route::resource('services', AdminServiceController::class);
    Route::post('/services/{service}/toggle-active', [AdminServiceController::class, 'toggleActive'])->name('services.toggle-active');
    Route::post('/services/{service}/toggle-featured', [AdminServiceController::class, 'toggleFeatured'])->name('services.toggle-featured');
    Route::post('/services/update-order', [AdminServiceController::class, 'updateOrder'])->name('services.update-order');
    
    // Service categories
    Route::resource('service-categories', ServiceCategoryController::class);
    Route::post('/service-categories/{serviceCategory}/toggle-active', [ServiceCategoryController::class, 'toggleActive'])->name('service-categories.toggle-active');
    
    // Quotations management
    Route::resource('quotations', AdminQuotationController::class);
    Route::post('/quotations/{quotation}/update-status', [AdminQuotationController::class, 'updateStatus'])->name('quotations.update-status');
    
    // Messages management
    Route::resource('messages', AdminMessageController::class)->except(['create', 'store', 'edit', 'update']);
    Route::post('/messages/{message}/reply', [AdminMessageController::class, 'reply'])->name('messages.reply');
    Route::post('/messages/{message}/toggle-read', [AdminMessageController::class, 'toggleRead'])->name('messages.toggle-read');
    Route::post('/messages/mark-read', [AdminMessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::delete('/messages/delete-multiple', [AdminMessageController::class, 'destroyMultiple'])->name('messages.destroy-multiple');
    
    // Team management
    Route::resource('team', AdminTeamController::class);
    Route::post('/team/{teamMember}/toggle-active', [AdminTeamController::class, 'toggleActive'])->name('team.toggle-active');
    Route::post('/team/{teamMember}/toggle-featured', [AdminTeamController::class, 'toggleFeatured'])->name('team.toggle-featured');
    Route::post('/team/update-order', [AdminTeamController::class, 'updateOrder'])->name('team.update-order');
    
    // Testimonials management
    Route::resource('testimonials', TestimonialController::class);
    Route::post('/testimonials/{testimonial}/toggle-active', [TestimonialController::class, 'toggleActive'])->name('testimonials.toggle-active');
    Route::post('/testimonials/{testimonial}/toggle-featured', [TestimonialController::class, 'toggleFeatured'])->name('testimonials.toggle-featured');
    
    // Certifications management
    Route::resource('certifications', CertificationController::class);
    Route::post('/certifications/{certification}/toggle-active', [CertificationController::class, 'toggleActive'])->name('certifications.toggle-active');
    Route::post('/certifications/update-order', [CertificationController::class, 'updateOrder'])->name('certifications.update-order');
    
    // Blog management
    Route::get('/blog', [AdminBlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create', [AdminBlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [AdminBlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{post}', [AdminBlogController::class, 'show'])->name('blog.show');
    Route::get('/blog/{post}/edit', [AdminBlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{post}', [AdminBlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [AdminBlogController::class, 'destroy'])->name('blog.destroy');
    Route::post('/blog/{post}/toggle-featured', [AdminBlogController::class, 'toggleFeatured'])->name('blog.toggle-featured');
    Route::post('/blog/{post}/change-status', [AdminBlogController::class, 'changeStatus'])->name('blog.change-status');
    
    // Blog categories
    Route::resource('blog/categories', BlogCategoryController::class)->names('blog.categories');
        
    // Company profile
    Route::get('/company-profile', [CompanyProfileController::class, 'index'])->name('company-profile.index');
    Route::put('/company-profile', [CompanyProfileController::class, 'update'])->name('company-profile.update');
    Route::get('/company-profile/seo', [CompanyProfileController::class, 'seo'])->name('company-profile.seo');
    Route::put('/company-profile/seo', [CompanyProfileController::class, 'updateSeo'])->name('company-profile.seo.update');
    // Company Profile (Alias routes for sidebar navigation)
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/edit', [CompanyProfileController::class, 'index'])->name('edit');
    });

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/settings/email', [SettingController::class, 'email'])->name('settings.email');
    Route::post('/settings/email', [SettingController::class, 'updateEmail'])->name('settings.email.update');
    Route::post('/settings/email/test', [SettingController::class, 'sendTestEmail'])->name('settings.email.test');
    Route::get('/settings/seo', [SettingController::class, 'seo'])->name('settings.seo');
    Route::post('/settings/seo', [SettingController::class, 'updateSeo'])->name('settings.seo.update');
    
    // Users management
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::get('/users/{user}/change-password', [UserController::class, 'showChangePasswordForm'])->name('users.password.form');
    Route::post('/users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.password.update');
    Route::post('/users/{user}/verify', [UserController::class, 'verifyClient'])->name('users.verify');
});