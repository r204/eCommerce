<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Notification;
use Midtrans\Config;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index($user_id)
    {
        $payments = Order::with('order')->whereHas('order', function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->get();

        return view('payments.index', compact('payments'));
    }

    public function show($order_id)
    {
        $payment = Order::with('order.user', 'order.orderItems.product')->findOrFail($order_id);


        if (!$payment) {
            return view('payments.notfound'); // atau redirect / abort(404)
        }

        return view('payments.show', compact('payment'));
    }


    // 🔍 1. Get all payment history for a user
    public function history($user_id)
    {
        $payments = Order::with('order')
            ->whereHas('order', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->orderByDesc('payment_date')
            ->get();

        return response()->json($payments);
    }

    // 🔍 2. Get detail payment for specific order
    public function detail($order_id)
    {
        $payment = Payment::with('order.user')->where('order_id', $order_id)->first();

        if (!$payment) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        return response()->json($payment);
    }
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function midtransCallback(Request $request)
    {
        Log::info('Midtrans Callback Payload', $request->all());

        $data = $request->all();

        $order_id = $data['order_id'] ?? null;
        $status = $data['transaction_status'] ?? null;
        $type = $data['payment_type'] ?? null;
        $fraud = $data['fraud_status'] ?? null;
        $amount = $data['gross_amount'] ?? 0;

        if (!$order_id) {
            return response()->json(['message' => 'Order ID tidak ditemukan'], 400);
        }

        $realOrderId = str_replace('ORDER-', '', $order_id);
        $order = Order::find($realOrderId);

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        if ($status == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $order->status = 'challenge';
                } else {
                    $order->status = 'paid';
                }
            }
        } elseif ($status == 'settlement') {
            $order->status = 'paid';
        } elseif ($status == 'pending') {
            $order->status = 'pending';
        } elseif (in_array($status, ['deny', 'expire', 'cancel'])) {
            $order->status = 'failed';
        }

        $order->save();

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'amount' => $amount,
                'payment_date' => now(),
                'payment_method' => $type,
                'status' => $status,
            ]
        );

        return response()->json(['message' => 'Transaksi Berhasil'], 200);
    }
}
