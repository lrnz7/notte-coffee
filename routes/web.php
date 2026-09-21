<?php

use Illuminate\Support\Facades\Route;

// Import Seluruh Controller Publik & Auth
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PageController;

// Import Seluruh Controller Admin ERP
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\OrderController;

/*
|--------------------------------------------------------------------------
| Public & Customer Routes (Frontend E-Commerce)
|--------------------------------------------------------------------------
*/
// 1. Landing Page Editorial & Katalog Public
Route::get('/', [CustomerController::class, 'index'])->name('home');
Route::get('/menu', [CustomerController::class, 'menu'])->name('customer.menu');

// 2. Checkout & Order Tracking
Route::post('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
Route::get('/order/{invoice}', [CustomerController::class, 'trackOrder'])->name('customer.order.track');

// 3. Halaman Pendukung Wajib
Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/faq', [PageController::class, 'faq'])->name('pages.faq');
Route::get('/terms', [PageController::class, 'terms'])->name('pages.terms');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin & Cashier Routes (ERP Core Backend)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,cashier'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Management Master Data
    Route::resource('materials', MaterialController::class);
    Route::resource('menus', MenuController::class);

    // POS Kasir Toko (Offline)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');

    // Pesanan Online Management
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});