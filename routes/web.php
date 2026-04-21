<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ImportInvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StoreInfoController;
use App\Models\StoreInfo;

// Auth Routes
Route::get('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Public Routes
Route::get('/', function () {
    $latestPrices = \App\Http\Controllers\QuotationController::getLatestPrices();
    $storeInfo = StoreInfo::first();
    $agent = new \Jenssegers\Agent\Agent();
    if ($agent->isMobile()) {
        return view('landing-mobile', compact('latestPrices', 'storeInfo'));
    }

    return view('landing', compact('latestPrices', 'storeInfo'));
})->name('landing');

Route::get('/quotation/download', [\App\Http\Controllers\QuotationController::class, 'download'])->name('quotation.download');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'indexAll'])->name('projects.index');

    Route::get('/store-settings', [StoreInfoController::class, 'edit'])->name('store-settings.edit');
    Route::put('/store-settings', [StoreInfoController::class, 'update'])->name('store-settings.update');

    // Quản lý báo giá vật tư
    Route::resource('material-prices', App\Http\Controllers\MaterialPriceController::class);
    Route::post('material-prices/sync', [App\Http\Controllers\MaterialPriceController::class, 'syncFromHistory'])->name('material-prices.sync');

    Route::get('/test-db', function () {
        try {
            DB::connection()->getPdo();
            $dbName = DB::connection()->getDatabaseName();

            return "✅ Kết nối thành công đến database: <b>{$dbName}</b>";
        } catch (\Exception $e) {
            return "❌ Không thể kết nối database.<br>Lỗi: ".$e->getMessage();
        }
    })->middleware('allow.diagnostics');

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
        Route::get('/invoices/{invoice}/pdf', 'pdf')->name('invoices.pdf');
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
    Route::middleware('admin')->group(function () {
        Route::post('/daily-reports/{dailyReport}/approve', [\App\Http\Controllers\DailyReportController::class, 'approve'])->name('daily-reports.approve');
        Route::put('/daily-reports/{dailyReport}/reject', [\App\Http\Controllers\DailyReportController::class, 'reject'])->name('daily-reports.reject');
    });
    Route::get('/api/latest-price', [\App\Http\Controllers\DailyReportController::class, 'getLatestPrice'])->name('api.latest-price');
});
