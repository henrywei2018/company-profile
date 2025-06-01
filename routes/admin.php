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
    ChatTemplateController
};
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;

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
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/settings', [ChatController::class, 'settings'])->name('settings');
        Route::post('/settings', [ChatController::class, 'updateSettings'])->name('settings.update');
        Route::get('/reports', [ChatController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [ChatController::class, 'exportReport'])->name('reports.export');
        Route::get('/{chatSession}', [ChatController::class, 'show'])->name('show');
        Route::post('/{chatSession}/reply', [ChatController::class, 'reply'])->middleware('throttle:30,1')->name('reply');
        Route::post('/{chatSession}/close-session', [ChatController::class, 'closeSession'])->name('close');
        Route::post('/{chatSession}/assign', [ChatController::class, 'assignToMe'])->name('assign');
        Route::post('/{chatSession}/priority', [ChatController::class, 'updatePriority'])->name('priority');
        Route::post('/{chatSession}/notes', [ChatController::class, 'updateNotes'])->name('notes');
        Route::post('/{chatSession}/typing', [ChatController::class, 'typing'])->name('typing');
        Route::post('/{chatSession}/transfer', [ChatController::class, 'transferSession'])->name('transfer');
        Route::post('/{chatSession}/use-template', [ChatController::class, 'useTemplate'])->name('use-template');
        Route::post('/{chatSession}/assign-to-me', [ChatController::class, 'assignToMe'])->name('assign-to-me');
        Route::post('/{chatSession}/take-over', [ChatController::class, 'takeOverSession'])->name('take-over');
        Route::get('/{chatSession}/poll-messages', [ChatController::class, 'pollMessages'])->name('poll-messages');
        Route::post('/{chatSession}/mark-messages-read', [ChatController::class, 'markMessagesRead'])->name('mark-messages-read');
        Route::get('/{chatSession}/messages', [ChatController::class, 'getChatMessages'])->name('messages');
        Route::get('/api/statistics', [ChatController::class, 'statistics'])->name('statistics');
        
        Route::post('/operator/online', [ChatController::class, 'goOnline'])->name('operator.online');
        Route::post('/operator/offline', [ChatController::class, 'goOffline'])->name('operator.offline');
        Route::get('/operator/status', [ChatController::class, 'getOperatorStatus'])->name('operator.status');
        Route::get('/operators/available', [ChatController::class, 'getAvailableOperators'])->name('operators.available');
        Route::post('/operator/availability', [ChatController::class, 'updateAvailability'])->name('operator.availability');
        Route::get('/templates', [ChatController::class, 'templates'])->name('templates');
        Route::post('/templates', [ChatController::class, 'storeTemplate'])->name('templates.store');
        Route::put('/templates/{template}', [ChatController::class, 'updateTemplate'])->name('templates.update');
        Route::delete('/templates/{template}', [ChatController::class, 'destroyTemplate'])->name('templates.destroy');
        Route::post('/bulk-update', [ChatController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/archive-old', [ChatController::class, 'archiveOldSessions'])->name('archive-old');
        
        Route::get('/operators/available', [ChatController::class, 'getAvailableOperators'])->name('operators.available');
        Route::get('/reports/detailed', [ChatController::class, 'detailedReports'])->name('reports.detailed');
        Route::post('/reports/generate', [ChatController::class, 'generateReport'])->name('reports.generate');
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
    Route::resource('posts', PostController::class);
    Route::post('/posts/{post}/toggle-featured', [PostController::class, 'toggleFeatured'])->name('posts.toggle-featured');
    Route::post('/posts/{post}/change-status', [PostController::class, 'changeStatus'])->name('posts.change-status');

    // Post Categories
    Route::resource('post-categories', PostCategoryController::class);

    // Company Profile
    Route::get('/company-profile', [CompanyProfileController::class, 'index'])->name('company-profile.index');
    Route::get('/company-profile/edit', [CompanyProfileController::class, 'edit'])->name('company-profile.edit');
    Route::put('/company-profile', [CompanyProfileController::class, 'update'])->name('company-profile.update');
    Route::get('/company-profile/seo', [CompanyProfileController::class, 'seo'])->name('company-profile.seo');
    Route::put('/company-profile/seo', [CompanyProfileController::class, 'updateSeo'])->name('company-profile.seo.update');
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/edit', [CompanyProfileController::class, 'index'])->name('edit');
    });

    // Notifications (global)
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('notifications/bulk-mark-as-read', [NotificationController::class, 'bulkMarkAsRead'])->name('notifications.bulk-mark-as-read');
    Route::post('notifications/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('notifications.bulk-delete');
    Route::get('notifications/settings', [NotificationController::class, 'settings'])->name('notifications.settings');
    Route::put('notifications/settings', [NotificationController::class, 'updateSettings'])->name('notifications.settings.update');
    Route::post('notifications/{notification}/mark-as-read', [DashboardController::class, 'markNotificationAsRead'])->name('notifications.mark-as-read');
    Route::post('notifications/mark-all-as-read', [DashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-as-read');
    Route::get('notifications/counts', [DashboardController::class, 'getNotificationCounts'])->name('notifications.counts');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/settings/email', [EmailSettingsController::class, 'index'])->name('settings.email');
    Route::post('/settings/email', [EmailSettingsController::class, 'update'])->name('settings.email.update');
    Route::post('/settings/email/test-connection', [EmailSettingsController::class, 'testConnection'])->name('settings.email.test-connection');
    Route::post('/settings/email/test', [EmailSettingsController::class, 'sendTestEmail'])->name('settings.email.test');
    Route::get('/settings/email/statistics', [EmailSettingsController::class, 'statistics'])->name('settings.email.statistics');
    Route::get('/settings/seo', [SettingController::class, 'seo'])->name('settings.seo');
    Route::post('/settings/seo', [SettingController::class, 'updateSeo'])->name('settings.seo.update');
});
