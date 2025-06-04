<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    DashboardController,
    RoleController,
    PermissionController,
    ProfileController,
    RBACController,
    UserController,
    ServiceController,
    ServiceCategoryController,
    ProjectController,
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

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
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

    Route::prefix('/profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::post('/update', [ProfileController::class, 'update'])->name('update');

        Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('password.form');
        Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('password.change');

        Route::get('/preferences', [ProfileController::class, 'preferences'])->name('preferences');
        Route::post('/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences.update');

        Route::get('/privacy', [ProfileController::class, 'privacy'])->name('privacy');
        Route::post('/privacy', [ProfileController::class, 'updatePrivacy'])->name('privacy.update');

        Route::get('/security', [ProfileController::class, 'security'])->name('security');
        Route::post('/security', [ProfileController::class, 'updateSecurity'])->name('security.update');

        Route::get('/delete', [ProfileController::class, 'showDeleteForm'])->name('delete.form');
        Route::post('/delete', [ProfileController::class, 'deleteAccount'])->name('delete');

        Route::get('/export', [ProfileController::class, 'exportData'])->name('export');
        Route::get('/activity', [ProfileController::class, 'activity'])->name('activity');
        Route::get('/test-notification', [ProfileController::class, 'testNotification'])->name('test.notification');
    });
    // Users
    Route::resource('users', UserController::class);
    Route::get('/users/{user}/roles', [UserController::class, 'showRoles'])->name('users.roles');
    Route::put('/users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');
    Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role');
    Route::delete('/users/{user}/remove-role/{role}', [UserController::class, 'removeRole'])->name('users.remove-role');
    Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::get('/users/{user}/change-password', [UserController::class, 'showChangePasswordForm'])->name('users.password.form');
    Route::post('/users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.password.update');
    Route::post('/users/{user}/verify', [UserController::class, 'verifyClient'])->name('users.verify');

    // Services
    Route::resource('services', ServiceController::class);
    Route::post('/services/{service}/toggle-status', [ServiceController::class, 'toggleActive'])->name('services.toggle-status');
    Route::patch('/services/{service}/toggle-featured', [ServiceController::class, 'toggleFeatured'])->name('services.toggle-featured');
    Route::post('/services/update-order', [ServiceController::class, 'updateOrder'])->name('services.update-order');

    // Service Categories
    Route::resource('service-categories', ServiceCategoryController::class);
    Route::patch('/service-categories/{serviceCategory}/toggle-active', [ServiceCategoryController::class, 'toggleActive'])->name('service-categories.toggle-active');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::patch('/projects/{project}/toggle-featured', [ProjectController::class, 'toggleFeatured'])->name('projects.toggle-featured');
    Route::post('/projects/update-order', [ProjectController::class, 'updateOrder'])->name('projects.update-order');

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

    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::post('/{quotation}/update-status', [QuotationController::class, 'updateStatus'])->name('update-status');
        Route::post('/{quotation}/send-response', [QuotationController::class, 'sendResponse'])->middleware('throttle:10,1')->name('send-response');
        Route::get('/{quotation}/create-project', [QuotationController::class, 'createProject'])->name('create-project');
        Route::post('/{quotation}/duplicate', [QuotationController::class, 'duplicate'])->name('duplicate');
        Route::get('/export', [QuotationController::class, 'export'])->name('export');
        Route::post('/bulk-action', [QuotationController::class, 'bulkAction'])->middleware('throttle:30,1')->name('bulk-action');
        Route::get('/statistics', [QuotationController::class, 'statistics'])->name('statistics');
        Route::post('/{quotation}/approve', [QuotationController::class, 'quickApprove'])->name('approve');
        Route::post('/{quotation}/reject', [QuotationController::class, 'quickReject'])->name('reject');
        Route::post('/{quotation}/mark-reviewed', [QuotationController::class, 'markAsReviewed'])->name('mark-reviewed');
        Route::get('/{quotation}/attachments/{attachment}/download', [QuotationController::class, 'downloadAttachment'])->name('attachments.download')->where('attachment', '[0-9]+');
        Route::post('/{quotation}/priority', [QuotationController::class, 'updatePriority'])->name('update-priority');
        Route::post('/{quotation}/link-client', [QuotationController::class, 'linkClient'])->name('link-client');
        Route::get('/{quotation}/communications', [QuotationController::class, 'communications'])->name('communications');
    });

    

    // Messages
    Route::resource('messages', MessageController::class);
    Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])->middleware('throttle:20,1')->name('messages.reply');
    Route::post('/messages/{message}/toggle-read', [MessageController::class, 'toggleRead'])->name('messages.toggle-read');
    Route::post('/messages/{message}/mark-unread', [MessageController::class, 'markAsUnread'])->name('messages.mark-unread');
    Route::post('/messages/mark-read', [MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::delete('/messages/delete-multiple', [MessageController::class, 'destroyMultiple'])->middleware('throttle:30,1')->name('messages.destroy-multiple');
    Route::get('/messages/{message}/attachments/{attachmentId}/download', [MessageController::class, 'downloadAttachment'])->name('messages.attachments.download')->where('attachmentId', '[0-9]+');
    Route::post('/messages/email-reply-webhook', [MessageController::class, 'handleEmailReply'])->middleware('throttle:100,1')->name('messages.email-reply-webhook');

    // Team
    Route::resource('team', TeamController::class);
    Route::post('/team/{teamMember}/toggle-active', [TeamController::class, 'toggleActive'])->name('team.toggle-active');
    Route::post('/team/{teamMember}/toggle-featured', [TeamController::class, 'toggleFeatured'])->name('team.toggle-featured');
    Route::post('/team/update-order', [TeamController::class, 'updateOrder'])->name('team.update-order');

    // Team Departments
    Route::resource('team-member-departments', TeamMemberDepartmentController::class);
    Route::patch('/team-member-departments/{teamMemberDepartment}/toggle-active', [TeamMemberDepartmentController::class, 'toggleActive'])->name('team-member-departments.toggle-active');
    Route::post('/team-member-departments/update-order', [TeamMemberDepartmentController::class, 'updateOrder'])->name('team-member-departments.update-order');

    // Testimonials
    Route::resource('testimonials', TestimonialController::class);
    Route::post('/testimonials/{testimonial}/toggle-active', [TestimonialController::class, 'toggleActive'])->name('testimonials.toggle-active');
    Route::post('/testimonials/{testimonial}/toggle-featured', [TestimonialController::class, 'toggleFeatured'])->name('testimonials.toggle-featured');

    // Certifications
    Route::resource('certifications', CertificationController::class);
    Route::post('/certifications/{certification}/toggle-active', [CertificationController::class, 'toggleActive'])->name('certifications.toggle-active');
    Route::post('/certifications/update-order', [CertificationController::class, 'updateOrder'])->name('certifications.update-order');

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
        Route::post('/{postCategory}/toggle-active', [PostCategoryController::class, 'toggleActive'])->name('toggle-active');
        Route::get('/export', [PostCategoryController::class, 'export'])->name('export');
        Route::get('/statistics', [PostCategoryController::class, 'statistics'])->name('statistics');
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
