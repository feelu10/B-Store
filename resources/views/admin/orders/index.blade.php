@extends('layouts.admin')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
  <div>
    <h1 class="text-2xl font-bold text-purple-800">Customer Orders</h1>
    <p class="text-sm text-gray-600">Review, filter and manage orders.</p>
  </div>
  <form method="GET" class="flex items-center gap-2">
    <select name="status" class="border rounded-lg px-3 py-2">
      <option value="">All statuses</option>
      @foreach (['pending','paid','shipped','completed','cancelled'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <button class="px-4 py-2 rounded-lg text-white bg-pink-500 hover:bg-pink-600">Filter</button>
    @if(request('status'))
      <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 rounded-lg border">Reset</a>
    @endif
  </form>
</div>

{{-- Quick stats --}}
<div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
  @foreach (['pending','paid','shipped','completed','cancelled'] as $s)
    <div class="bg-white rounded-2xl shadow p-4">
      <div class="text-xs uppercase text-gray-500">{{ strtoupper($s) }}</div>
      <div class="text-2xl font-extrabold text-purple-700">{{ number_format($stats[$s] ?? 0) }}</div>
    </div>
  @endforeach
</div>

<div class="bg-white rounded-2xl shadow overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="bg-purple-100 text-purple-900">
      <tr>
        <th class="text-left p-3">Order #</th>
        <th class="text-left p-3">Customer</th>
        <th class="text-right p-3">Total</th>
        <th class="text-left p-3">Status</th>
        <th class="text-left p-3">Placed</th>
        <th class="p-3"></th>
      </tr>
    </thead>
    <tbody>
      @forelse($orders as $o)
        <tr class="border-t">
          <td class="p-3">ORD-{{ $o->id }}</td>
          <td class="p-3">
            <div class="font-medium">{{ $o->user?->name ?? '—' }}</div>
            <div class="text-gray-500">{{ $o->user?->email ?? '' }}</div>
          </td>
          <td class="p-3 text-right font-semibold">₱{{ number_format($o->total,2) }}</td>
          <td class="p-3">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
              @class([
                'bg-yellow-100 text-yellow-800' => $o->status==='pending',
                'bg-blue-100 text-blue-800'     => $o->status==='paid',
                'bg-purple-100 text-purple-800' => $o->status==='shipped',
                'bg-green-100 text-green-800'   => $o->status==='completed',
                'bg-red-100 text-red-800'       => $o->status==='cancelled',
              ])
            ">{{ ucfirst($o->status) }}</span>
          </td>
          <td class="p-3">{{ $o->created_at->format('M d, Y h:i A') }}</td>
          <td class="p-3 text-right">
            <a href="{{ route('admin.orders.show',$o) }}" class="text-pink-600 hover:underline">View</a>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="p-6 text-center text-gray-500">No orders found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $orders->links() }}</div>
@endsection
