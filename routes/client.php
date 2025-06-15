<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\{
    DashboardController as ClientDashboardController,
    NotificationPreferencesController,
    ProjectController,
    QuotationController,
    MessageController,
    TestimonialController,
    NotificationController,
    ProfileController,
    
};
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UnifiedProfileController;

Route::prefix('client')->name('client.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/realtime-stats', [ClientDashboardController::class, 'getRealtimeStats'])->name('dashboard.realtime-stats');
    Route::get('/dashboard/chart-data', [ClientDashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/performance-metrics', [ClientDashboardController::class, 'getPerformanceMetrics'])->name('dashboard.performance-metrics');
    Route::get('/dashboard/upcoming-deadlines', [ClientDashboardController::class, 'getUpcomingDeadlines'])->name('dashboard.upcoming-deadlines');
    Route::get('/dashboard/recent-activities', [ClientDashboardController::class, 'getRecentActivities'])->name('dashboard.recent-activities');
    Route::post('/dashboard/clear-cache', [ClientDashboardController::class, 'clearCache'])->name('dashboard.clear-cache');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        
        // Main Views
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/preferences', [NotificationController::class, 'preferences'])->name('preferences');
        Route::put('/preferences', [NotificationController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('/recent', [NotificationController::class, 'getRecent'])
            ->middleware('throttle:60,1')
            ->name('recent');
        Route::get('/summary', [NotificationController::class, 'getSummary'])
            ->middleware('throttle:30,1')
            ->name('summary');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])
            ->middleware('throttle:120,1')
            ->name('unread-count');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])
            ->middleware('throttle:60,1')
            ->name('mark-as-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])
            ->middleware('throttle:30,1')
            ->name('destroy');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])
            ->middleware('throttle:10,1')
            ->name('mark-all-read');
        Route::post('/bulk-mark-as-read', [NotificationController::class, 'bulkMarkAsRead'])
            ->middleware('throttle:20,1')
            ->name('bulk-mark-as-read');
        Route::post('/bulk-delete', [NotificationController::class, 'bulkDelete'])
            ->middleware('throttle:10,1')
            ->name('bulk-delete');
        Route::delete('/clear-read', [NotificationController::class, 'clearRead'])
            ->middleware('throttle:5,1')
            ->name('clear-read');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
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

    // Main quotation routes
    
    Route::prefix('quotations')->name('quotations.')->group(function () {

        Route::post('upload-attachment', [QuotationController::class, 'uploadAttachment'])
            ->name('upload-attachment');

        Route::delete('delete-temp-file', [QuotationController::class, 'deleteTempFile'])
            ->name('delete-temp-file');

        Route::get('get-temp-files', [QuotationController::class, 'getTempFiles'])
            ->name('get-temp-files');
        Route::resource('', QuotationController::class)
    ->except(['destroy'])
    ->parameters(['' => 'quotation']);
        // Quotation attachment management
        Route::delete('{quotation}/attachments/{attachment}', [QuotationController::class, 'deleteAttachment'])
            ->name('delete-attachment');

        Route::get('{quotation}/attachments/{attachment}/download', [QuotationController::class, 'downloadAttachment'])
            ->name('download-attachment');
        
        // Additional quotation management routes
        Route::post('{quotation}/duplicate', [QuotationController::class, 'duplicate'])
            ->name('duplicate');

        Route::patch('{quotation}/cancel', [QuotationController::class, 'cancel'])
            ->name('cancel');

        Route::get('{quotation}/activity', [QuotationController::class, 'getActivity'])
            ->name('activity');

        Route::get('{quotation}/print', [QuotationController::class, 'print'])
            ->name('print');
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

    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/dashboard/stats', [ClientDashboardController::class, 'getRealtimeStats'])->name('dashboard.stats');
        Route::get('/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('notifications.count');
        Route::get('/messages/count', [MessageController::class, 'getUnreadCount'])->name('messages.count');
        Route::get('/projects/stats', [ProjectController::class, 'getStatistics'])->name('projects.stats');
        Route::get('/quotations/stats', [QuotationController::class, 'getStatistics'])->name('quotations.stats');
    });
});

Route::post('/dashboard/mark-notification-read', [ClientDashboardController::class, 'markNotificationRead'])->name('dashboard.mark-notification-read');
Route::post('/dashboard/test-notification', [ClientDashboardController::class, 'testNotification'])->name('dashboard.test-notification');