<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon; // pastikan ini di atas
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Shipping;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
{
    // Tambah ke keranjang
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        //$data = $validated->all();

        $cartItem = Cart::where('user_id', $validated['user_id'])
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            $cartItem = Cart::create($validated);
        }

        //Order::create($cartItem);
        return redirect()->route('cart.checkout', ['user_id' => $validated['user_id']])
            ->with('success', 'Item berhasil ditambahkan ke keranjang!');

        /*return response()->json([
            'message' => 'Item ditambahkan ke keranjang',
            'data' => $cartItem,
        ]);*/
    }


    // Lihat isi keranjang
    public function index($user_id)
    {
        $items = Cart::with('product')->where('user_id', $user_id)->get();
        return response()->json($items);
    }

    // Hapus item dari keranjang
    public function destroy($id)
    {
        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Item dihapus dari keranjang']);
    }
    public function showAddForm()
    {
        $products = Product::all();
        $user_id = 1; // Simulasikan user login
        return view('cart.add', compact('products', 'user_id'));
    }

    public function showCheckoutForm($user_id)
    {
        $cartItems = Cart::with('product')->where('user_id', $user_id)->get();
        return view('cart.checkout', compact('cartItems', 'user_id'));
    }
    public function checkout(Request $request, MidtransService $midtrans)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;

        DB::beginTransaction();

        try {
            $cartItems = Cart::with('product')->where('user_id', $userId)->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'Keranjang kosong'], 400);
            }

            $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => $total,
                'status' => 'pending',
                'order_date' => Carbon::now(),
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            Cart::where('user_id', $userId)->delete();

            $order->load(['orderItems.product', 'user']);
            $snap = $midtrans->createTransaction($order);

            DB::commit();

            return view('cart.payment', [
                'snapToken' => $snap->token,
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Checkout gagal',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function showConfirmation($order_id)
    {
        $order = Order::findOrFail($order_id);
        return view('cart.payment', compact('order'));
    }

    // Tampilkan form pembayaran
    public function showPaymentForm($order_id)
    {
        $order = Order::with('orderItems.product')->findOrFail($order_id);
        return view('payments.payment', compact('order'));
    }

    // Proses pembayaran
    public function processPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $order = Order::findOrFail($request->order_id);

        // ✅ Cek apakah status sudah paid
        if ($order->status === 'paid') {
            return redirect()->back()->with('error', 'Pembayaran tidak dapat diproses karena pesanan sudah dibayar.');
        }

        DB::transaction(function () use ($request) {
            Payment::create([
                'order_id' => $request->order_id,
                'amount' => $request->amount,
                'payment_date' => now(),
                'payment_method' => $request->payment_method,
                'status' => 'paid',
            ]);

            Order::where('id', $request->order_id)->update(['status' => 'paid']);
        });

        return redirect()->route('cart.add', ['order_id' => $request->order_id])
            ->with('success', 'Pembayaran berhasil dilakukan.');
    }
}
