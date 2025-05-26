@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow p-4">
        <h4 class="mb-3">Bayar Pesanan #{{ $order->id }}</h4>
        <p>Total Pembayaran: <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>

        <button id="pay-button" class="btn btn-primary">Bayar Sekarang</button>
    </div>
</div>

<!-- Midtrans Snap.js -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

<script>
    document.getElementById('pay-button').addEventListener('click', function() {
        window.snap.pay("{{ $snapToken }}", {
            onSuccess: function(result) {
                alert("Pembayaran sukses!");
                console.log(result);
                window.location.href = "/cart/payment-success/{{ $order->id }}";
            },
            onPending: function(result) {
                alert("Menunggu pembayaran...");
                console.log(result);
            },
            onError: function(result) {
                alert("Pembayaran gagal.");
                console.log(result);
            },
            onClose: function() {
                alert("Popup ditutup tanpa menyelesaikan pembayaran.");
            }
        });
    });
</script>
@endsection