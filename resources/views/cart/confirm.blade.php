@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Checkout Berhasil</h2>
    <p>Terima kasih, pesanan Anda telah berhasil dibuat.</p>

    <p><strong>ID Pesanan:</strong> {{ $order->id }}</p>
    <p><strong>Total:</strong> Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
</div>
@endsection