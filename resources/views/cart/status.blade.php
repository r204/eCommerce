@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card p-4 shadow">
        <h4>Status Pembayaran</h4>
        <p>Order ID: {{ $order->id }}</p>
        <p>Status: <strong class="text-uppercase">{{ $order->status }}</strong></p>
        @if($order->status === 'paid')
        <div class="alert alert-success">Pembayaran berhasil diterima!</div>
        @elseif($order->status === 'pending')
        <div class="alert alert-warning">Menunggu pembayaran...</div>
        @elseif($order->status === 'failed')
        <div class="alert alert-danger">Pembayaran gagal atau kadaluarsa.</div>
        @endif
    </div>
</div>
@endsection