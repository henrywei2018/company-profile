<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\{
    DashboardController as ClientDashboardController,
    NotificationPreferencesController,
    ProductOrderController,
    CartController,
    ProjectController,
    QuotationController,
    MessageController,
    TestimonialController,
    NotificationController,
    ProfileController,
    
};
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UnifiedProfileController;

Route::prefix('client')->name('client.')->middleware(['auth', 'client'])->group(function () {
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

    // Product Order Routes for Client Dashboard
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [ProductOrderController::class, 'index'])->name('index');
        Route::get('/checkout', [ProductOrderController::class, 'checkout'])->name('checkout');
        Route::post('/', [ProductOrderController::class, 'store'])->name('store');
        Route::get('/{order}', [ProductOrderController::class, 'show'])->name('show');
        
        // Additional cart actions (keep existing naming for compatibility)
        Route::post('/add-to-cart', [ProductOrderController::class, 'addToCart'])->name('add-to-cart');
        Route::post('/remove-from-cart', [ProductOrderController::class, 'removeFromCart'])->name('remove-from-cart');
        Route::post('/update-cart-quantity', [ProductOrderController::class, 'updateCartQuantity'])->name('update-cart-quantity');
        Route::post('/clear-cart', [ProductOrderController::class, 'clearCart'])->name('clear-cart');
        Route::get('/cart-count', [ProductOrderController::class, 'getCartCount'])->name('cart-count');
    });
    
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductOrderController::class, 'browse'])->name('index');
        Route::get('/{product}', [ProductOrderController::class, 'showProduct'])->name('show');
        Route::get('/category/{category}', [ProductOrderController::class, 'browseCategory'])->name('category');
    });

    // Cart Routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [ProductOrderController::class, 'addToCart'])->name('add');
        Route::patch('/update-quantity', [CartController::class, 'updateQuantity'])->name('update-quantity');
        Route::delete('/remove', [ProductOrderController::class, 'removeFromCart'])->name('remove');
        Route::delete('/clear', [ProductOrderController::class, 'clearCart'])->name('clear');
        Route::get('/count', [CartController::class, 'getCartCount'])->name('count');
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
            ->name('delete-attachment')
            ->where('attachment', '[0-9]+');

        Route::get('{quotation}/attachments/{attachment}/download', [QuotationController::class, 'downloadAttachment'])
            ->name('download-attachment')
            ->where('attachment', '[0-9]+');;
        
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
        Route::post('/', [MessageController::class, 'store'])->name('store');
        Route::get('/{message}', [MessageController::class, 'show'])->name('show');
        
        Route::post('/{message}/reply', [MessageController::class, 'reply'])
            ->middleware('throttle:10,1')
            ->name('reply');
            
        Route::patch('/{message}/urgent', [MessageController::class, 'markUrgent'])
            ->name('mark-urgent');
            
        Route::patch('/{message}/toggle-read', [MessageController::class, 'toggleRead'])
            ->name('toggle-read');
        
        Route::post('/bulk-action', [MessageController::class, 'bulkAction'])
            ->middleware('throttle:20,1')
            ->name('bulk-action');
        Route::get('/project/{project}', [MessageController::class, 'projectMessages'])
            ->name('project')
            ->where('project', '[0-9]+');
        
        Route::get('/{message}/attachments/{attachmentId}/download', [MessageController::class, 'downloadAttachment'])
            ->name('attachment.download')
            ->where(['message' => '[0-9]+', 'attachmentId' => '[0-9]+']);
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
        
        Route::prefix('api')->name('api.')->group(function () {
            
            Route::get('/statistics', [MessageController::class, 'getStatistics'])
                ->middleware('throttle:120,1')
                ->name('statistics');
                
            Route::post('/mark-all-read', [MessageController::class, 'markAllAsRead'])
                ->middleware('throttle:10,1')
                ->name('mark-all-read');
                
            Route::post('/{message}/toggle-read', [MessageController::class, 'apiToggleRead'])
                ->middleware('throttle:60,1')
                ->name('toggle-read');
        });
    });

    Route::prefix('testimonials')->name('testimonials.')->group(function () {
        
        // Temporary file upload routes (MUST come before resource routes to avoid conflicts)
        Route::post('/temp-upload', [TestimonialController::class, 'uploadTempImages'])->name('temp-upload');
        Route::delete('/temp-delete', [TestimonialController::class, 'deleteTempImage'])->name('temp-delete');
        
        // Standard CRUD routes
        Route::get('/', [TestimonialController::class, 'index'])->name('index');
        Route::get('/create', [TestimonialController::class, 'create'])->name('create');
        Route::post('/', [TestimonialController::class, 'store'])->name('store');
        Route::get('/{testimonial}', [TestimonialController::class, 'show'])->name('show');
        Route::get('/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('edit');
        Route::put('/{testimonial}', [TestimonialController::class, 'update'])->name('update');
        Route::delete('/{testimonial}', [TestimonialController::class, 'destroy'])->name('destroy');
        
        // API endpoints for client dashboard
        Route::get('/api/stats', [TestimonialController::class, 'getStats'])->name('api.stats');
        
        // Additional client-specific routes if needed
        Route::get('/{testimonial}/preview', [TestimonialController::class, 'preview'])->name('preview');
    });

    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/history', [ChatController::class, 'history'])->name('history');
        Route::get('/{chatSession}', [ChatController::class, 'show'])->name('show');
    });

    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/dashboard/stats', [ClientDashboardController::class, 'getRealtimeStats'])->name('dashboard.stats');
        Route::get('/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('notifications.count');
        Route::get('/projects/stats', [ProjectController::class, 'getStatistics'])->name('projects.stats');
        Route::get('/quotations/stats', [QuotationController::class, 'getStatistics'])->name('quotations.stats');
    
    });
});

Route::post('/dashboard/mark-notification-read', [ClientDashboardController::class, 'markNotificationRead'])->name('dashboard.mark-notification-read');
Route::post('/dashboard/test-notification', [ClientDashboardController::class, 'testNotification'])->name('dashboard.test-notification');