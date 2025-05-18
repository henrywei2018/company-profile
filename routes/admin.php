<?php

use App\Http\Controllers\Admin\ServiceController;

// Services Management
Route::group(['prefix' => 'services', 'as' => 'services.'], function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/create', [ServiceController::class, 'create'])->name('create');
    Route::post('/', [ServiceController::class, 'store'])->name('store');
    Route::get('/{service}', [ServiceController::class, 'show'])->name('show');
    Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
    Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
    Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('destroy');
    Route::patch('/{service}/toggle-status', [ServiceController::class, 'toggleActive'])->name('toggle-status');
    Route::patch('/{service}/toggle-featured', [ServiceController::class, 'toggleFeatured'])->name('toggle-featured');
    Route::post('/update-order', [ServiceController::class, 'updateOrder'])->name('update-order');
});