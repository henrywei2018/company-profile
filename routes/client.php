<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\{
    DashboardController as ClientDashboardController,
    NotificationPreferencesController,
    ProjectController,
    QuotationController,
    MessageController,
    TestimonialController,
    ProfileController,
    SettingsController
};
use App\Http\Controllers\ChatController;

Route::prefix('client')->name('client.')->middleware(['auth', 'client'])->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/realtime-stats', [ClientDashboardController::class, 'getRealtimeStats'])->name('dashboard.realtime-stats');
    Route::get('/dashboard/chart-data', [ClientDashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/performance-metrics', [ClientDashboardController::class, 'getPerformanceMetrics'])->name('dashboard.performance-metrics');
    Route::get('/dashboard/upcoming-deadlines', [ClientDashboardController::class, 'getUpcomingDeadlines'])->name('dashboard.upcoming-deadlines');
    Route::get('/dashboard/recent-activities', [ClientDashboardController::class, 'getRecentActivities'])->name('dashboard.recent-activities');
    Route::get('/dashboard/notifications', [ClientDashboardController::class, 'getNotifications'])->name('dashboard.notifications');
    Route::post('/dashboard/mark-notification-read', [ClientDashboardController::class, 'markNotificationRead'])->name('dashboard.mark-notification-read');
    Route::post('/dashboard/test-notification', [ClientDashboardController::class, 'testNotification'])->name('dashboard.test-notification');
    Route::post('/dashboard/clear-cache', [ClientDashboardController::class, 'clearCache'])->name('dashboard.clear-cache');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\NotificationController::class, 'index'])->name('index');
        Route::get('/recent', [App\Http\Controllers\Client\NotificationController::class, 'getRecent'])->name('recent');
        Route::post('/{notification}/read', [App\Http\Controllers\Client\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [App\Http\Controllers\Client\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [App\Http\Controllers\Client\NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/clear-read', [App\Http\Controllers\Client\NotificationController::class, 'clearRead'])->name('clear-read');
        Route::get('/summary', [App\Http\Controllers\Client\NotificationController::class, 'getSummary'])->name('summary');
        Route::get('/unread-count', [App\Http\Controllers\Client\NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/preferences', [NotificationPreferencesController::class, 'show'])->name('preferences');
        Route::put('/preferences', [NotificationPreferencesController::class, 'update'])->name('preferences.update');
    });

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/documents', [ProjectController::class, 'documents'])->name('documents');
        Route::get('/{project}/documents/{document}/download', [ProjectController::class, 'downloadDocument'])->name('documents.download');
        Route::get('/{project}/timeline', [ProjectController::class, 'getTimeline'])->name('timeline');
        Route::get('/{project}/files/{file}/download', [ProjectController::class, 'downloadFile'])->name('files.download');
        Route::get('/statistics', [ProjectController::class, 'getStatistics'])->name('statistics');
        Route::get('/{project}/testimonial/create', [ProjectController::class, 'showTestimonialForm'])->name('testimonial.create');
        Route::post('/{project}/testimonial', [ProjectController::class, 'storeTestimonial'])->middleware('throttle:3,1')->name('testimonial.store');
    });

    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->name('index');
        Route::get('/create', [QuotationController::class, 'create'])->name('create');
        Route::post('/', [QuotationController::class, 'store'])->middleware('throttle:5,1')->name('store');
        Route::get('/{quotation}', [QuotationController::class, 'show'])->name('show');
        Route::put('/{quotation}/approve', [QuotationController::class, 'approve'])->name('approve');
        Route::put('/{quotation}/reject', [QuotationController::class, 'reject'])->name('reject');
        Route::post('/{quotation}/feedback', [QuotationController::class, 'provideFeedback'])->name('feedback');
        Route::get('/{quotation}/additional-info', [QuotationController::class, 'showAdditionalInfoForm'])->name('additional-info');
        Route::put('/{quotation}/additional-info', [QuotationController::class, 'updateAdditionalInfo'])->name('additional-info.update');
        Route::get('/{quotation}/decline', [QuotationController::class, 'showDeclineForm'])->name('decline.form');
        Route::post('/{quotation}/decline', [QuotationController::class, 'decline'])->name('decline');
        Route::get('/{quotation}/attachments/{attachment}/download', [QuotationController::class, 'downloadAttachment'])->name('attachments.download');
        Route::get('/statistics', [QuotationController::class, 'getStatistics'])->name('statistics');
    });

    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/create', [MessageController::class, 'create'])->name('create');
        Route::post('/', [MessageController::class, 'store'])->middleware('throttle:10,1')->name('store');
        Route::get('/{message}', [MessageController::class, 'show'])->name('show');
        Route::post('/{message}/reply', [MessageController::class, 'reply'])->middleware('throttle:10,1')->name('reply');
        Route::patch('/{message}/mark-read', [MessageController::class, 'markAsRead'])->name('mark-read');
        Route::get('/{message}/reply', [MessageController::class, 'showReplyForm'])->name('reply.form');
        Route::post('/{message}/toggle-read', [MessageController::class, 'toggleRead'])->name('toggle-read');
        Route::get('/{message}/attachments/{attachment}/download', [MessageController::class, 'downloadAttachment'])->name('attachments.download');
        Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('unread-count');
        Route::post('/mark-all-read', [MessageController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/statistics', [MessageController::class, 'getStatistics'])->name('statistics');
    });

    Route::prefix('testimonials')->name('testimonials.')->group(function () {
        Route::get('/', [TestimonialController::class, 'index'])->name('index');
        Route::get('/create', [TestimonialController::class, 'create'])->name('create');
        Route::post('/', [TestimonialController::class, 'store'])->middleware('throttle:3,1')->name('store');
        Route::get('/{testimonial}', [TestimonialController::class, 'show'])->name('show');
        Route::get('/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('edit');
        Route::put('/{testimonial}', [TestimonialController::class, 'update'])->name('update');
        Route::delete('/{testimonial}', [TestimonialController::class, 'destroy'])->name('destroy');
        Route::get('/available-projects', [TestimonialController::class, 'availableProjects'])->name('available-projects');
        Route::get('/{testimonial}/preview', [TestimonialController::class, 'preview'])->name('preview');
        Route::get('/statistics', [TestimonialController::class, 'getStatistics'])->name('statistics');
    });

    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/history', [ChatController::class, 'history'])->name('history');
        Route::get('/{chatSession}', [ChatController::class, 'show'])->name('show');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::post('/update', [ProfileController::class, 'update'])->name('update');
        Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('change-password');
        Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('change-password.update');
        Route::get('/preferences', [ProfileController::class, 'preferences'])->name('preferences');
        Route::post('/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('/privacy', [ProfileController::class, 'privacy'])->name('privacy');
        Route::post('/privacy', [ProfileController::class, 'updatePrivacy'])->name('privacy.update');
        Route::get('/activity', [ProfileController::class, 'activity'])->name('activity');
        Route::get('/export-data', [ProfileController::class, 'exportData'])->name('export-data');
        Route::get('/delete', [ProfileController::class, 'showDeleteForm'])->name('delete');
        Route::delete('/delete', [ProfileController::class, 'deleteAccount'])->name('delete.confirm');
    });

    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/dashboard/stats', [ClientDashboardController::class, 'getRealtimeStats'])->name('dashboard.stats');
        Route::get('/notifications/count', [App\Http\Controllers\Client\NotificationController::class, 'getUnreadCount'])->name('notifications.count');
        Route::get('/messages/count', [MessageController::class, 'getUnreadCount'])->name('messages.count');
        Route::get('/projects/stats', [ProjectController::class, 'getStatistics'])->name('projects.stats');
        Route::get('/quotations/stats', [QuotationController::class, 'getStatistics'])->name('quotations.stats');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/account', [SettingsController::class, 'account'])->name('account');
        Route::post('/account', [SettingsController::class, 'updateAccount'])->name('account.update');
        Route::get('/security', [SettingsController::class, 'security'])->name('security');
        Route::post('/security', [SettingsController::class, 'updateSecurity'])->name('security.update');
    });
});
