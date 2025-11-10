<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Sales\SaleController;
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Purchases\PurchaseController;
use App\Http\Controllers\Purchases\SupplierController;
use App\Http\Controllers\Reports\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes(['register' => false]);

// Redirect root to home
Route::redirect('/', '/home');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Inventory Routes
    Route::get('products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);

    // Sales Routes
    Route::resource('sales', SaleController::class);
    Route::resource('customers', CustomerController::class);

    // Purchase Routes
    Route::resource('purchases', PurchaseController::class);
    Route::resource('suppliers', SupplierController::class);

    // Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/suppliers', [ReportController::class, 'suppliers'])->name('suppliers');
    });
});
