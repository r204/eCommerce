@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        @if ($payment)
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-light text-gray-900 mb-2">Detail Pembayaran</h1>
            <p class="text-sm text-gray-500">#{{ $payment->order->id }}</p>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Customer Info Section -->
            <div class="px-6 py-6 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $payment->order->user->name }}</h3>
                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y H:i') }}</p>
                    </div>
                    <div class="flex flex-col items-start sm:items-end gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $payment->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ ucfirst($payment->payment_method) }}</span>
                    </div>
                </div>
            </div>

            <!-- Order Items Section -->
            <div class="px-6 py-6">
                <h4 class="text-sm font-medium text-gray-700 mb-4 uppercase tracking-wide">Item Pesanan</h4>
                <div class="space-y-4">
                    @foreach ($payment->order->orderItems as $item)
                    <div class="flex justify-between items-center py-3 border-b border-gray-50 last:border-b-0">
                        <div class="flex-1">
                            <p class="text-gray-900 font-medium">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">
                                Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Total Section -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900">Total Pembayaran</span>
                        <span class="text-2xl font-bold text-gray-900">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="mt-8">
            <a href="{{ route('payments.index', $payment->order->user_id) }}"
                class="inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Pembayaran
            </a>
        </div>

        @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Pembayaran Tidak Ditemukan</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">Data pembayaran yang Anda cari tidak tersedia atau telah dihapus dari sistem.</p>
            <a href="{{ route('payments.index') }}"
                class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                Kembali ke Daftar Pembayaran
            </a>
        </div>
        @endif
    </div>
</div>
@endsection