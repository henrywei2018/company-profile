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
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This is your main routes file where all routes are registered.
| We'll split the routes by section for better organization but keep
| the original structure to maintain compatibility with your application.
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

// Authentication Routes
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
    Route::get('/profile', [App\Http\Controllers\Client\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Client\ProfileController::class, 'destroy'])->name('profile.destroy');        
    Route::post('logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// Admin routes - keeping the original structure for compatibility
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Services management
    Route::resource('services', App\Http\Controllers\Admin\ServiceController::class);
    Route::post('/services/{service}/toggle-status', [ServiceController::class, 'toggleActive'])
    ->name('services.toggle-status');
    Route::patch('/services/{service}/toggle-featured', [App\Http\Controllers\Admin\ServiceController::class, 'toggleFeatured'])->name('services.toggle-featured');
    Route::post('/services/update-order', [App\Http\Controllers\Admin\ServiceController::class, 'updateOrder'])->name('services.update-order');
    
    // Service categories
    Route::resource('service-categories', App\Http\Controllers\Admin\ServiceCategoryController::class);
    Route::patch('/service-categories/{serviceCategory}/toggle-active', [App\Http\Controllers\Admin\ServiceCategoryController::class, 'toggleActive'])->name('service-categories.toggle-active');
    
    // Projects management
    Route::resource('project-categories', App\Http\Controllers\Admin\ProjectCategoryController::class);
    Route::patch('/project-categories/{projectCategory}/toggle-active', [App\Http\Controllers\Admin\ProjectCategoryController::class, 'toggleActive'])->name('project-categories.toggle-active');
    Route::resource('projects', App\Http\Controllers\Admin\ProjectController::class);
    Route::patch('/projects/{project}/toggle-featured', [App\Http\Controllers\Admin\ProjectController::class, 'toggleFeatured'])->name('projects.toggle-featured');
    Route::post('/projects/update-order', [App\Http\Controllers\Admin\ProjectController::class, 'updateOrder'])->name('projects.update-order');
    
    // Quotations management
    Route::resource('quotations', App\Http\Controllers\Admin\QuotationController::class);
    Route::post('/quotations/{quotation}/update-status', [App\Http\Controllers\Admin\QuotationController::class, 'updateStatus'])->name('quotations.update-status');
    
    // Messages management
    Route::resource('messages', App\Http\Controllers\Admin\MessageController::class)->except(['create', 'store', 'edit', 'update']);
    Route::post('/messages/{message}/reply', [App\Http\Controllers\Admin\MessageController::class, 'reply'])->name('messages.reply');
    Route::post('/messages/{message}/toggle-read', [App\Http\Controllers\Admin\MessageController::class, 'toggleRead'])->name('messages.toggle-read');
    Route::post('/messages/mark-read', [App\Http\Controllers\Admin\MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::delete('/messages/delete-multiple', [App\Http\Controllers\Admin\MessageController::class, 'destroyMultiple'])->name('messages.destroy-multiple');
    
    // Team management
    Route::resource('team', App\Http\Controllers\Admin\TeamController::class);
    Route::post('/team/{teamMember}/toggle-active', [App\Http\Controllers\Admin\TeamController::class, 'toggleActive'])->name('team.toggle-active');
    Route::post('/team/{teamMember}/toggle-featured', [App\Http\Controllers\Admin\TeamController::class, 'toggleFeatured'])->name('team.toggle-featured');
    Route::post('/team/update-order', [App\Http\Controllers\Admin\TeamController::class, 'updateOrder'])->name('team.update-order');
    
    // Team department routes
    Route::resource('team-member-departments', App\Http\Controllers\Admin\TeamMemberDepartmentController::class);
    Route::patch('/team-member-departments/{teamMemberDepartment}/toggle-active', [App\Http\Controllers\Admin\TeamMemberDepartmentController::class, 'toggleActive'])->name('team-member-departments.toggle-active');
    Route::post('/team-member-departments/update-order', [App\Http\Controllers\Admin\TeamMemberDepartmentController::class, 'updateOrder'])->name('team-member-departments.update-order');
    // Testimonials management
    Route::resource('testimonials', App\Http\Controllers\Admin\TestimonialController::class);
    Route::post('/testimonials/{testimonial}/toggle-active', [App\Http\Controllers\Admin\TestimonialController::class, 'toggleActive'])->name('testimonials.toggle-active');
    Route::post('/testimonials/{testimonial}/toggle-featured', [App\Http\Controllers\Admin\TestimonialController::class, 'toggleFeatured'])->name('testimonials.toggle-featured');
    
    // Certifications management
    Route::resource('certifications', App\Http\Controllers\Admin\CertificationController::class);
    Route::post('/certifications/{certification}/toggle-active', [App\Http\Controllers\Admin\CertificationController::class, 'toggleActive'])->name('certifications.toggle-active');
    Route::post('/certifications/update-order', [App\Http\Controllers\Admin\CertificationController::class, 'updateOrder'])->name('certifications.update-order');
    
    // Blog management
    Route::get('/blog', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create', [App\Http\Controllers\Admin\BlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [App\Http\Controllers\Admin\BlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{post}', [App\Http\Controllers\Admin\BlogController::class, 'show'])->name('blog.show');
    Route::get('/blog/{post}/edit', [App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{post}', [App\Http\Controllers\Admin\BlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [App\Http\Controllers\Admin\BlogController::class, 'destroy'])->name('blog.destroy');
    Route::post('/blog/{post}/toggle-featured', [App\Http\Controllers\Admin\BlogController::class, 'toggleFeatured'])->name('blog.toggle-featured');
    Route::post('/blog/{post}/change-status', [App\Http\Controllers\Admin\BlogController::class, 'changeStatus'])->name('blog.change-status');
    
    // Blog categories
    Route::resource('blog/categories', App\Http\Controllers\Admin\BlogCategoryController::class)->names('blog.categories');
        
    // Company profile
    Route::get('/company-profile', [App\Http\Controllers\Admin\CompanyProfileController::class, 'index'])->name('company-profile.index');
    Route::put('/company-profile', [App\Http\Controllers\Admin\CompanyProfileController::class, 'update'])->name('company-profile.update');
    Route::get('/company-profile/seo', [App\Http\Controllers\Admin\CompanyProfileController::class, 'seo'])->name('company-profile.seo');
    Route::put('/company-profile/seo', [App\Http\Controllers\Admin\CompanyProfileController::class, 'updateSeo'])->name('company-profile.seo.update');
    // Company Profile (Alias routes for sidebar navigation)
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/edit', [App\Http\Controllers\Admin\CompanyProfileController::class, 'index'])->name('edit');
    });

    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::get('/settings/email', [App\Http\Controllers\Admin\SettingController::class, 'email'])->name('settings.email');
    Route::post('/settings/email', [App\Http\Controllers\Admin\SettingController::class, 'updateEmail'])->name('settings.email.update');
    Route::post('/settings/email/test', [App\Http\Controllers\Admin\SettingController::class, 'sendTestEmail'])->name('settings.email.test');
    Route::get('/settings/seo', [App\Http\Controllers\Admin\SettingController::class, 'seo'])->name('settings.seo');
    Route::post('/settings/seo', [App\Http\Controllers\Admin\SettingController::class, 'updateSeo'])->name('settings.seo.update');
    
    // Users management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('/users/{user}/toggle-active', [App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::get('/users/{user}/change-password', [App\Http\Controllers\Admin\UserController::class, 'showChangePasswordForm'])->name('users.password.form');
    Route::post('/users/{user}/change-password', [App\Http\Controllers\Admin\UserController::class, 'changePassword'])->name('users.password.update');
    Route::post('/users/{user}/verify', [App\Http\Controllers\Admin\UserController::class, 'verifyClient'])->name('users.verify');
});

// Client routes
Route::prefix('client')->name('client.')->middleware(['auth', 'client'])->group(function () {
    // Dashboard
    Route::get('/', [ClientDashboardController::class, 'index'])->name('dashboard');
    
    // Projects
    Route::get('/projects', [App\Http\Controllers\Client\ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [App\Http\Controllers\Client\ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/testimonial', [App\Http\Controllers\Client\ProjectController::class, 'showTestimonialForm'])->name('projects.testimonial.create');
    Route::post('/projects/{project}/testimonial', [App\Http\Controllers\Client\ProjectController::class, 'storeTestimonial'])->name('projects.testimonial.store');
    Route::get('/projects/{project}/files/{file}/download', [App\Http\Controllers\Client\ProjectController::class, 'downloadFile'])->name('projects.files.download');
    
    // Quotations
    Route::get('/quotations', [App\Http\Controllers\Client\QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [App\Http\Controllers\Client\QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [App\Http\Controllers\Client\QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [App\Http\Controllers\Client\QuotationController::class, 'show'])->name('quotations.show');
    Route::get('/quotations/{quotation}/additional-info', [App\Http\Controllers\Client\QuotationController::class, 'showAdditionalInfoForm'])->name('quotations.additional-info.form');
    Route::post('/quotations/{quotation}/additional-info', [App\Http\Controllers\Client\QuotationController::class, 'updateAdditionalInfo'])->name('quotations.additional-info.update');
    Route::get('/quotations/{quotation}/attachments/{attachmentId}/download', [App\Http\Controllers\Client\QuotationController::class, 'downloadAttachment'])->name('quotations.attachments.download');
    Route::post('/quotations/{quotation}/approve', [App\Http\Controllers\Client\QuotationController::class, 'approve'])->name('quotations.approve');
    Route::get('/quotations/{quotation}/decline', [App\Http\Controllers\Client\QuotationController::class, 'showDeclineForm'])->name('quotations.decline.form');
    Route::post('/quotations/{quotation}/decline', [App\Http\Controllers\Client\QuotationController::class, 'decline'])->name('quotations.decline');
    
    // Messages routes
    Route::get('/messages', [App\Http\Controllers\Client\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [App\Http\Controllers\Client\MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [App\Http\Controllers\Client\MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [App\Http\Controllers\Client\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [App\Http\Controllers\Client\MessageController::class, 'reply'])->name('messages.reply');
    Route::post('/messages/{message}/mark-read', [App\Http\Controllers\Client\MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::post('/messages/{message}/mark-unread', [App\Http\Controllers\Client\MessageController::class, 'markAsUnread'])->name('messages.mark-unread');
    Route::get('/messages/{message}/attachments/{attachmentId}/download', [App\Http\Controllers\Client\MessageController::class, 'downloadAttachment'])->name('messages.attachments.download');
    
    // Profile
    Route::get('/profile', [App\Http\Controllers\Client\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\Client\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [App\Http\Controllers\Client\ProfileController::class, 'showChangePasswordForm'])->name('profile.password.form');
    Route::post('/profile/change-password', [App\Http\Controllers\Client\ProfileController::class, 'changePassword'])->name('profile.password.update');
});
