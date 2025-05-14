<?php

use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/about', [App\Http\Controllers\AboutController::class, 'index'])->name('about');
Route::get('/about/team', [App\Http\Controllers\TeamController::class, 'index'])->name('team');
Route::get('/about/team/{slug}', [App\Http\Controllers\TeamController::class, 'show'])->name('team.show');
Route::get('/services', [App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [App\Http\Controllers\ServiceController::class, 'show'])->name('services.show');
Route::get('/portfolio', [App\Http\Controllers\PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [App\Http\Controllers\PortfolioController::class, 'show'])->name('portfolio.show');
Route::get('/blog', [App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// Contact & Quotation Routes
Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
Route::get('/quotation', [App\Http\Controllers\QuotationController::class, 'index'])->name('quotation.index');
Route::post('/quotation', [App\Http\Controllers\QuotationController::class, 'store'])->name('quotation.store');
Route::get('/quotation/thank-you', [App\Http\Controllers\QuotationController::class, 'thankYou'])->name('quotation.thank-you');

// Messages
Route::post('/messages', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');

// Authentication Routes
require __DIR__.'/auth.php';

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Portfolio Management
    Route::resource('projects', App\Http\Controllers\Admin\ProjectController::class);
    Route::patch('projects/{project}/toggle-featured', [App\Http\Controllers\Admin\ProjectController::class, 'toggleFeatured'])->name('projects.toggle-featured');
    
    // Service Management
    Route::resource('services', App\Http\Controllers\Admin\ServiceController::class);
    Route::resource('service-categories', App\Http\Controllers\Admin\ServiceCategoryController::class);
    Route::patch('service-categories/{serviceCategory}/toggle-active', [App\Http\Controllers\Admin\ServiceCategoryController::class, 'toggleActive'])->name('service-categories.toggle-active');
    
    // Team Management
    Route::resource('team', App\Http\Controllers\Admin\TeamController::class);
    Route::patch('team/{teamMember}/toggle-active', [App\Http\Controllers\Admin\TeamController::class, 'toggleActive'])->name('team.toggle-active');
    Route::patch('team/{teamMember}/toggle-featured', [App\Http\Controllers\Admin\TeamController::class, 'toggleFeatured'])->name('team.toggle-featured');
    Route::post('team/update-order', [App\Http\Controllers\Admin\TeamController::class, 'updateOrder'])->name('team.update-order');
    
    // Testimonial Management
    Route::resource('testimonials', App\Http\Controllers\Admin\TestimonialController::class);
    Route::patch('testimonials/{testimonial}/toggle-active', [App\Http\Controllers\Admin\TestimonialController::class, 'toggleActive'])->name('testimonials.toggle-active');
    Route::patch('testimonials/{testimonial}/toggle-featured', [App\Http\Controllers\Admin\TestimonialController::class, 'toggleFeatured'])->name('testimonials.toggle-featured');
    
    // Blog Management
    Route::resource('blog', App\Http\Controllers\Admin\BlogController::class);
    Route::resource('blog-categories', App\Http\Controllers\Admin\BlogCategoryController::class);
    Route::patch('blog/{post}/toggle-featured', [App\Http\Controllers\Admin\BlogController::class, 'toggleFeatured'])->name('blog.toggle-featured');
    
    // Certification Management
    Route::resource('certifications', App\Http\Controllers\Admin\CertificationController::class);
    Route::patch('certifications/{certification}/toggle-status', [App\Http\Controllers\Admin\CertificationController::class, 'toggleStatus'])->name('certifications.toggle-status');
    Route::get('certifications/{certification}/download', [App\Http\Controllers\Admin\CertificationController::class, 'download'])->name('certifications.download');
    
    // User Management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::patch('users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle-status');
    Route::get('users/{user}/change-password', [App\Http\Controllers\Admin\UserController::class, 'showChangePasswordForm'])->name('users.change-password');
    Route::post('users/{user}/change-password', [App\Http\Controllers\Admin\UserController::class, 'changePassword'])->name('users.update-password');
    Route::patch('users/{user}/verify-client', [App\Http\Controllers\Admin\UserController::class, 'verifyClient'])->name('users.verify-client');
    
    // Message Management
    Route::resource('messages', App\Http\Controllers\Admin\MessageController::class)->except(['create', 'store', 'edit', 'update']);
    Route::patch('messages/{message}/toggle-read', [App\Http\Controllers\Admin\MessageController::class, 'toggleRead'])->name('messages.toggle-read');
    Route::patch('messages/{message}/mark-as-read', [App\Http\Controllers\Admin\MessageController::class, 'markAsRead'])->name('messages.mark-as-read');
    Route::post('messages/{message}/reply', [App\Http\Controllers\Admin\MessageController::class, 'reply'])->name('messages.reply');
    Route::post('messages/delete-multiple', [App\Http\Controllers\Admin\MessageController::class, 'destroyMultiple'])->name('messages.destroy-multiple');
    
    // Quotation Management
    Route::resource('quotations', App\Http\Controllers\Admin\QuotationController::class);
    Route::patch('quotations/{quotation}/update-status', [App\Http\Controllers\Admin\QuotationController::class, 'updateStatus'])->name('quotations.update-status');
    Route::put('quotations/{quotation}/approve', [App\Http\Controllers\Admin\QuotationController::class, 'approve'])->name('quotations.approve');
    Route::put('quotations/{quotation}/decline', [App\Http\Controllers\Admin\QuotationController::class, 'decline'])->name('quotations.decline');
    Route::post('quotations/{quotation}/send-response', [App\Http\Controllers\Admin\QuotationController::class, 'sendResponse'])->name('quotations.send-response');
    
    // Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::get('settings/seo', [App\Http\Controllers\Admin\SettingController::class, 'seo'])->name('settings.seo');
    Route::put('settings/seo', [App\Http\Controllers\Admin\SettingController::class, 'updateSeo'])->name('settings.update-seo');
    Route::get('settings/company-profile', [App\Http\Controllers\Admin\SettingController::class, 'companyProfile'])->name('settings.company-profile');
    Route::put('settings/company-profile', [App\Http\Controllers\Admin\SettingController::class, 'updateCompanyProfile'])->name('settings.update-company-profile');
    Route::get('settings/email', [App\Http\Controllers\Admin\SettingController::class, 'email'])->name('settings.email');
    Route::put('settings/email', [App\Http\Controllers\Admin\SettingController::class, 'updateEmail'])->name('settings.update-email');
    Route::post('settings/send-test-email', [App\Http\Controllers\Admin\SettingController::class, 'sendTestEmail'])->name('settings.send-test-email');
    Route::post('settings/clear-cache', [App\Http\Controllers\Admin\SettingController::class, 'clearCache'])->name('settings.clear-cache');
});

// Client Routes
Route::prefix('client')->name('client.')->middleware(['auth', 'client', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
    
    // Client Profile
    Route::get('/profile', [App\Http\Controllers\Client\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\Client\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [App\Http\Controllers\Client\ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/profile/change-password', [App\Http\Controllers\Client\ProfileController::class, 'changePassword'])->name('profile.update-password');
    
    // Client Projects
    Route::get('/projects', [App\Http\Controllers\Client\ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [App\Http\Controllers\Client\ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/file/{file}/download', [App\Http\Controllers\Client\ProjectController::class, 'downloadFile'])->name('projects.download-file');
    Route::get('/projects/{project}/testimonial', [App\Http\Controllers\Client\ProjectController::class, 'showTestimonialForm'])->name('projects.testimonial');
    Route::post('/projects/{project}/testimonial', [App\Http\Controllers\Client\ProjectController::class, 'storeTestimonial'])->name('projects.store-testimonial');
    
    // Client Quotations
    Route::resource('quotations', App\Http\Controllers\Client\QuotationController::class)->except(['destroy']);
    Route::get('/quotations/{quotation}/additional-info', [App\Http\Controllers\Client\QuotationController::class, 'showAdditionalInfoForm'])->name('quotations.additional-info');
    Route::put('/quotations/{quotation}/additional-info', [App\Http\Controllers\Client\QuotationController::class, 'updateAdditionalInfo'])->name('quotations.update-additional-info');
    Route::post('/quotations/{quotation}/approve', [App\Http\Controllers\Client\QuotationController::class, 'approve'])->name('quotations.approve');
    Route::post('/quotations/{quotation}/decline', [App\Http\Controllers\Client\QuotationController::class, 'decline'])->name('quotations.decline');
    
    // Client Messages
    Route::resource('messages', App\Http\Controllers\Client\MessageController::class)->except(['destroy', 'edit', 'update']);
    Route::post('/messages/{message}/reply', [App\Http\Controllers\Client\MessageController::class, 'reply'])->name('messages.reply');
    Route::patch('/messages/{message}/mark-as-read', [App\Http\Controllers\Client\MessageController::class, 'markAsRead'])->name('messages.mark-as-read');
    Route::patch('/messages/{message}/mark-as-unread', [App\Http\Controllers\Client\MessageController::class, 'markAsUnread'])->name('messages.mark-as-unread');
});