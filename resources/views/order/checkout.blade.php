@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Checkout</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('order.checkout') }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user_id }}">

        <h4>Items:</h4>
        <ul>
            @foreach($cartItems as $item)
                <li>{{ $item->product->name }} x {{ $item->quantity }} = Rp{{ $item->product->price * $item->quantity }}</li>
            @endforeach
        </ul>

        <div class="form-group">
            <label for="payment_method">Metode Pembayaran:</label>
            <input type="text" name="payment_method" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="shipping_address">Alamat Pengiriman:</label>
            <textarea name="shipping_address" class="form-control" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Checkout</button>
    </form>
</div>
@endsection
