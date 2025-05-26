<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'shipping_address' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Hitung total
            $total = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $total += $product->price * $item['quantity'];
            }

            // Buat order
            $order = Order::create([
                'user_id' => $validated['user_id'],
                'order_date' => now(),
                'status' => 'pending',
                'total_amount' => $total
            ]);

            // Buat item
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Kurangi stok
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok produk '{$product->name}' tidak cukup.");
                }

                $product->decrement('stock', $item['quantity']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ]);
            }

            // Buat pembayaran
            Payment::create([
                'order_id' => $order->id,
                'payment_date' => now(),
                'payment_method' => $validated['payment_method'],
                'amount' => $total,
                'status' => 'paid'
            ]);

            // Buat pengiriman
            Shipping::create([
                'order_id' => $order->id,
                'shipping_address' => $validated['shipping_address'],
                'shipping_date' => now()->addDays(1),
                'delivery_status' => 'processing'
            ]);

            DB::commit();

            return response()->json(['message' => 'Order berhasil dibuat', 'order' => $order], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal membuat order', 'error' => $e->getMessage()], 500);
        }
    }
    public function show($id)
    {
        $order = Order::with(['orderItems.product', 'payment', 'shipping', 'user'])->findOrFail($id);

        return response()->json($order);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string'
        ]);

        $order = Order::findOrFail($id);
        $order->status = $validated['status'];
        $order->save();

        return response()->json(['message' => 'Status pesanan diperbarui', 'order' => $order]);
    }
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);

            // Kembalikan stok
            foreach ($order->orderItems as $item) {
                $product = Product::findOrFail($item->product_id);
                $product->increment('stock', $item->quantity);
            }

            // Hapus data terkait
            $order->orderItems()->delete();
            $order->payment()->delete();
            $order->shipping()->delete();
            $order->delete();

            DB::commit();

            return response()->json(['message' => 'Pesanan berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus pesanan', 'error' => $e->getMessage()], 500);
        }
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payment_method' => 'required|string',
            'shipping_address' => 'required|string',
        ]);

        $cartItems = Cart::with('product')->where('user_id', $validated['user_id'])->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong'], 400);
        }

        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($cartItems as $item) {
                if ($item->product->stock < $item->quantity) {
                    throw new \Exception("Stok tidak cukup untuk produk: " . $item->product->name);
                }
                $total += $item->product->price * $item->quantity;
            }

            $order = Order::create([
                'user_id' => $validated['user_id'],
                'order_date' => now(),
                'status' => 'pending',
                'total_amount' => $total
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_date' => now(),
                'payment_method' => $validated['payment_method'],
                'amount' => $total,
                'status' => 'paid'
            ]);

            Shipping::create([
                'order_id' => $order->id,
                'shipping_address' => $validated['shipping_address'],
                'shipping_date' => now()->addDays(1),
                'delivery_status' => 'processing'
            ]);

            // Kosongkan keranjang
            Cart::where('user_id', $validated['user_id'])->delete();

            DB::commit();
            return response()->json(['message' => 'Checkout berhasil', 'order' => $order]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Checkout gagal', 'error' => $e->getMessage()], 500);
        }
    }
}
