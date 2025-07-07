<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    DashboardController,
    RoleController,
    PermissionController,
    RBACController,
    UserController,
    ServiceController,
    ServiceCategoryController,
    BannerCategoryController,
    BannerController,
    ProductController,
    ProductCategoryController,
    ProjectController,
    ProjectFileController, 
    ProjectMilestoneController,
    ProjectCategoryController,
    QuotationController,
    MessageController,
    TeamController,
    TeamMemberDepartmentController,
    TestimonialController,
    CertificationController,
    PostController,
    PostCategoryController,
    CompanyProfileController,
    SettingController,
    EmailSettingsController,
    NotificationController,
    ChatTemplateController
};
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UnifiedProfileController;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    
    // API Token Management
    Route::post('/api-tokens/generate', [DashboardController::class, 'generateApiToken'])->name('admin.api-tokens.generate');
    Route::get('/api-tokens', [DashboardController::class, 'listApiTokens'])->name('admin.api-tokens.list');
    Route::delete('/api-tokens/revoke', [DashboardController::class, 'revokeApiToken'])->name('admin.api-tokens.revoke');
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::post('dashboard/clear-cache', [DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');
    Route::get('dashboard/export', [DashboardController::class, 'exportDashboard'])->name('dashboard.export');
    Route::get('dashboard/system-health', [DashboardController::class, 'getSystemHealth'])->name('dashboard.system-health');
    Route::post('dashboard/send-test-notification', [DashboardController::class, 'sendTestNotification'])->name('dashboard.send-test-notification');

    // RBAC
    Route::resource('roles', RoleController::class);
    Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    Route::get('/roles/{role}/users', [RoleController::class, 'users'])->name('roles.users');
    Route::post('/roles/{role}/duplicate', [RoleController::class, 'duplicate'])->name('roles.duplicate');

    Route::resource('permissions', PermissionController::class);
    Route::get('/permissions/bulk-create', [PermissionController::class, 'showBulkCreate'])->name('permissions.bulk-create');
    Route::post('/permissions/bulk-create', [PermissionController::class, 'bulkCreate'])->name('permissions.bulk-store');
    Route::get('/permissions/{permission}/roles', [PermissionController::class, 'roles'])->name('permissions.roles');

    Route::get('/rbac/dashboard', [RBACController::class, 'dashboard'])->name('rbac.dashboard');
    Route::get('/rbac/audit-log', [RBACController::class, 'auditLog'])->name('rbac.audit-log');
    Route::post('/rbac/clear-cache', [RBACController::class, 'clearCache'])->name('rbac.clear-cache');


    // Users
    Route::resource('users', UserController::class);
    Route::prefix('users')->name('users.')->group(function () {
        // ✅ KEEP - Core admin user management
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        
        // ✅ KEEP - Admin-specific user actions
        Route::post('/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{user}/verify', [UserController::class, 'verifyClient'])->name('verify');
        
        // ✅ NEW - Bulk operations
        Route::post('/bulk-action', [UserController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/export', [UserController::class, 'exportUsers'])->name('export');
        Route::get('/search', [UserController::class, 'searchUsers'])->name('search');
        Route::get('/statistics', [UserController::class, 'getUserStatistics'])->name('statistics');
        Route::post('/{user}/send-welcome', [UserController::class, 'sendWelcomeEmail'])->name('send-welcome');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::post('/{user}/impersonate', [UserController::class, 'impersonate'])->name('impersonate');
        Route::post('/stop-impersonation', [UserController::class, 'stopImpersonation'])->name('stop-impersonation');
        
    });
    
    // Services
    Route::resource('services', ServiceController::class);
    Route::patch('services/{service}/toggle-active', [ServiceController::class, 'toggleActive'])->name('services.toggle-active');
    Route::patch('services/{service}/toggle-featured', [ServiceController::class, 'toggleFeatured'])->name('services.toggle-featured');
    Route::post('services/update-order', [ServiceController::class, 'updateOrder'])->name('services.update-order');
    
    // Service Categories
    Route::resource('service-categories', ServiceCategoryController::class)->parameters(['service-categories' => 'category']);
    Route::patch('service-categories/{category}/toggle-active', [ServiceCategoryController::class, 'toggleActive'])->name('service-categories.toggle-active');
    Route::post('service-categories/update-order', [ServiceCategoryController::class, 'updateOrder'])->name('service-categories.update-order');
    Route::post('service-categories/bulk-action', [ServiceCategoryController::class, 'bulkAction'])->name('service-categories.bulk-action');
    // Product Management
    Route::prefix('products')->name('products.')->group(function () {
        // Temporary file routes (MUST come before resource routes to avoid conflicts)
        Route::post('/temp-upload', [ProductController::class, 'uploadTempImages'])->name('temp-upload');
        Route::delete('/temp-delete', [ProductController::class, 'deleteTempImage'])->name('temp-delete');
        Route::get('/temp-files', [ProductController::class, 'getTempFiles'])->name('temp-files');
        Route::post('/cleanup-temp', [ProductController::class, 'cleanupTempFiles'])->name('cleanup-temp');
        
        // Resource routes (these will bind {product} parameter)
        Route::resource('/', ProductController::class)->parameters(['' => 'product']);
        
        // Product-specific routes (these need {product} parameter)
        Route::post('{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('toggle-active');
        Route::post('{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
        Route::post('{product}/upload-image', [ProductController::class, 'uploadImages'])->name('upload-image');
        Route::delete('{product}/delete-image', [ProductController::class, 'deleteImage'])->name('delete-image');
        
        // Bulk operations (no specific product needed)
        Route::post('bulk-action', [ProductController::class, 'bulkAction'])->name('bulk-action');
        Route::post('update-order', [ProductController::class, 'updateOrder'])->name('update-order');
        
        // Data endpoints (no specific product needed)
        Route::get('statistics', [ProductController::class, 'getStatistics'])->name('statistics');
        Route::get('export', [ProductController::class, 'export'])->name('export');
        Route::get('search', [ProductController::class, 'search'])->name('search');
    });

    // Product Categories
    Route::resource('product-categories', ProductCategoryController::class)->parameters(['product-categories' => 'productCategory']);
    Route::prefix('product-categories')->name('product-categories.')->group(function () {
        Route::patch('{productCategory}/toggle-active', [ProductCategoryController::class, 'toggleActive'])->name('toggle-active');
        Route::post('{productCategory}/duplicate', [ProductCategoryController::class, 'duplicate'])->name('duplicate');
        Route::post('update-order', [ProductCategoryController::class, 'updateOrder'])->name('update-order');
        Route::post('bulk-action', [ProductCategoryController::class, 'bulkAction'])->name('bulk-action');
        Route::get('statistics', [ProductCategoryController::class, 'statistics'])->name('statistics');
        Route::get('export', [ProductCategoryController::class, 'export'])->name('export');
    });
    // Banner Management
    Route::prefix('banner-categories')->name('banner-categories.')->group(function () {
        Route::resource('/', BannerCategoryController::class)->parameters(['' => 'bannerCategory']);
        Route::post('{bannerCategory}/toggle-status', [BannerCategoryController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('reorder', [BannerCategoryController::class, 'reorder'])->name('reorder');
        Route::get('statistics', [BannerCategoryController::class, 'statistics'])->name('statistics');
        Route::get('export', [BannerCategoryController::class, 'export'])->name('export');
        Route::post('bulk-action', [BannerCategoryController::class, 'bulkAction'])->name('bulk-action');
    });
    Route::prefix('banners')->name('banners.')->group(function () {
        // Temporary file routes (MUST come before resource routes to avoid conflicts)
        Route::post('/temp-upload', [BannerController::class, 'uploadTempImages'])->name('temp-upload');
        Route::delete('/temp-delete', [BannerController::class, 'deleteTempImage'])->name('temp-delete');
        Route::get('/temp-files', [BannerController::class, 'getTempFiles'])->name('temp-files');
        Route::post('/cleanup-temp', [BannerController::class, 'cleanupTempFiles'])->name('cleanup-temp');
        
        // Resource routes (these will bind {banner} parameter)
        Route::resource('/', BannerController::class)->parameters(['' => 'banner']);
        
        // Banner-specific routes (these need {banner} parameter)
        Route::post('{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('{banner}/duplicate', [BannerController::class, 'duplicate'])->name('duplicate');
        Route::post('{banner}/upload-image', [BannerController::class, 'uploadImages'])->name('upload-image');
        Route::delete('{banner}/delete-image', [BannerController::class, 'deleteImage'])->name('delete-image');
        
        // Bulk operations (no specific banner needed)
        Route::post('bulk-action', [BannerController::class, 'bulkAction'])->name('bulk-action');
        Route::post('reorder', [BannerController::class, 'reorder'])->name('reorder');
        
        // Data endpoints (no specific banner needed)
        Route::get('statistics', [BannerController::class, 'statistics'])->name('statistics');
        Route::get('export', [BannerController::class, 'export'])->name('export');
        
        // Preview route (needs {banner} parameter)
        Route::get('{banner}/preview', [BannerController::class, 'preview'])->name('preview');
    });

    
    
    

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::prefix('projects')->name('projects.')->group(function () {
        // Temporary file routes (MUST come before resource routes to avoid conflicts)
        Route::post('/upload-temp', [ProjectController::class, 'uploadTempImages'])->name('upload-temp');
        Route::delete('/delete-temp', [ProjectController::class, 'deleteTempImage'])->name('delete-temp');
        Route::get('/temp-files', [ProjectController::class, 'getTempFiles'])->name('temp-files');
        Route::post('/cleanup-temp', [ProjectController::class, 'cleanupTempFiles'])->name('cleanup-temp');
        Route::patch('/{project}/toggle-featured', [ProjectController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/update-order', [ProjectController::class, 'updateOrder'])->name('update-order');
        Route::patch('/{project}/quick-update', [ProjectController::class, 'quickUpdate'])->name('quick-update');
        Route::post('/{project}/convert-to-quotation', [ProjectController::class, 'convertToQuotation'])->name('convert-to-quotation');
        
        Route::post('/{project}/images/reorder', [ProjectController::class, 'reorderImages'])->name('reorder-images');
    });
    
    // Project Milestones - Enhanced Routes
    Route::prefix('projects/{project}')->name('projects.')->group(function () {
        // Milestone CRUD
        Route::resource('milestones', ProjectMilestoneController::class)->except(['index']);
        
        // Milestone specific actions
        Route::prefix('milestones')->name('milestones.')->group(function () {
            // List all milestones for a project
            Route::get('/', [ProjectMilestoneController::class, 'index'])->name('index');
            
            // Quick actions
            Route::patch('/{milestone}/complete', [ProjectMilestoneController::class, 'complete'])->name('complete');
            Route::patch('/{milestone}/reopen', [ProjectMilestoneController::class, 'reopen'])->name('reopen');
            
            // NEW: Update milestone status via AJAX (for kanban)
            Route::patch('/{milestone}/update-status', [ProjectMilestoneController::class, 'updateStatus'])->name('update-status');
            
            // Bulk operations
            Route::post('/bulk-update', [ProjectMilestoneController::class, 'bulkUpdate'])->name('bulk-update');
            Route::post('/update-order', [ProjectMilestoneController::class, 'updateOrder'])->name('update-order');
            
            // Data endpoints
            Route::get('/calendar', [ProjectMilestoneController::class, 'calendar'])->name('calendar');
            Route::get('/statistics', [ProjectMilestoneController::class, 'statistics'])->name('statistics');
        });

        // NEW: Project Files Management
        Route::prefix('files')->name('files.')->group(function () {
            Route::get('/', [ProjectFileController::class, 'index'])->name('index');
            Route::get('/show', [ProjectFileController::class, 'show'])->name('show');
            Route::get('/create', [ProjectFileController::class, 'create'])->name('create');
            Route::post('/', [ProjectFileController::class, 'store'])->name('store');
            
            
            Route::get('/{file}/download', [ProjectFileController::class, 'download'])->name('download');
            Route::get('/{file}/preview', [ProjectFileController::class, 'preview'])->name('preview');
            Route::get('/{file}/thumbnail', [ProjectFileController::class, 'thumbnail'])->name('thumbnail');
            Route::delete('/{file}', [ProjectFileController::class, 'destroy'])->name('destroy');
        
            Route::post('/upload', [ProjectFileController::class, 'upload'])->name('upload');   // temp upload
            Route::delete('/upload', [ProjectFileController::class, 'delete'])->name('delete'); // temp delete
        
            Route::post('/process', [ProjectFileController::class, 'process'])->name('process');
            Route::delete('/revert', [ProjectFileController::class, 'revert'])->name('revert');
            Route::post('/submit', [ProjectFileController::class, 'processSubmission'])->name('submit');
        
            Route::post('/cleanup', [ProjectFileController::class, 'cleanupTempFiles'])->name('cleanup');
            Route::post('/bulk-download', [ProjectFileController::class, 'bulkDownload'])->name('bulk-download');
            Route::delete('/bulk-delete', [ProjectFileController::class, 'bulkDelete'])->name('bulk-delete');
            Route::get('/search', [ProjectFileController::class, 'search'])->name('search');
            Route::get('/statistics', [ProjectFileController::class, 'getStatistics'])->name('statistics');
            Route::get('/export', [ProjectFileController::class, 'export'])->name('export');
        });
        
    });
    // Additional project routes
    Route::patch('projects/{project}/toggle-featured', [ProjectController::class, 'toggleFeatured'])->name('projects.toggle-featured');
    Route::patch('projects/{project}/quick-update', [ProjectController::class, 'quickUpdate'])->name('projects.quick-update');
    Route::post('projects/update-order', [ProjectController::class, 'updateOrder'])->name('projects.update-order');
    Route::post('projects/bulk-action', [ProjectController::class, 'bulkAction'])->name('projects.bulk-action');
    Route::get('projects/search', [ProjectController::class, 'search'])->name('projects.search');
    Route::get('projects/{project}/timeline-data', [ProjectController::class, 'getTimelineData'])->name('projects.timeline-data');
    Route::patch('projects/{project}/set-featured-image/{image}', [ProjectController::class, 'setFeaturedImage'])->name('projects.set-featured-image');
    Route::delete('projects/{project}/delete-image/{image}', [ProjectController::class, 'deleteImage'])->name('projects.delete-image');
    Route::get('projects/statistics', [ProjectController::class, 'getStatistics'])->name('projects.statistics');
    Route::get('projects/export', [ProjectController::class, 'export'])->name('projects.export');
    
    // Convert project to/from quotation
    Route::post('projects/{project}/convert-to-quotation', [ProjectController::class, 'convertToQuotation'])->name('projects.convert-to-quotation');
    Route::get('quotations/{quotation}/create-project', [ProjectController::class, 'createFromQuotation'])->name('quotations.create-project');

    // Project Categories
    Route::resource('project-categories', ProjectCategoryController::class);
    Route::patch('/project-categories/{projectCategory}/toggle-active', [ProjectCategoryController::class, 'toggleActive'])->name('project-categories.toggle-active');
    // Chat
    Route::prefix('chat')->name('chat.')->group(function () {
        // Main chat management
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/settings', [ChatController::class, 'settings'])->name('settings');
        Route::post('/settings', [ChatController::class, 'updateSettings'])->name('settings.update');
        Route::get('/reports', [ChatController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [ChatController::class, 'exportReport'])->name('reports.export');
        Route::get('/reports/detailed', [ChatController::class, 'detailedReports'])->name('reports.detailed');
        Route::post('/reports/generate', [ChatController::class, 'generateReport'])->name('reports.generate');
        
        // Individual chat session management 
        Route::get('/{chatSession}', [ChatController::class, 'show'])->name('show');
        Route::post('/{chatSession}/reply', [ChatController::class, 'reply'])->middleware('throttle:30,1')->name('reply');
        Route::post('/{chatSession}/close-session', [ChatController::class, 'closeSession'])->name('close');
        Route::post('/{chatSession}/assign', [ChatController::class, 'assignToMe'])->name('assign');
        Route::post('/{chatSession}/assign-to-me', [ChatController::class, 'assignToMe'])->name('assign-to-me');
        Route::post('/{chatSession}/take-over', [ChatController::class, 'takeOverSession'])->name('take-over');
        Route::post('/{chatSession}/priority', [ChatController::class, 'updatePriority'])->name('priority');
        Route::post('/{chatSession}/notes', [ChatController::class, 'updateNotes'])->name('notes');
        Route::post('/{chatSession}/typing', [ChatController::class, 'typing'])->name('typing');
        Route::post('/{chatSession}/transfer', [ChatController::class, 'transferSession'])->name('transfer');
        Route::post('/{chatSession}/use-template', [ChatController::class, 'useTemplate'])->name('use-template');
        Route::get('/{chatSession}/poll-messages', [ChatController::class, 'pollMessages'])->name('poll-messages');
        Route::post('/{chatSession}/mark-messages-read', [ChatController::class, 'markMessagesRead'])->name('mark-messages-read');
        Route::get('/{chatSession}/messages', [ChatController::class, 'getChatMessages'])->name('messages');
        
        // Operator management
        Route::post('/operator/online', [ChatController::class, 'goOnline'])->name('operator.online');
        Route::post('/operator/offline', [ChatController::class, 'goOffline'])->name('operator.offline');
        Route::get('/operator/status', [ChatController::class, 'getOperatorStatus'])->name('operator.status');
        Route::post('/operator/availability', [ChatController::class, 'updateAvailability'])->name('operator.availability');
        Route::get('/operators/available', [ChatController::class, 'getAvailableOperators'])->name('operators.available');
        
        // Quick templates for chat usage (different from full template management)
        Route::get('/quick-templates', [ChatController::class, 'getQuickTemplates'])->name('quick-templates');
        Route::get('/search-templates', [ChatController::class, 'searchTemplates'])->name('search-templates');
        
        // Bulk operations
        Route::post('/bulk-update', [ChatController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/archive-old', [ChatController::class, 'archiveOldSessions'])->name('archive-old');
        
        // API endpoints
        Route::get('/api/statistics', [ChatController::class, 'statistics'])->name('statistics');
        Route::get('/api/dashboard-metrics', [ChatController::class, 'getDashboardMetrics'])->name('api.dashboard-metrics');
        Route::get('/api/sessions', [ChatController::class, 'getAdminSessions'])->name('api.sessions');
        
        // Chat Templates Management (Full CRUD)
        Route::prefix('templates')->name('templates.')->group(function () {
            // Standard CRUD routes
            Route::get('/', [ChatTemplateController::class, 'index'])->name('index');
            Route::get('/create', [ChatTemplateController::class, 'create'])->name('create');
            Route::post('/', [ChatTemplateController::class, 'store'])->name('store');
            Route::get('/{template}', [ChatTemplateController::class, 'show'])->name('show');
            Route::get('/{template}/edit', [ChatTemplateController::class, 'edit'])->name('edit');
            Route::put('/{template}', [ChatTemplateController::class, 'update'])->name('update');
            Route::delete('/{template}', [ChatTemplateController::class, 'destroy'])->name('destroy');
            
            // Template actions
            Route::post('/{template}/toggle-active', [ChatTemplateController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/{template}/duplicate', [ChatTemplateController::class, 'duplicate'])->name('duplicate');
            Route::post('/{template}/increment-usage', [ChatTemplateController::class, 'incrementUsage'])->name('increment-usage');
            
            // Bulk operations
            Route::post('/bulk-update', [ChatTemplateController::class, 'bulkUpdate'])->name('bulk-update');
            Route::get('/export', [ChatTemplateController::class, 'export'])->name('export');
            Route::post('/import', [ChatTemplateController::class, 'import'])->name('import');
            
            // API endpoints for templates
            Route::get('/api/by-type', [ChatTemplateController::class, 'getByType'])->name('api.by-type');
            Route::get('/api/search', [ChatTemplateController::class, 'search'])->name('api.search');
            Route::get('/api/statistics', [ChatTemplateController::class, 'getStatistics'])->name('api.statistics');
        });
    });
    // Messages
    Route::prefix('messages')->name('messages.')->group(function () {
        // CRUD
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/create', [MessageController::class, 'create'])->name('create');
        Route::post('/', [MessageController::class, 'store'])->name('store');
        Route::get('/{message}', [MessageController::class, 'show'])->name('show');
        Route::delete('/{message}', [MessageController::class, 'destroy'])->name('destroy');
        
        // Reply System
        Route::get('/{message}/reply', [MessageController::class, 'reply'])->name('reply');
        Route::post('/{message}/reply', [MessageController::class, 'storeReply'])->name('store-reply');
        
        // Message Status Actions
        Route::post('/{message}/toggle-read', [MessageController::class, 'toggleRead'])->name('toggle-read');
        Route::post('/{message}/update-priority', [MessageController::class, 'updatePriority'])->name('update-priority');
        Route::post('/{message}/forward', [MessageController::class, 'forwardMessage'])->name('forward');
        
        // Bulk Operations
        Route::post('/bulk-action', [MessageController::class, 'bulkAction'])
        ->middleware('throttle:30,1')
        ->name('bulk-action');
        
        Route::post('/bulk-priority', [MessageController::class, 'bulkUpdatePriority'])
            ->middleware('throttle:30,1')
            ->name('bulk-priority');
            
        Route::post('/preview-bulk-action', [MessageController::class, 'previewBulkAction'])
            ->middleware('throttle:60,1')
            ->name('preview-bulk-action');
            
        Route::get('/bulk-actions', [MessageController::class, 'getBulkActions'])
            ->middleware('throttle:60,1')
            ->name('bulk-actions');
        Route::post('/bulk-forward', [MessageController::class, 'bulkForward'])->name('bulk-forward');
        
        // File
        Route::get('/{message}/attachments/{attachmentId}/download', [MessageController::class, 'downloadAttachment'])->name('attachments.download');
        Route::post('/temp-upload', [MessageController::class, 'uploadTempAttachment'])
            ->middleware('throttle:30,1')
            ->name('temp-upload');
            
        Route::delete('/temp-delete', [MessageController::class, 'deleteTempAttachment'])
            ->middleware('throttle:30,1')
            ->name('temp-delete');
            
        Route::get('/temp-files', [MessageController::class, 'getTempFiles'])
            ->middleware('throttle:60,1')
            ->name('temp-files');
            
        Route::post('/cleanup-temp', [MessageController::class, 'cleanupTempFiles'])
            ->middleware('throttle:10,1')
            ->name('cleanup-temp');
        // Export & Statistics
        Route::get('/export', [MessageController::class, 'export'])->name('export');
        Route::get('/statistics', [MessageController::class, 'statistics'])->name('statistics');
    });
    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::prefix('quotations')->name('quotations.')->group(function () {
        // Status management
        Route::post('/{quotation}/update-status', [QuotationController::class, 'updateStatus'])->name('update-status');
        Route::post('/{quotation}/approve', [QuotationController::class, 'quickApprove'])->name('approve');
        Route::post('/{quotation}/reject', [QuotationController::class, 'quickReject'])->name('reject');
        Route::post('/{quotation}/mark-reviewed', [QuotationController::class, 'markAsReviewed'])->name('mark-reviewed');
        
        // Communication
        Route::post('/{quotation}/send-response', [QuotationController::class, 'sendResponse'])->middleware('throttle:10,1')->name('send-response');
        Route::get('/{quotation}/communications', [QuotationController::class, 'communications'])->name('communications');
        
        // Project conversion - NEW ROUTES
        Route::get('/{quotation}/convert-to-project', [QuotationController::class, 'showConversionForm'])->name('convert-to-project.form');
        Route::post('/{quotation}/convert-to-project', [QuotationController::class, 'convertToProject'])->name('convert-to-project');
        Route::post('/{quotation}/quick-convert', [QuotationController::class, 'quickConvertToProject'])->name('quick-convert');
        
        // Management actions
        Route::post('/{quotation}/duplicate', [QuotationController::class, 'duplicate'])->name('duplicate');
        Route::post('/{quotation}/priority', [QuotationController::class, 'updatePriority'])->name('update-priority');
        Route::post('/{quotation}/link-client', [QuotationController::class, 'linkClient'])->name('link-client');
        
        // Attachments
        Route::get('/{quotation}/attachments/{attachment}/download', [QuotationController::class, 'downloadAttachment'])
            ->name('attachments.download')->where('attachment', '[0-9]+');
        
        // Bulk operations
        Route::post('/bulk-action', [QuotationController::class, 'bulkAction'])->middleware('throttle:30,1')->name('bulk-action');
        
        // Data exports and statistics
        Route::get('/export', [QuotationController::class, 'export'])->name('export');
        Route::get('/statistics', [QuotationController::class, 'statistics'])->name('statistics');
        Route::get('/counts', [QuotationController::class, 'getCounts'])->name('counts');
    });

    // Team
    Route::prefix('team')->name('team.')->group(function () {
        // Temporary file routes (MUST come before resource routes to avoid conflicts)
        Route::post('/temp-upload', [TeamController::class, 'uploadTempImages'])->name('upload-temp');
        Route::delete('/temp-delete', [TeamController::class, 'deleteTempImage'])->name('delete-temp');
        Route::get('/temp-files', [TeamController::class, 'getTempFiles'])->name('temp-files');
        Route::post('/cleanup-temp', [TeamController::class, 'cleanupTempFiles'])->name('cleanup-temp');
        
        // Standard CRUD routes
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::get('/create', [TeamController::class, 'create'])->name('create');
        Route::post('/', [TeamController::class, 'store'])->name('store');
        Route::get('/{teamMember}', [TeamController::class, 'show'])->name('show');
        Route::get('/{teamMember}/edit', [TeamController::class, 'edit'])->name('edit');
        Route::put('/{teamMember}', [TeamController::class, 'update'])->name('update');
        Route::delete('/{teamMember}', [TeamController::class, 'destroy'])->name('destroy');
        
        // Team member specific actions (these need {teamMember} parameter)
        Route::post('/{teamMember}/toggle-active', [TeamController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{teamMember}/toggle-featured', [TeamController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::delete('/{teamMember}/delete-photo', [TeamController::class, 'deletePhoto'])->name('delete-photo');
        
        // Bulk operations (no specific team member needed)
        Route::post('/bulk-action', [TeamController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/update-order', [TeamController::class, 'updateOrder'])->name('update-order');
        
        // Data endpoints (no specific team member needed)
        Route::get('/statistics', [TeamController::class, 'statistics'])->name('statistics');
        Route::get('/export', [TeamController::class, 'export'])->name('export');
    });

    // Team Departments
    Route::prefix('team-member-departments')->name('team-member-departments.')->group(function () {
        // Standard CRUD routes (if not already defined)
        Route::get('/', [TeamMemberDepartmentController::class, 'index'])->name('index');
        Route::get('/create', [TeamMemberDepartmentController::class, 'create'])->name('create');
        Route::post('/', [TeamMemberDepartmentController::class, 'store'])->name('store');
        Route::get('/{teamMemberDepartment}', [TeamMemberDepartmentController::class, 'show'])->name('show');
        Route::get('/{teamMemberDepartment}/edit', [TeamMemberDepartmentController::class, 'edit'])->name('edit');
        Route::put('/{teamMemberDepartment}', [TeamMemberDepartmentController::class, 'update'])->name('update');
        Route::delete('/{teamMemberDepartment}', [TeamMemberDepartmentController::class, 'destroy'])->name('destroy');
        
        // Additional action routes
        Route::patch('/{teamMemberDepartment}/toggle-active', [TeamMemberDepartmentController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/update-order', [TeamMemberDepartmentController::class, 'updateOrder'])->name('update-order');
        Route::post('/bulk-action', [TeamMemberDepartmentController::class, 'bulkAction'])->name('bulk-action');
        
        // Data endpoints
        Route::get('/api/statistics', [TeamMemberDepartmentController::class, 'statistics'])->name('statistics');
        Route::get('/api/export', [TeamMemberDepartmentController::class, 'export'])->name('export');
        Route::get('/api/search', [TeamMemberDepartmentController::class, 'search'])->name('search');
    });

    // Testimonials
    Route::prefix('testimonials')->name('testimonials.')->group(function () {
        Route::post('/temp-upload', [TestimonialController::class, 'uploadTempImages'])->name('temp-upload');
        Route::delete('/temp-delete', [TestimonialController::class, 'deleteTempImage'])->name('temp-delete');
        Route::post('/cleanup-temp', [TestimonialController::class, 'cleanupTempFiles'])->name('cleanup-temp');
        Route::get('/ajax/clients', [TestimonialController::class, 'getClientsWithCompletedProjects'])->name('ajax.clients');
        Route::get('/ajax/client/{client}/details', [TestimonialController::class, 'getClientDetails'])->name('ajax.client.details');
        Route::get('/ajax/client/{client}/projects', [TestimonialController::class, 'getClientProjects'])->name('ajax.client.projects');
        Route::get('/', [TestimonialController::class, 'index'])->name('index');
        Route::get('/create', [TestimonialController::class, 'create'])->name('create');
        Route::post('/', [TestimonialController::class, 'store'])->name('store');
        Route::get('/{testimonial}', [TestimonialController::class, 'show'])->name('show');
        Route::get('/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('edit');
        Route::put('/{testimonial}', [TestimonialController::class, 'update'])->name('update');
        Route::delete('/{testimonial}', [TestimonialController::class, 'destroy'])->name('destroy');
        
        Route::post('/{testimonial}/upload-image', [TestimonialController::class, 'uploadImages'])->name('upload-image');
        Route::delete('/{testimonial}/delete-image', [TestimonialController::class, 'deleteImage'])->name('delete-image');
        // Status management
        Route::patch('/{testimonial}/toggle-active', [TestimonialController::class, 'toggleActive'])->name('toggle-active');
        Route::patch('/{testimonial}/toggle-featured', [TestimonialController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::patch('/{testimonial}/approve', [TestimonialController::class, 'approve'])->name('approve');
        Route::patch('/{testimonial}/reject', [TestimonialController::class, 'reject'])->name('reject');
        
        // Bulk actions
        Route::post('/bulk-action', [TestimonialController::class, 'bulkAction'])->name('bulk-action');
        
        // API endpoints
        Route::get('/api/statistics', [TestimonialController::class, 'statistics'])->name('statistics');
    });

    // Certifications
    Route::prefix('certifications')->name('certifications.')->group(function () {
        Route::post('/temp-upload', [CertificationController::class, 'uploadTempImages'])->name('temp-upload');
        Route::delete('/temp-delete', [CertificationController::class, 'deleteTempImage'])->name('temp-delete'); // Changed back to DELETE
        Route::get('/temp-files', [CertificationController::class, 'getTempFiles'])->name('temp-files');
        Route::post('/cleanup-temp', [CertificationController::class, 'cleanupTempFiles'])->name('cleanup-temp');
        Route::post('/{certification}/toggle-active', [CertificationController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/update-order', [CertificationController::class, 'updateOrder'])->name('update-order');
    });
    Route::resource('certifications', CertificationController::class);

        // Posts
    Route::resource('posts', PostController::class)->names('posts');

    // Additional custom post routes
    Route::prefix('posts')->name('posts.')->group(function () {
        // Custom toggles
        Route::post('/{post}/toggle-featured', [PostController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{post}/change-status', [PostController::class, 'changeStatus'])->name('change-status');
        Route::delete('/{post}/remove-image', [PostController::class, 'removeFeaturedImage'])->name('remove-image');

        // Management tools
        Route::post('/bulk-action', [PostController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/{post}/duplicate', [PostController::class, 'duplicate'])->name('duplicate');

        // Export & stats
        Route::get('/export', [PostController::class, 'export'])->name('export');
        Route::get('/statistics', [PostController::class, 'statistics'])->name('statistics');
    });


    // Post Categories
    Route::resource('post-categories', PostCategoryController::class);
    Route::prefix('post-categories')->name('post-categories.')->group(function () {
        // Bulk operations
        Route::post('/bulk-action', [PostCategoryController::class, 'bulkAction'])->name('bulk-action');
       
        Route::get('/export', [PostCategoryController::class, 'export'])->name('export');
        
        Route::get('/statistics', [PostCategoryController::class, 'statistics'])->name('statistics');
        
        Route::get('/popular', [PostCategoryController::class, 'getPopularCategories'])->name('popular');
        
        Route::get('/search', [PostCategoryController::class, 'search'])->name('search');
    });

    // Company Profile
    Route::prefix('company')->name('company.')->group(function () {
        // Existing routes...
        Route::get('/', [App\Http\Controllers\Admin\CompanyProfileController::class, 'index'])->name('index');
        Route::get('/edit', [App\Http\Controllers\Admin\CompanyProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [App\Http\Controllers\Admin\CompanyProfileController::class, 'update'])->name('update');
        Route::get('/seo', [App\Http\Controllers\Admin\CompanyProfileController::class, 'seo'])->name('seo');
        Route::put('/seo', [App\Http\Controllers\Admin\CompanyProfileController::class, 'updateSeo'])->name('seo.update');
        Route::get('/certificates', [App\Http\Controllers\Admin\CompanyProfileController::class, 'certificates'])->name('certificates');
        
        // Export Routes - NEW
        Route::get('/export', [App\Http\Controllers\Admin\CompanyProfileController::class, 'export'])->name('export');
        Route::get('/export/pdf', [App\Http\Controllers\Admin\CompanyProfileController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/pdf/stream', [App\Http\Controllers\Admin\CompanyProfileController::class, 'streamPdf'])->name('export.pdf.stream');
        Route::get('/export/certificates/pdf', [App\Http\Controllers\Admin\CompanyProfileController::class, 'exportCertificatesPdf'])->name('export.certificates.pdf');
        Route::post('/export/bulk', [App\Http\Controllers\Admin\CompanyProfileController::class, 'bulkExport'])->name('export.bulk');
    });

    // Notifications (global)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        // Main views
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/preferences', [NotificationController::class, 'preferences'])->name('preferences');
        Route::put('/preferences', [NotificationController::class, 'updatePreferences'])->name('preferences.update');
        
        // API endpoints for AJAX calls (ADDED/FIXED)
        Route::get('/recent', [NotificationController::class, 'getRecent'])->name('recent');
        Route::get('/summary', [NotificationController::class, 'getSummary'])->name('summary');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        
        // Individual notification actions (FIXED)
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/clear-read', [NotificationController::class, 'clearRead'])->name('clear-read');
        Route::post('/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-mark-as-read', [NotificationController::class, 'bulkMarkAsRead'])->name('bulk-mark-as-read');
        
        // Individual notification view
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        
        // Export functionality
        Route::get('/export', [NotificationController::class, 'export'])->name('export');
        
        // Test notification (ADDED)
        Route::post('/test', [NotificationController::class, 'sendTestNotification'])->name('test');
    });


    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/', [SettingController::class, 'update'])->name('update');
        
        // SEO Settings
        Route::get('/seo', [SettingController::class, 'seo'])->name('seo');
        Route::put('/seo', [SettingController::class, 'updateSeo'])->name('seo.update');
        
        // Email Settings  
        Route::get('/email', [EmailSettingsController::class, 'index'])->name('email');
        Route::put('/email', [EmailSettingsController::class, 'update'])->name('email.update');
        Route::post('/email/test-connection', [EmailSettingsController::class, 'testConnection'])->name('email.test-connection');
        Route::post('/email/test', [EmailSettingsController::class, 'sendTestEmail'])->name('email.test');
        
        // Company Profile Settings
        Route::get('/company-profile', [SettingController::class, 'companyProfile'])->name('company-profile');
        Route::put('/company-profile', [SettingController::class, 'updateCompanyProfile'])->name('company-profile.update');
        
        // Cache Management
        Route::post('/clear-cache', [SettingController::class, 'clearCache'])->name('clear-cache');
    });
});
