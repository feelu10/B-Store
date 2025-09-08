@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold text-purple-700 mb-4">Your Cart</h1>

@if($items->isEmpty())
  <div class="bg-white rounded px-4 py-6">Your cart is empty.</div>
@else
  <div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full">
      <thead class="bg-purple-100 text-purple-800">
        <tr>
          <th class="text-left p-3">Product</th>
          <th class="text-right p-3">Qty</th>
          <th class="text-right p-3">Price</th>
          <th class="text-right p-3">Line</th>
          <th class="p-3"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $row)
          <tr class="border-b">
            <td class="p-3">
              <a class="text-purple-700 hover:underline" href="{{ route('shop.show', $row['slug']) }}">{{ $row['name'] }}</a>
            </td>
            <td class="p-3 text-right">{{ $row['qty'] }}</td>
            <td class="p-3 text-right">₱{{ number_format($row['price'],2) }}</td>
            <td class="p-3 text-right">₱{{ number_format($row['qty']*$row['price'],2) }}</td>
            <td class="p-3 text-right">
              <form action="{{ route('cart.remove', $row['product_id']) }}" method="POST">
                @csrf
                <button class="text-pink-600 hover:underline">Remove</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" class="p-3 text-right font-bold">Subtotal</td>
          <td class="p-3 text-right font-bold">₱{{ number_format($subtotal,2) }}</td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <form action="{{ route('checkout') }}" method="POST" class="mt-6 bg-white rounded shadow p-4 grid md:grid-cols-3 gap-4">
    @csrf
    <h2 class="md:col-span-3 text-xl font-bold text-purple-700">Shipping Details</h2>

    <div>
      <label class="block text-sm text-gray-600 mb-1">Full Name</label>
      <input name="shipping_name" class="w-full border rounded px-3 py-2" required>
      @error('shipping_name') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div>
      <label class="block text-sm text-gray-600 mb-1">Phone</label>
      <input name="shipping_phone" class="w-full border rounded px-3 py-2" required>
      @error('shipping_phone') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
    </div>
    <div>
      <label class="block text-sm text-gray-600 mb-1">Address</label>
      <input name="shipping_address" class="w-full border rounded px-3 py-2" required>
      @error('shipping_address') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-3 flex justify-end gap-3">
      <a href="{{ route('shop.index') }}" class="px-4 py-2 rounded border">Continue Shopping</a>
      <button class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded">Place Order</button>
    </div>
  </form>
@endif
@endsection
