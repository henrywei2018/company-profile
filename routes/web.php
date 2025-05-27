<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TeamController;
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

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');


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
    
    // RBAC Management - Roles and Permissions
    Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
    Route::get('/roles/{role}/permissions', [App\Http\Controllers\Admin\RoleController::class, 'permissions'])
        ->name('roles.permissions');
    Route::put('/roles/{role}/permissions', [App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])
        ->name('roles.permissions.update');
    Route::get('/roles/{role}/users', [App\Http\Controllers\Admin\RoleController::class, 'users'])
        ->name('roles.users');
    Route::post('/roles/{role}/duplicate', [App\Http\Controllers\Admin\RoleController::class, 'duplicate'])
        ->name('roles.duplicate');

    // Permissions Management
    Route::resource('permissions', App\Http\Controllers\Admin\PermissionController::class);
    Route::get('/permissions/bulk-create', [App\Http\Controllers\Admin\PermissionController::class, 'showBulkCreate'])
        ->name('permissions.bulk-create');
    Route::post('/permissions/bulk-create', [App\Http\Controllers\Admin\PermissionController::class, 'bulkCreate'])
        ->name('permissions.bulk-store');
    Route::get('/permissions/{permission}/roles', [App\Http\Controllers\Admin\PermissionController::class, 'roles'])
        ->name('permissions.roles');

    // RBAC Dashboard/Statistics
    Route::get('/rbac/dashboard', [App\Http\Controllers\Admin\RBACController::class, 'dashboard'])
        ->name('rbac.dashboard');
    Route::get('/rbac/audit-log', [App\Http\Controllers\Admin\RBACController::class, 'auditLog'])
        ->name('rbac.audit-log');
    Route::post('/rbac/clear-cache', [App\Http\Controllers\Admin\RBACController::class, 'clearCache'])
        ->name('rbac.clear-cache');

    // Enhanced User Management with Role Assignment
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::get('/users/{user}/roles', [App\Http\Controllers\Admin\UserController::class, 'showRoles'])
        ->name('users.roles');
    Route::put('/users/{user}/roles', [App\Http\Controllers\Admin\UserController::class, 'updateRoles'])
        ->name('users.roles.update');
    Route::post('/users/{user}/assign-role', [App\Http\Controllers\Admin\UserController::class, 'assignRole'])
        ->name('users.assign-role');
    Route::delete('/users/{user}/remove-role/{role}', [App\Http\Controllers\Admin\UserController::class, 'removeRole'])
        ->name('users.remove-role');
    Route::post('/users/{user}/toggle-active', [App\Http\Controllers\Admin\UserController::class, 'toggleActive'])
        ->name('users.toggle-active');
    Route::get('/users/{user}/change-password', [App\Http\Controllers\Admin\UserController::class, 'showChangePasswordForm'])
        ->name('users.password.form');
    Route::post('/users/{user}/change-password', [App\Http\Controllers\Admin\UserController::class, 'changePassword'])
        ->name('users.password.update');
    Route::post('/users/{user}/verify', [App\Http\Controllers\Admin\UserController::class, 'verifyClient'])
        ->name('users.verify');

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
    
    //chat
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [App\Http\Controllers\ChatController::class, 'index'])->name('index');
        Route::get('/settings', [App\Http\Controllers\ChatController::class, 'settings'])->name('settings');
        Route::get('/{chatSession}', [App\Http\Controllers\ChatController::class, 'show'])->name('show');
        Route::get('/api/statistics', [App\Http\Controllers\ChatController::class, 'statistics'])->name('statistics');
    });
    
    
    // Quotations management
    Route::resource('quotations', App\Http\Controllers\Admin\QuotationController::class);
    
    // Enhanced quotation routes
    Route::prefix('quotations')->name('quotations.')->group(function () {
        
        // Status management routes
        Route::post('/{quotation}/update-status', [App\Http\Controllers\Admin\QuotationController::class, 'updateStatus'])
            ->name('update-status');
        
        // Email response routes
        Route::post('/{quotation}/send-response', [App\Http\Controllers\Admin\QuotationController::class, 'sendResponse'])
            ->name('send-response');
        
        // Project creation from quotation
        Route::get('/{quotation}/create-project', [App\Http\Controllers\Admin\QuotationController::class, 'createProject'])
            ->name('create-project');
        
        // Quotation duplication
        Route::post('/{quotation}/duplicate', [App\Http\Controllers\Admin\QuotationController::class, 'duplicate'])
            ->name('duplicate');
        
        // Export functionality
        Route::get('/export', [App\Http\Controllers\Admin\QuotationController::class, 'export'])
            ->name('export');
        
        // Bulk actions
        Route::post('/bulk-action', [App\Http\Controllers\Admin\QuotationController::class, 'bulkAction'])
            ->name('bulk-action');
        
        // Statistics and analytics
        Route::get('/statistics', [App\Http\Controllers\Admin\QuotationController::class, 'statistics'])
            ->name('statistics');
        
        // Quick approve/reject routes (for faster actions)
        Route::post('/{quotation}/approve', [App\Http\Controllers\Admin\QuotationController::class, 'quickApprove'])
            ->name('approve');
        
        Route::post('/{quotation}/reject', [App\Http\Controllers\Admin\QuotationController::class, 'quickReject'])
            ->name('reject');
        
        // Mark as reviewed
        Route::post('/{quotation}/mark-reviewed', [App\Http\Controllers\Admin\QuotationController::class, 'markAsReviewed'])
            ->name('mark-reviewed');
        
        // Attachment management
        Route::get('/{quotation}/attachments/{attachment}/download', [App\Http\Controllers\Admin\QuotationController::class, 'downloadAttachment'])
            ->name('attachments.download')
            ->where('attachment', '[0-9]+');
        
        // Priority management
        Route::post('/{quotation}/priority', [App\Http\Controllers\Admin\QuotationController::class, 'updatePriority'])
            ->name('update-priority');
        
        // Client linking
        Route::post('/{quotation}/link-client', [App\Http\Controllers\Admin\QuotationController::class, 'linkClient'])
            ->name('link-client');
        
        // Communication history
        Route::get('/{quotation}/communications', [App\Http\Controllers\Admin\QuotationController::class, 'communications'])
            ->name('communications');
    });
    
    // Messages management - Updated section
    Route::resource('messages', App\Http\Controllers\Admin\MessageController::class);
    Route::post('/messages/{message}/reply', [App\Http\Controllers\Admin\MessageController::class, 'reply'])->name('messages.reply');
    Route::post('/messages/{message}/toggle-read', [App\Http\Controllers\Admin\MessageController::class, 'toggleRead'])->name('messages.toggle-read');
    Route::post('/messages/{message}/mark-unread', [App\Http\Controllers\Admin\MessageController::class, 'markAsUnread'])->name('messages.mark-unread');
    Route::post('/messages/mark-read', [App\Http\Controllers\Admin\MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::delete('/messages/delete-multiple', [App\Http\Controllers\Admin\MessageController::class, 'destroyMultiple'])->name('messages.destroy-multiple');
    Route::get('/messages/{message}/attachments/{attachmentId}/download', [App\Http\Controllers\Admin\MessageController::class, 'downloadAttachment'])
        ->name('messages.attachments.download')
        ->where('attachmentId', '[0-9]+');

    // Email reply webhook (for external email providers)
    Route::post('/messages/email-reply-webhook', [App\Http\Controllers\Admin\MessageController::class, 'handleEmailReply'])
        ->name('messages.email-reply-webhook')
        ->middleware('throttle:100,1');
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
    
    // Post management
    Route::get('/posts', [App\Http\Controllers\Admin\PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [App\Http\Controllers\Admin\PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [App\Http\Controllers\Admin\PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [App\Http\Controllers\Admin\PostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [App\Http\Controllers\Admin\PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [App\Http\Controllers\Admin\PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [App\Http\Controllers\Admin\PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/toggle-featured', [App\Http\Controllers\Admin\PostController::class, 'toggleFeatured'])->name('posts.toggle-featured');
    Route::post('/posts/{post}/change-status', [App\Http\Controllers\Admin\PostController::class, 'changeStatus'])->name('posts.change-status');   
    // Post category management
    Route::get('/post-categories', [App\Http\Controllers\Admin\PostCategoryController::class, 'index'])->name('post-categories.index');
    Route::get('/post-categories/create', [App\Http\Controllers\Admin\PostCategoryController::class, 'create'])->name('post-categories.create');
    Route::post('/post-categories', [App\Http\Controllers\Admin\PostCategoryController::class, 'store'])->name('post-categories.store');
    Route::get('/post-categories/{postCategory}', [App\Http\Controllers\Admin\PostCategoryController::class, 'show'])->name('post-categories.show');
    Route::get('/post-categories/{postCategory}/edit', [App\Http\Controllers\Admin\PostCategoryController::class, 'edit'])->name('post-categories.edit');
    Route::put('/post-categories/{postCategory}', [App\Http\Controllers\Admin\PostCategoryController::class, 'update'])->name('post-categories.update');
    Route::delete('/post-categories/{postCategory}', [App\Http\Controllers\Admin\PostCategoryController::class, 'destroy'])->name('post-categories.destroy');
            
    // Company profile
    Route::get('/company-profile', [App\Http\Controllers\Admin\CompanyProfileController::class, 'index'])->name('company-profile.index');
    Route::get('/company-profile/edit', [App\Http\Controllers\Admin\CompanyProfileController::class, 'edit'])->name('company-profile.edit');
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
    Route::get('/settings/email', [App\Http\Controllers\Admin\EmailSettingsController::class, 'index'])->name('settings.email');
    Route::post('/settings/email', [App\Http\Controllers\Admin\EmailSettingsController::class, 'update'])->name('settings.email.update');
    Route::post('/settings/email/test-connection', [App\Http\Controllers\Admin\EmailSettingsController::class, 'testConnection'])->name('settings.email.test-connection');
    Route::post('/settings/email/test', [App\Http\Controllers\Admin\EmailSettingsController::class, 'sendTestEmail'])->name('settings.email.test');
    Route::get('/settings/email/statistics', [App\Http\Controllers\Admin\EmailSettingsController::class, 'statistics'])->name('settings.email.statistics');
    Route::get('/settings/seo', [App\Http\Controllers\Admin\SettingController::class, 'seo'])->name('settings.seo');
    Route::post('/settings/seo', [App\Http\Controllers\Admin\SettingController::class, 'updateSeo'])->name('settings.seo.update');
});