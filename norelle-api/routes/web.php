<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;



Route::get('/admin/login', [AuthController::class, 'show'])->name('login');

Route::post('/admin/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('admin.login');

Route::middleware(['auth', 'can:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
       ->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
       

        Route::get('/orders', [OrderController::class, 'index'])
         ->name('orders.index');
         Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.status');
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');

        Route::patch('/products/{product}', [ProductController::class, 'update'])
        ->name('products.update');
        Route::get('/products/create', [ProductController::class, 'create'])
        ->name('products.create');
       Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');
       
        Route::resource('categories', CategoryController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);


    });
