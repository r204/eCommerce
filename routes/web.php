<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;

// ===================
// Halaman Utama
// ===================
Route::get('/public/', function () {
    return view('welcome');
})->name('home');

// ===================
// Cart & Checkout
// ===================
Route::get('/cart/add', [CartController::class, 'showAddForm'])->name('cart.add');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::get('/cart/{user_id}', [CartController::class, 'index'])->name('cart.view');
Route::get('/cart/checkout/{user_id}', [CartController::class, 'showCheckoutForm'])->name('cart.checkout');
Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout.process');

// ===================
// Pembayaran
// ===================
// Menampilkan form pembayaran
Route::get('/payment/{order_id}', [CartController::class, 'showPaymentForm'])->name('payment.form');
// Proses pembayaran manual (jika masih ada)
Route::post('/payment', [CartController::class, 'processPayment'])->name('payment.process');

// Callback dari Midtrans (ini seharusnya ada di routes/api.php!)
//Route::post('/midtrans/callback', [PaymentController::class, 'midtransCallback']);

// ===================
// Status & Konfirmasi
// ===================
Route::get('/payment/confirm/{order_id}', [CartController::class, 'showConfirmation'])->name('order.confirm');
Route::get('/cart/payment-success/{order}', function ($orderId) {
    $order = \App\Models\Order::findOrFail($orderId);
    return view('cart.payment-success', compact('order'));
})->name('payment.success');

Route::get('/cart/status/{order}', function ($orderId) {
    $order = \App\Models\Order::findOrFail($orderId);
    return view('cart.status', compact('order'));
})->name('payment.status');

// ===================
// Riwayat Pembayaran
// ===================
Route::get('/payment-history/{user_id}', [PaymentController::class, 'index'])->name('payments.index');
Route::get('/payment-detail/{order_id}', [PaymentController::class, 'show'])->name('payments.show');
