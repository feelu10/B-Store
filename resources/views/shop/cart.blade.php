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

  {{-- Checkout / Shipping --}}
  <form action="{{ route('checkout') }}" method="POST" class="mt-6 bg-white rounded shadow p-4 grid md:grid-cols-2 gap-4">
    @csrf
    <div class="md:col-span-2 flex items-center justify-between">
      <h2 class="text-xl font-bold text-purple-700">Shipping Details</h2>
      @auth
        <a href="{{ route('customer.profile.edit') }}" class="text-sm text-purple-700 hover:underline">Edit saved address</a>
      @endauth
    </div>

    {{-- Full name --}}
    <div>
      <label class="block text-sm text-gray-600 mb-1">Full Name</label>
      <input
        name="shipping_name"
        class="w-full border rounded px-3 py-2"
        value="{{ old('shipping_name', $profile->ship_full_name ?? '') }}"
        required>
      @error('shipping_name') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
    </div>

    {{-- Phone --}}
    <div>
      <label class="block text-sm text-gray-600 mb-1">Phone</label>
      <input
        name="shipping_phone"
        type="tel"
        inputmode="tel"
        pattern="^(?:\+1)?\d{10}$"
        title="Enter a valid US phone (10 digits, e.g. 5551234567 or +15551234567)"
        class="w-full border rounded px-3 py-2"
        value="{{ old('shipping_phone', $profile->phone ?? '') }}"
        required>
      @error('shipping_phone') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
    </div>

    {{-- Address line 1 --}}
    <div class="md:col-span-2">
      <label class="block text-sm text-gray-600 mb-1">Address Line 1</label>
      <input
        name="shipping_line1"
        class="w-full border rounded px-3 py-2"
        value="{{ old('shipping_line1', $profile->ship_line1 ?? '') }}"
        required>
      @error('shipping_line1') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
    </div>

    {{-- Address line 2 --}}
    <div class="md:col-span-2">
      <label class="block text-sm text-gray-600 mb-1">Address Line 2 (Optional)</label>
      <input
        name="shipping_line2"
        class="w-full border rounded px-3 py-2"
        value="{{ old('shipping_line2', $profile->ship_line2 ?? '') }}">
      @error('shipping_line2') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
    </div>

    {{-- City --}}
    <div>
      <label class="block text-sm text-gray-600 mb-1">City</label>
      <input
        name="shipping_city"
        class="w-full border rounded px-3 py-2"
        value="{{ old('shipping_city', $profile->ship_city ?? '') }}"
        required>
      @error('shipping_city') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
    </div>

    {{-- State --}}
    <div>
      <label class="block text-sm text-gray-600 mb-1">State</label>
      <input
        name="shipping_state"
        class="w-full border rounded px-3 py-2"
        maxlength="2"
        value="{{ old('shipping_state', $profile->ship_state ?? '') }}"
        required>
      @error('shipping_state') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
    </div>

    {{-- Postal code --}}
    <div>
      <label class="block text-sm text-gray-600 mb-1">Postal Code</label>
      <input
        name="shipping_postal_code"
        class="w-full border rounded px-3 py-2"
        pattern="^\d{5}(?:-\d{4})?$"
        title="Enter a 5-digit ZIP or ZIP+4"
        value="{{ old('shipping_postal_code', $profile->ship_postal_code ?? '') }}"
        required>
      @error('shipping_postal_code') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
    </div>

    {{-- Country --}}
    <div>
      <label class="block text-sm text-gray-600 mb-1">Country</label>
      <input
        name="shipping_country"
        class="w-full border rounded px-3 py-2"
        value="{{ old('shipping_country', $profile->ship_country ?? 'United States') }}"
        required>
      @error('shipping_country') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
    </div>

    {{-- Landmark / Notes --}}
    <div class="md:col-span-2">
      <label class="block text-sm text-gray-600 mb-1">Landmark / Notes (Optional)</label>
      <input
        name="shipping_landmark"
        class="w-full border rounded px-3 py-2"
        value="{{ old('shipping_landmark', $profile->ship_landmark ?? '') }}">
      @error('shipping_landmark') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2 flex justify-end gap-3">
      <a href="{{ route('shop.index') }}" class="px-4 py-2 rounded border">Continue Shopping</a>
      <button class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded">Place Order</button>
    </div>
  </form>
@endif
@endsection
