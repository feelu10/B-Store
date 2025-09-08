@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-purple-800">Order ORD-{{ $order->id }}</h1>
    <p class="text-sm text-gray-600">Placed {{ $order->created_at->format('M d, Y h:i A') }}</p>
  </div>
  <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 rounded-lg border">Back</a>
</div>

<div class="grid lg:grid-cols-3 gap-6">
  {{-- Left: Items --}}
  <div class="lg:col-span-2 space-y-6">
    <div class="bg-white rounded-2xl shadow overflow-hidden">
      <div class="px-4 py-3 bg-purple-100 text-purple-900 font-semibold">Items</div>
      <div class="p-4 overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left">
              <th class="p-2">Product</th>
              <th class="p-2 text-right">Qty</th>
              <th class="p-2 text-right">Price</th>
              <th class="p-2 text-right">Line Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $it)
              <tr class="border-t">
                <td class="p-2">{{ $it->product?->name ?? '—' }}</td>
                <td class="p-2 text-right">{{ $it->qty }}</td>
                <td class="p-2 text-right">₱{{ number_format($it->price,2) }}</td>
                <td class="p-2 text-right">₱{{ number_format($it->line_total,2) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot class="border-t">
            <tr>
              <td class="p-2"></td>
              <td class="p-2"></td>
              <td class="p-2 text-right font-medium">Subtotal</td>
              <td class="p-2 text-right font-medium">₱{{ number_format($order->subtotal,2) }}</td>
            </tr>
            <tr>
              <td class="p-2"></td>
              <td class="p-2"></td>
              <td class="p-2 text-right font-medium">Tax</td>
              <td class="p-2 text-right font-medium">₱{{ number_format($order->tax,2) }}</td>
            </tr>
            <tr>
              <td class="p-2"></td>
              <td class="p-2"></td>
              <td class="p-2 text-right font-extrabold">Total</td>
              <td class="p-2 text-right font-extrabold text-pink-600">₱{{ number_format($order->total,2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  {{-- Right: Customer + Shipping + Status --}}
  <div class="space-y-6">
    <div class="bg-white rounded-2xl shadow p-4">
      <div class="font-semibold text-purple-800 mb-2">Customer</div>
      <div class="text-sm">
        <div class="font-medium">{{ $order->user?->name ?? '—' }}</div>
        <div class="text-gray-600">{{ $order->user?->email ?? '' }}</div>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4">
      <div class="font-semibold text-purple-800 mb-2">Shipping</div>
      <div class="text-sm">
        <div>{{ $order->shipping_name }}</div>
        <div>{{ $order->shipping_phone }}</div>
        <div>{{ $order->shipping_address }}</div>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4">
      <div class="font-semibold text-purple-800 mb-3">Status</div>
      <form action="{{ route('admin.orders.update',$order) }}" method="POST" class="flex items-center gap-2">
        @csrf @method('PUT')
        <select name="status" class="border rounded-lg px-3 py-2">
          @foreach (['pending','paid','shipped','completed','cancelled'] as $s)
            <option value="{{ $s }}" @selected($order->status===$s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
        <button class="px-4 py-2 rounded-lg text-white bg-pink-500 hover:bg-pink-600">Update</button>
      </form>
    </div>
  </div>
</div>
@endsection
