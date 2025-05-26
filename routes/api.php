<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;

Route::get('/cart/{user_id}', [CartController::class, 'index']);
Route::post('/cart', [CartController::class, 'store']);
Route::delete('/cart/{id}', [CartController::class, 'destroy']);

Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
// Di api.php (karena ini dipanggil oleh Midtrans server, tidak perlu CSRF)
Route::post('/midtrans/callback', [\App\Http\Controllers\PaymentController::class, 'midtransCallback']);

//Route::post('/midtrans/callback', [\App\Http\Controllers\PaymentController::class, 'callback']);




Route::get('/payment-history/{user_id}', [PaymentController::class, 'history']);
Route::get('/payment-detail/{order_id}', [PaymentController::class, 'detail']);
Route::get('/payment-history/{user_id}/filter', [PaymentController::class, 'filteredHistory']);
