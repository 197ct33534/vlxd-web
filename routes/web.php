<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ImportInvoiceController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialPriceController;
use App\Http\Controllers\StoreInfoController;
use App\Http\Controllers\Portal\AuthController as PortalAuthController;
use App\Http\Controllers\Portal\HomeController as PortalHomeController;
use App\Http\Controllers\Portal\OrderController as PortalOrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Driver\DeliveryController;
use App\Http\Controllers\Portal\CalculatorController as PortalCalculatorController;
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

Route::post('/landing/quote-request', [\App\Http\Controllers\LandingQuoteRequestController::class, 'store'])
    ->name('landing.quote-request')
    ->middleware('throttle:8,1');

// Mobile portal (customer-facing PWA)
Route::get('/m', function () {
    return session('portal_customer_id')
        ? redirect()->route('portal.home')
        : redirect()->route('portal.login');
});
Route::prefix('m')->name('portal.')->group(function () {
    Route::get('/login', [PortalAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [PortalAuthController::class, 'login'])->name('login.submit');

    Route::middleware('portal')->group(function () {
        Route::post('/logout', [PortalAuthController::class, 'logout'])->name('logout');
        Route::get('/', [PortalHomeController::class, 'index'])->name('home');
        Route::get('/orders', [PortalOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [PortalOrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [PortalOrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [PortalOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/status', [PortalOrderController::class, 'status'])->name('orders.status');
        Route::get('/api/latest-price', [PortalOrderController::class, 'latestPrice'])->name('orders.latest-price');
        Route::get('/api/suggest-products', [PortalOrderController::class, 'suggestProducts'])->name('orders.suggest');
        Route::get('/calculator', [PortalCalculatorController::class, 'index'])->name('calculator');
    });
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'indexAll'])->name('projects.index');

    Route::get('material-prices', [MaterialPriceController::class, 'index'])->name('material-prices.index');

    Route::middleware('admin')->group(function () {
        Route::get('/store-settings', [StoreInfoController::class, 'edit'])->name('store-settings.edit');
        Route::put('/store-settings', [StoreInfoController::class, 'update'])->name('store-settings.update');

        Route::delete('material-prices/bulk', [MaterialPriceController::class, 'destroyBulk'])->name('material-prices.bulk-destroy');
        Route::post('material-prices/sync', [MaterialPriceController::class, 'syncFromHistory'])->name('material-prices.sync');
        Route::resource('material-prices', MaterialPriceController::class)->except(['index', 'show']);

        Route::resource('employees', \App\Http\Controllers\EmployeeController::class);
        Route::post('/employees/{employee}/advances', [\App\Http\Controllers\EmployeeController::class, 'storeAdvance'])->name('employees.advances.store');
        Route::get('/employees/{employee}/advances', [\App\Http\Controllers\EmployeeController::class, 'getAdvances'])->name('employees.advances.index');

        Route::prefix('reports')->name('admin.reports.')->group(function () {
            Route::get('/', [AdminReportController::class, 'index'])->name('index');
            Route::get('/export/debt-excel', [AdminReportController::class, 'exportDebtExcel'])->name('export.debt_excel');
            Route::get('/export/revenue-excel', [AdminReportController::class, 'exportRevenueExcel'])->name('export.revenue_excel');
            Route::get('/export/summary-pdf', [AdminReportController::class, 'exportSummaryPdf'])->name('export.summary_pdf');
        });
    });

    Route::get('/test-db', function () {
        try {
            DB::connection()->getPdo();
            $dbName = DB::connection()->getDatabaseName();

            return "✅ Kết nối thành công đến database: <b>{$dbName}</b>";
        } catch (\Exception $e) {
            return "❌ Không thể kết nối database.<br>Lỗi: ".$e->getMessage();
        }
    })->middleware('allow.diagnostics');

    Route::controller(CustomerController::class)
        ->prefix('customers')
        ->name('customers.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/update/{id}', 'update')->name('update');
            Route::delete('/delete/{id}', 'destroy')->name('destroy');
        });

    Route::controller(\App\Http\Controllers\ProjectController::class)
        ->prefix('customers/{customer}/projects')
        ->name('customers.projects.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{project}', 'update')->name('update');
            Route::delete('/{project}', 'destroy')->name('destroy');
        });

    Route::controller(ImportInvoiceController::class)->group(function () {
        Route::get('/import-invoice', 'index')->name('invoice.index');
        Route::post('/import-invoice', 'import')->name('invoice.import');
        Route::post('/import/save', 'save')->name('invoice.save');
    });

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

    Route::resource('daily-reports', \App\Http\Controllers\DailyReportController::class);
    Route::middleware('admin')->group(function () {
        Route::post('/daily-reports/{dailyReport}/approve', [\App\Http\Controllers\DailyReportController::class, 'approve'])->name('daily-reports.approve');
        Route::put('/daily-reports/{dailyReport}/reject', [\App\Http\Controllers\DailyReportController::class, 'reject'])->name('daily-reports.reject');
    });
    Route::get('/api/latest-price', [\App\Http\Controllers\DailyReportController::class, 'getLatestPrice'])->name('api.latest-price');

    // Admin order confirmation (shop) — Mobile-First
    Route::middleware('role:admin')->prefix('admin/orders')->name('admin.orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
        Route::post('/{order}/confirm', [AdminOrderController::class, 'confirm'])->name('confirm');
        Route::put('/{order}/items', [AdminOrderController::class, 'updateItems'])->name('items.update');
        Route::post('/{order}/assign-driver', [AdminOrderController::class, 'assignDriver'])->name('assign-driver');
        Route::post('/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/export-invoice', [AdminOrderController::class, 'exportInvoice'])->name('export-invoice');
        Route::put('/{order}/project', [AdminOrderController::class, 'updateProject'])->name('project.update');
    });

    // Driver delivery screens
    Route::middleware('role:driver,admin')->prefix('driver')->name('driver.')->group(function () {
        Route::get('/', [DeliveryController::class, 'index'])->name('index');
        Route::get('/orders/{order}', [DeliveryController::class, 'show'])->name('show');
        Route::post('/orders/{order}/start', [DeliveryController::class, 'start'])->name('start');
        Route::post('/orders/{order}/complete', [DeliveryController::class, 'complete'])->name('complete');
    });
});
