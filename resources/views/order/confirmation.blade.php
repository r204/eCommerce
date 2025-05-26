@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Order Berhasil!</h2>

    <p>Terima kasih telah berbelanja. Berikut detail pesanan Anda:</p>

    <ul>
        <li><strong>Order ID:</strong> {{ $order->id }}</li>
        <li><strong>Tanggal Order:</strong> {{ $order->order_date }}</li>
        <li><strong>Total:</strong> Rp{{ $order->total_amount }}</li>
        <li><strong>Status:</strong> {{ $order->status }}</li>
    </ul>

    <h4>Item:</h4>
    <ul>
        @foreach($order->orderItems as $item)
            <li>{{ $item->product->name }} x {{ $item->quantity }} = Rp{{ $item->price * $item->quantity }}</li>
        @endforeach
    </ul>

    <h4>Pengiriman:</h4>
    <p>{{ $order->shipping->shipping_address }} ({{ $order->shipping->delivery_status }})</p>

    <h4>Pembayaran:</h4>
    <p>Metode: {{ $order->payment->payment_method }} - Status: {{ $order->payment->status }}</p>
</div>
@endsection
