<?php

use Illuminate\Support\Facades\Route;

// Import Controller Public & Auth
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PageController;

// Import Controller Admin ERP
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CashFlowController;
use App\Http\Controllers\Admin\CrmController;

/*
|--------------------------------------------------------------------------
| Public & Customer Routes (Frontend E-Commerce)
|--------------------------------------------------------------------------
*/
Route::get('/', [CustomerController::class, 'index'])->name('home');
Route::get('/menu', [CustomerController::class, 'menu'])->name('customer.menu');
Route::post('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
Route::get('/order/{invoice}', [CustomerController::class, 'trackOrder'])->name('customer.order.track');
Route::post('/order/{invoice}/upload-proof', [CustomerController::class, 'uploadProof'])->name('customer.order.uploadProof');

/* Halaman Statis */
Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/faq', [PageController::class, 'faq'])->name('pages.faq');
Route::get('/terms', [PageController::class, 'terms'])->name('pages.terms');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Secured with Guest Middleware)
|--------------------------------------------------------------------------
*/
// Customer Auth (Tampilan Luxury Gold) - Hanya untuk guest customer
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthController::class, 'showCustomerLogin'])->name('login');
    Route::post('/customer/login', [AuthController::class, 'customerLogin'])->name('customer.login.post');
    Route::post('/customer/register', [AuthController::class, 'customerRegister'])->name('customer.register.post');
});

// Staff ERP Admin / Kasir Auth - Hanya untuk guest admin
Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Customer Account Area (Wajib Login Guard Web)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web'])->prefix('account')->group(function () {
    Route::get('/', [CustomerController::class, 'account'])->name('customer.account');
    Route::post('/update', [CustomerController::class, 'updateAccount'])->name('customer.account.update');
});

/*
|--------------------------------------------------------------------------
| Admin & Cashier Routes (Wajib Login Guard Admin + Role Check)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:admin', 'role:admin,cashier'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Master Data
    Route::resource('materials', MaterialController::class);
    Route::resource('menus', MenuController::class);

    // POS Kasir Toko & KDS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/active-queue', [PosController::class, 'activeQueue'])->name('pos.active_queue');
    Route::post('/pos/mark-completed/{id}', [PosController::class, 'markCompleted'])->name('pos.mark_completed');
    Route::get('/pos/shift-summary', [PosController::class, 'getShiftSummary'])->name('pos.shift_summary');
    Route::post('/pos/close-shift', [PosController::class, 'closeShift'])->name('pos.close_shift');
    Route::post('/pos/sync-offline', [PosController::class, 'syncOffline'])->name('pos.sync_offline');

    // Pesanan Management
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/payment-proof', [OrderController::class, 'servePaymentProof'])->name('orders.paymentProof');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Keuangan & Arus Kas
    Route::get('/cash-flows', [CashFlowController::class, 'index'])->name('cash_flows.index');
    Route::post('/cash-flows', [CashFlowController::class, 'store'])->name('cash_flows.store');

    // Modul CRM (Manajemen Pelanggan)
    Route::get('/customers', [CrmController::class, 'index'])->name('crm.index');
    Route::get('/customers/{customer}', [CrmController::class, 'show'])->name('crm.show');
});