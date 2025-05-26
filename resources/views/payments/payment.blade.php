@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-gray-900">Pembayaran Pesanan</h1>
        </div>

        <div class="space-y-6">
            <!-- Order Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informasi Pesanan</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">ID Pesanan:</span>
                        <span class="font-medium text-gray-900">{{ $order->id }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total:</span>
                        <span class="font-semibold text-lg text-gray-900">
                            Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $order->status === 'paid' ? 'bg-green-100 text-green-800' : 
                               ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Product Details Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Detail Produk</h2>
                
                <div class="space-y-3">
                    @foreach ($order->orderItems as $item)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <span class="text-gray-900">{{ $item->product->name }}</span>
                            <span class="text-gray-500 ml-2">x{{ $item->quantity }}</span>
                        </div>
                        <span class="font-medium text-gray-900">
                            Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Form Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form action="{{ route('payment.process') }}" method="POST">
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    
                    <!-- Payment Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Bayar
                        </label>
                        <input 
                            type="number" 
                            id="amount" 
                            name="amount" 
                            value="{{ $order->total_amount }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                   disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed"
                            {{ $order->status === 'paid' ? 'disabled' : '' }}>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Metode Pembayaran
                        </label>
                        <select 
                            id="payment_method" 
                            name="payment_method"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                   disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed"
                            {{ $order->status === 'paid' ? 'disabled' : '' }}>
                            <option value="">-- Pilih Metode --</option>
                            <option value="bank_transfer">Transfer Bank</option>
                            <option value="e_wallet">E-Wallet</option>
                            <option value="cod">Cash on Delivery</option>
                        </select>
                    </div>

                    <!-- Payment Status Notification -->
                    @if ($order->status === 'paid')
                    <div class="bg-green-50 border border-green-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-800">
                                    Pesanan ini sudah dibayar. Pembayaran tidak dapat dilakukan lagi.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-md font-medium
                               hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                               disabled:bg-gray-300 disabled:cursor-not-allowed disabled:hover:bg-gray-300
                               transition-colors duration-200"
                        {{ $order->status === 'paid' ? 'disabled' : '' }}>
                        {{ $order->status === 'paid' ? 'Sudah Dibayar' : 'Bayar Sekarang' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection