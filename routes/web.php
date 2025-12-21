<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ImportInvoiceController;

Route::get('/', fn() => view('dashboard'));

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
    ->prefix('customers')  // Sửa lại prefix thành 'customers'
    ->name('customers.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'destroy')->name('destroy');
    });

// Nhóm routes cho ImportInvoiceController
Route::controller(ImportInvoiceController::class)->group(function () {
    Route::get('/import-invoice', 'index')->name('invoice.index');
    Route::post('/import-invoice', 'import')->name('invoice.import');
    Route::post('/import/save', 'save')->name('invoice.save');
});
