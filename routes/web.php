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
| Authentication Routes
|--------------------------------------------------------------------------
*/
// Customer Auth (Tampilan Luxury Gold)
Route::get('/login', [AuthController::class, 'showCustomerLogin'])->name('login');
Route::post('/customer/login', [AuthController::class, 'customerLogin'])->name('customer.login.post');
Route::post('/customer/register', [AuthController::class, 'customerRegister'])->name('customer.register.post');

// Staff ERP Admin / Kasir Auth (Biar Form Bawaan ERP Lu Gak Error)
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post'); // Alias untuk login.post bawaan form ERP
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Customer Account Area (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('account')->group(function () {
    Route::get('/', [CustomerController::class, 'account'])->name('customer.account');
    Route::post('/update', [CustomerController::class, 'updateAccount'])->name('customer.account.update');
});

/*
|--------------------------------------------------------------------------
| Admin & Cashier Routes (ERP Core Backend)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,cashier'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Master Data
    Route::resource('materials', MaterialController::class);
    Route::resource('menus', MenuController::class);

    // POS Kasir Toko
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');

    // Pesanan Management
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Keuangan & Arus Kas
    Route::get('/cash-flows', [CashFlowController::class, 'index'])->name('cash_flows.index');
    Route::post('/cash-flows', [CashFlowController::class, 'store'])->name('cash_flows.store');

    // Modul CRM (Manajemen Pelanggan)
    Route::get('/customers', [CrmController::class, 'index'])->name('crm.index');
    Route::get('/customers/{customer}', [CrmController::class, 'show'])->name('crm.show');
});