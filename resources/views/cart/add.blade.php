@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Daftar Produk</h1>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($products as $product)
        <div class="bg-white shadow-md rounded-lg p-4 flex flex-col justify-between">
            <div>
                <img src="{{ $product->image_url ?? 'https://via.placeholder.com/300' }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded">
                <h2 class="text-xl font-semibold mt-2">{{ $product->name }}</h2>
                <p class="text-gray-700 mt-1">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
            </div>

            <form action="{{ route('cart.store') }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user_id }}">
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <label for="quantity-{{ $product->id }}" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                <input type="number" name="quantity" id="quantity-{{ $product->id }}" min="1" value="1"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

                <button type="submit"
                    class="mt-3 w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
                    Tambah ke Keranjang
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection