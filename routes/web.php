<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/{username}', [FrontendController::class, 'index'])->name('index');

Route::get('/{username}/find-product', [ProductController::class, 'find'])->name('product.find');
Route::get('/{username}/find-product/results', [ProductController::class, 'findResults'])->name('product.find-results');
Route::get('/{username}/product/{id}', [ProductController::class, 'show'])->name('product.show');

Route::get('/{username}/cart', [TransactionController::class, 'cart'])->name('cart');
Route::get('/{username}/customer-information', [TransactionController::class, 'customerInformation'])->name('customer-information');
Route::get('{username}/checkout', [TransactionController::class, 'checkout'])->name('payment');
Route::post('/{username}/checkout', [TransactionController::class, 'checkout'])->name('payment');
Route::get('/transaction/success', [TransactionController::class, 'success'])->name('success');
