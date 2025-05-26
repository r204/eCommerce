@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-light text-gray-900 mb-2">Checkout</h1>
            <p class="text-sm text-gray-600">Review pesanan Anda sebelum melanjutkan</p>
        </div>

        @if(session('success'))
        <!-- Success Alert -->
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if($cartItems->isEmpty())
        <!-- Empty Cart State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="text-center py-16 px-6">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13v8a2 2 0 002 2h6a2 2 0 002-2v-8m-8 0V9a2 2 0 012-2h4a2 2 0 012 2v4.01" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Keranjang Kosong</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">Anda belum menambahkan produk ke keranjang. Silakan pilih produk terlebih dahulu sebelum melakukan checkout.</p>
                <a href="{{ url('/') }}"
                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Mulai Belanja
                </a>
            </div>
        </div>
        @else
        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user_id }}">

            <!-- Order Summary Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                <!-- Card Header -->
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Ringkasan Pesanan</h3>
                    <p class="text-sm text-gray-600">{{ $cartItems->count() }} item dalam keranjang</p>
                </div>

                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php $grandTotal = 0; @endphp
                            @foreach($cartItems as $item)
                            @php
                            $subtotal = $item->product->price * $item->quantity;
                            $grandTotal += $subtotal;
                            @endphp
                            <tr class="hover:bg-gray-25 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $item->product->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">
                                        Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden divide-y divide-gray-100">
                    @php $grandTotal = 0; @endphp
                    @foreach($cartItems as $item)
                    @php
                    $subtotal = $item->product->price * $item->quantity;
                    $grandTotal += $subtotal;
                    @endphp
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium text-gray-900 flex-1 pr-4">{{ $item->product->name }}</h4>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center text-sm text-gray-600">
                            <span>Rp {{ number_format($item->product->price, 0, ',', '.') }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Qty: {{ $item->quantity }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Total Section -->
                <div class="px-6 py-6 border-t border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900">Total Pembayaran</span>
                        <span class="text-2xl font-bold text-gray-900">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Termasuk pajak dan biaya layanan</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-between">
                <a href="javascript:history.back()"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center px-8 py-3 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Proses Checkout
                </button>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection