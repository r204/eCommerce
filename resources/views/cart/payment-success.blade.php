@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="alert alert-success">
        <h4>Pembayaran Berhasil!</h4>
        <p>Terima kasih! Pembayaran Anda untuk pesanan #{{ $order->id }} telah diterima.</p>
    </div>
</div>
@endsection