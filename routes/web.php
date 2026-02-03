<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ImportInvoiceController;

// Auth Routes
Route::get('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', fn() => view('dashboard'))->name('dashboard');
    
    Route::get('/test-db', function () {
        try {
            DB::connection()->getPdo();
            $dbName = DB::connection()->getDatabaseName();
            return "✅ Kết nối thành công đến database: <b>{$dbName}</b>";
        } catch (\Exception $e) {
            return "❌ Không thể kết nối database.<br>Lỗi: " . $e->getMessage();
        }
    });

    // Nhóm routes cho CustomerController
    Route::controller(CustomerController::class)
        ->prefix('customers')
        ->name('customers.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/update/{id}', 'update')->name('update');
            Route::delete('/delete/{id}', 'destroy')->name('destroy');
        });

    // Routes for ProjectController
    Route::controller(\App\Http\Controllers\ProjectController::class)
        ->prefix('customers/{customer}/projects')
        ->name('customers.projects.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{project}', 'update')->name('update');
            Route::delete('/{project}', 'destroy')->name('destroy');
        });

    // Nhóm routes cho ImportInvoiceController
    Route::controller(ImportInvoiceController::class)->group(function () {
        Route::get('/import-invoice', 'index')->name('invoice.index');
        Route::post('/import-invoice', 'import')->name('invoice.import');
        Route::post('/import/save', 'save')->name('invoice.save');
    });

    // Routes for InvoiceController
    Route::controller(\App\Http\Controllers\InvoiceController::class)->group(function () {
        Route::get('/projects/{project}/invoices', 'index')->name('projects.invoices.index');
        Route::get('/projects/{project}/invoices/create', 'create')->name('projects.invoices.create');
        Route::post('/projects/{project}/invoices', 'store')->name('projects.invoices.store');
        Route::get('/invoices/{invoice}', 'show')->name('invoices.show');
        Route::get('/invoices/{invoice}/edit', 'edit')->name('invoices.edit');
        Route::put('/invoices/{invoice}', 'update')->name('invoices.update');
        Route::delete('/invoices/{invoice}', 'destroy')->name('invoices.destroy');
        Route::post('/projects/{project}/payments', 'storePayment')->name('projects.payments.store');
        Route::put('/payments/{payment}', 'updatePayment')->name('payments.update');
        Route::delete('/payments/{payment}', 'destroyPayment')->name('payments.destroy');
        Route::post('/invoices/{invoice}/pay', 'markAsPaid')->name('invoices.pay');
        Route::get('/projects/{project}/price-history', [\App\Http\Controllers\PriceHistoryController::class, 'index'])->name('projects.price_history');
    });

    // Routes for EmployeeController
    Route::resource('employees', \App\Http\Controllers\EmployeeController::class);
    Route::post('/employees/{employee}/advances', [\App\Http\Controllers\EmployeeController::class, 'storeAdvance'])->name('employees.advances.store');
    Route::get('/employees/{employee}/advances', [\App\Http\Controllers\EmployeeController::class, 'getAdvances'])->name('employees.advances.index');

    // Routes for DailyReportController
    Route::resource('daily-reports', \App\Http\Controllers\DailyReportController::class);
    Route::post('/daily-reports/{dailyReport}/approve', [\App\Http\Controllers\DailyReportController::class, 'approve'])->name('daily-reports.approve');
    Route::put('/daily-reports/{dailyReport}/reject', [\App\Http\Controllers\DailyReportController::class, 'reject'])->name('daily-reports.reject');
    Route::get('/api/latest-price', [\App\Http\Controllers\DailyReportController::class, 'getLatestPrice'])->name('api.latest-price');
    
    // Image Manager
    Route::get('/image-manager', [\App\Http\Controllers\ImageManagerController::class, 'index'])->name('images.index');
    Route::get('/image-manager/upload', [\App\Http\Controllers\ImageManagerController::class, 'create'])->name('images.create');
});
