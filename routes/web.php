<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;

use App\Http\Controllers\CatalogController;

Route::get('/login', [LoginController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| KATALOG PRODUK (CUSTOMER & PUBLIK)
|--------------------------------------------------------------------------
| Customer & publik melihat etalase katalog produk toko.
| Seluruh transaksi penjualan dilayani di kasir oleh staf/admin.
*/
Route::get('/', [CatalogController::class, 'index'])
    ->name('catalog.index');

Route::get('/catalog', [CatalogController::class, 'index']);

Route::get('/catalog/{product}', [CatalogController::class, 'show'])
    ->name('catalog.show')
    ->whereNumber('product');

Route::get('/receipt/{saleNumber}', [CatalogController::class, 'receipt'])
    ->name('catalog.receipt');

// Aliases untuk integrasi & backward compatibility
Route::get('/shop', [CatalogController::class, 'index'])
    ->name('customer.shop');

Route::get('/shop/{product}', [CatalogController::class, 'show'])
    ->name('customer.product')
    ->whereNumber('product');

// Pengalihan halaman cek struk lama langsung ke katalog
Route::get('/receipt-lookup', function () {
    return redirect()->route('catalog.index');
})->name('catalog.receipt_lookup');

Route::get('/orders/track', function () {
    return redirect()->route('catalog.index');
})->name('customer.track');

Route::get('/orders/{saleNumber}/receipt', [CatalogController::class, 'receipt'])
    ->name('customer.receipt');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    | Admin  : CRUD
    | Staff  : hanya index dan detail
    */

    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');

    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->name('products.show')
        ->whereNumber('product');

    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('categories.index');

    Route::get('/categories/{category}', [CategoryController::class, 'show'])
        ->name('categories.show')
        ->whereNumber('category');

    Route::get('/suppliers', [SupplierController::class, 'index'])
        ->name('suppliers.index');

    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])
        ->name('suppliers.show')
        ->whereNumber('supplier');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */

    Route::middleware('can:admin-only')->group(function () {

        Route::get('/products/create', [ProductController::class, 'create'])
            ->name('products.create');

        Route::post('/products', [ProductController::class, 'store'])
            ->name('products.store');

        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
            ->name('products.edit');

        Route::put('/products/{product}', [ProductController::class, 'update'])
            ->name('products.update');

        Route::patch('/products/{product}', [ProductController::class, 'update']);

        Route::delete('/products/{product}', [ProductController::class, 'destroy'])
            ->name('products.destroy');

        Route::resource('categories', CategoryController::class)->except(['index', 'show']);

        Route::resource('suppliers', SupplierController::class)->except(['index', 'show']);

        Route::resource('users', UserController::class);

        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])
        ->name('sales.cancel');

        Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])
        ->name('purchases.cancel');
    });

    /*
    |--------------------------------------------------------------------------
    | PURCHASE
    |--------------------------------------------------------------------------
    | Admin dan Staff boleh melakukan transaksi.
    */

    Route::get('/purchases', [PurchaseController::class, 'index'])
        ->name('purchases.index');

    Route::get('/purchases/create', [PurchaseController::class, 'create'])
        ->name('purchases.create');

    Route::post('/purchases', [PurchaseController::class, 'store'])
        ->name('purchases.store');

    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])
        ->name('purchases.show');

    /*
    |--------------------------------------------------------------------------
    | SALES
    |--------------------------------------------------------------------------
    | Admin dan Staff boleh melakukan transaksi.
    */

    Route::get('/sales', [SaleController::class, 'index'])
        ->name('sales.index');

    Route::get('/sales/create', [SaleController::class, 'create'])
        ->name('sales.create');

    Route::post('/sales', [SaleController::class, 'store'])
        ->name('sales.store');

    Route::get('/sales/{sale}', [SaleController::class, 'show'])
        ->name('sales.show');

    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])
        ->name('sales.receipt');

    /*
    |--------------------------------------------------------------------------
    | STOCK MOVEMENTS
    |--------------------------------------------------------------------------
    | Hanya riwayat otomatis Purchase / Sales.
    */

    Route::get('/stock-movements', [StockMovementController::class, 'index'])
        ->name('stock-movements.index');

    /*
    |--------------------------------------------------------------------------
    | STOCK REPORT
    |--------------------------------------------------------------------------
    */

    Route::get('/stock-reports', [StockReportController::class, 'index'])
        ->name('stock-reports.index');
});