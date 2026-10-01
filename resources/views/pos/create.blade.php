@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
<h1 class="text-lg font-semibold mb-4">Transaksi Kasir</h1>

@if (session('success'))
<div class="bg-green-50 text-green-700 p-3 rounded-md mb-4">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="bg-red-50 text-red-700 p-3 rounded-md mb-4">
    <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('transactions.store') }}" x-data="{
    cart: [],
    errorMessage: '',
    addToCart(id, name, price, stock) {
        this.errorMessage = '';
        if (stock <= 0) {
            this.errorMessage = 'Stok untuk produk ' + name + ' sudah habis!';
            return;
        }
        let existingItem = this.cart.find(item => item.id === id);
        if (existingItem) {
            if (existingItem.qty >= stock) {
                this.errorMessage = 'Stok untuk produk ' + name + ' tidak mencukupi (tersedia: ' + stock + ').';
                return;
            }
            existingItem.qty++;
        } else {
            this.cart.push({ id: id, name: name, price: price, qty: 1, stock: stock });
        }
    },
    decreaseQty(id) {
        this.errorMessage = '';
        let existingItem = this.cart.find(item => item.id === id);
        if (existingItem) {
            if (existingItem.qty > 1) {
                existingItem.qty--;
            } else {
                this.cart = this.cart.filter(item => item.id !== id);
            }
        }
    },
    removeFromCart(id) {
        this.errorMessage = '';
        this.cart = this.cart.filter(item => item.id !== id);
    },
    subtotal() {
        return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    }
}">
    @csrf

    <template x-if="errorMessage">
        <div class="bg-amber-50 border border-amber-200 text-amber-800 p-3 rounded-md mb-4 flex justify-between items-center">
            <span x-text="errorMessage"></span>
            <button type="button" @click="errorMessage = ''" class="text-amber-800 font-bold">&times;</button>
        </div>
    </template>

    <div class="grid grid-cols-3 gap-4">
        @foreach ($products as $product)
        <div class="border rounded-md p-3 cursor-pointer hover:bg-slate-50 transition {{ $product->stock <= 0 ? 'opacity-50' : '' }}"
             @click="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->stock }})">
            <p class="font-medium">{{ $product->name }}</p>
            <p class="text-sm text-slate-500">Rp {{ number_format($product->price) }}</p>
            <p class="text-xs {{ $product->stock <= 0 ? 'text-red-500 font-medium' : 'text-slate-400' }}">
                Stok: {{ $product->stock }} {{ $product->stock <= 0 ? '(Habis)' : '' }}
            </p>
        </div>
        @endforeach
    </div>

    <div class="mt-4 border-t pt-3">
        <template x-for="(item, index) in cart" :key="index">
            <div class="flex items-center justify-between py-1">
                <div>
                    <span class="font-medium" x-text="item.name"></span>
                    <span class="text-slate-500 text-sm" x-text="' - Rp ' + Number(item.price).toLocaleString() + ' (x' + item.qty + ')'"></span>
                    <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.id">
                    <input type="hidden" :name="'items[' + index + '][qty]'" :value="item.qty">
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" @click="decreaseQty(item.id)" class="px-2 py-0.5 bg-slate-200 hover:bg-slate-300 rounded text-sm">-</button>
                    <span class="px-2 text-sm" x-text="item.qty"></span>
                    <button type="button" @click="addToCart(item.id, item.name, item.price, item.stock)" class="px-2 py-0.5 bg-slate-200 hover:bg-slate-300 rounded text-sm">+</button>
                    <button type="button" @click="removeFromCart(item.id)" class="ml-2 text-red-500 hover:text-red-700 text-sm font-bold">&times;</button>
                </div>
            </div>
        </template>

        <p class="font-semibold mt-2">Subtotal: Rp <span x-text="subtotal().toLocaleString()"></span></p>
        <button type="submit" :disabled="cart.length === 0" class="mt-3 bg-blue-600 disabled:bg-slate-400 text-white px-4 py-2 rounded-md">Bayar</button>
    </div>
</form>
@endsection
