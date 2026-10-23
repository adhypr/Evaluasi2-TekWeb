<?php

use App\Http\Controllers\DashboardWebController;
use App\Http\Controllers\ProductWebController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardWebController::class, 'index'])->name('admin.dashboard');
    Route::patch('/admin/orders/{order}/status', [DashboardWebController::class, 'updateOrderStatus'])->name('admin.orders.update-status');
    Route::resource('admin/products', ProductWebController::class)->names('admin.products');
});

Route::redirect('/admin', '/admin/dashboard');
Route::redirect('/backpage', '/admin/dashboard');
Route::redirect('/modul-praktikum/products', '/admin/products');
Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');
Route::view('/portal', 'welcome')->name('portal');
Route::redirect('/hub', '/portal');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
