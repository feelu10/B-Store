@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold text-purple-700 mb-4">My Orders</h1>

@if($orders->isEmpty())
  <div class="bg-white rounded px-4 py-6">No orders yet.</div>
@else
  <div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full">
      <thead class="bg-purple-100 text-purple-800">
        <tr>
          <th class="text-left p-3">#</th>
          <th class="text-right p-3">Subtotal</th>
          <th class="text-right p-3">Tax</th>
          <th class="text-right p-3">Total</th>
          <th class="text-left p-3">Status</th>
          <th class="text-left p-3">Placed</th>
        </tr>
      </thead>
      <tbody>
        @foreach($orders as $o)
          <tr class="border-b">
            <td class="p-3">ORD-{{ $o->id }}</td>
            <td class="p-3 text-right">₱{{ number_format($o->subtotal,2) }}</td>
            <td class="p-3 text-right">₱{{ number_format($o->tax,2) }}</td>
            <td class="p-3 text-right font-bold">₱{{ number_format($o->total,2) }}</td>
            <td class="p-3">{{ ucfirst($o->status) }}</td>
            <td class="p-3">{{ $o->created_at->format('M d, Y h:i A') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $orders->links() }}</div>
@endif
@endsection
