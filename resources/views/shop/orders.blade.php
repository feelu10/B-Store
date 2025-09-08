@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-tight text-purple-900">My Orders</h1>
      <p class="text-sm text-purple-800/70">Track your purchases and their delivery status.</p>
    </div>

    <div class="hidden sm:flex items-center gap-2">
      <a href="{{ route('shop.index') }}"
         class="inline-flex items-center gap-2 rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm font-medium text-purple-900 hover:bg-purple-50">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M10 19a1 1 0 0 1-1-1v-5H4a1 1 0 0 1-.7-1.7l8-8a1 1 0 0 1 1.4 0l8 8A1 1 0 0 1 20 13h-5v5a1 1 0 0 1-1 1Z"/></svg>
        Continue shopping
      </a>
    </div>
  </div>

  @if ($orders->isEmpty())
    {{-- Empty state --}}
    <div class="relative overflow-hidden rounded-2xl border border-purple-100 bg-white p-8 text-center">
      <div class="pointer-events-none absolute -top-24 -right-20 h-64 w-64 rounded-full bg-gradient-to-br from-purple-100 to-pink-100 blur-2xl"></div>
      <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br from-purple-600 to-pink-500 text-white shadow-sm">
        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor"><path d="M7 5h10l1 2h3a1 1 0 1 1 0 2h-1.2l-1.5 8.1A3 3 0 0 1 15.3 20H8.7a3 3 0 0 1-2.9-2.4L4.3 9H3A1 1 0 1 1 3 7h3l1-2Zm1.1 4 1.1 6.4c.1.8.8 1.6 1.7 1.6h6.4c.9 0 1.6-.7 1.7-1.5L20.7 9H8.1Z"/></svg>
      </div>
      <h2 class="text-lg font-semibold text-purple-900">No orders yet</h2>
      <p class="mt-1 text-sm text-purple-800/70">When you buy something, it’ll show up here.</p>
      <a href="{{ route('shop.index') }}"
         class="mt-4 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 px-4 py-2 text-sm font-semibold text-white shadow hover:from-purple-700 hover:to-pink-600">
        Start shopping
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13.3 5.3a1 1 0 1 0-1.4 1.4L15.6 10H6a1 1 0 1 0 0 2h9.6l-3.7 3.3a1 1 0 0 0 1.4 1.5l5.5-5a1.5 1.5 0 0 0 0-2.2l-5.5-5Z"/></svg>
      </a>
    </div>
  @else
    @php
      // Map status → chip styles
      $chip = [
        'pending'   => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
        'paid'      => 'bg-green-50 text-green-700 ring-1 ring-green-200',
        'processing'=> 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
        'shipped'   => 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200',
        'delivered' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
        'cancelled' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
        'refunded'  => 'bg-slate-50 text-slate-700 ring-1 ring-slate-200',
      ];
      $fmt = fn($n) => '₱' . number_format($n, 2);
    @endphp

    <div class="overflow-hidden rounded-2xl border border-purple-100 bg-white">
      {{-- Sticky header table for md+ --}}
      <div class="hidden md:block">
        <table class="w-full text-sm">
          <thead class="sticky top-0 z-10 bg-gradient-to-r from-purple-50 to-pink-50 text-purple-900">
            <tr class="text-left">
              <th class="p-4 font-semibold">Order</th>
              <th class="p-4 text-right font-semibold">Subtotal</th>
              <th class="p-4 text-right font-semibold">Tax</th>
              <th class="p-4 text-right font-semibold">Total</th>
              <th class="p-4 font-semibold">Status</th>
              <th class="p-4 font-semibold">Placed</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-purple-50">
            @foreach ($orders as $o)
              <tr class="group hover:bg-purple-50/60 transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-purple-600 to-pink-500 text-white shadow-sm">
                      <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 5h10l1 2h3a1 1 0 1 1 0 2h-1.2l-1.5 8.1A3 3 0 0 1 15.3 20H8.7a3 3 0 0 1-2.9-2.4L4.3 9H3A1 1 0 1 1 3 7h3l1-2Z"/></svg>
                    </span>
                    <div>
                      <div class="font-semibold text-purple-900">ORD-{{ $o->id }}</div>
                      <div class="text-xs text-purple-800/70">{{ $o->shipping_name }}</div>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-right tabular-nums">{{ $fmt($o->subtotal) }}</td>
                <td class="p-4 text-right tabular-nums">{{ $fmt($o->tax) }}</td>
                <td class="p-4 text-right tabular-nums font-semibold text-purple-900">{{ $fmt($o->total) }}</td>
                <td class="p-4">
                  @php $s = strtolower($o->status); @endphp
                  <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $chip[$s] ?? 'bg-slate-50 text-slate-700 ring-1 ring-slate-200' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    {{ ucfirst($o->status) }}
                  </span>
                </td>
                <td class="p-4 text-purple-900">
                  <div class="text-sm">{{ $o->created_at->format('M d, Y') }}</div>
                  <div class="text-xs text-purple-800/70">{{ $o->created_at->format('h:i A') }}</div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Mobile cards --}}
      <div class="md:hidden divide-y divide-purple-50">
        @foreach ($orders as $o)
          @php $s = strtolower($o->status); @endphp
          <div class="p-4">
            <div class="flex items-start justify-between">
              <div>
                <div class="font-semibold text-purple-900">ORD-{{ $o->id }}</div>
                <div class="text-xs text-purple-800/70">{{ $o->shipping_name }}</div>
              </div>
              <span class="ml-3 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $chip[$s] ?? 'bg-slate-50 text-slate-700 ring-1 ring-slate-200' }}">
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                {{ ucfirst($o->status) }}
              </span>
            </div>

            <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
              <div class="rounded-xl bg-purple-50 px-3 py-2">
                <div class="text-purple-800/70 text-xs">Subtotal</div>
                <div class="font-medium text-purple-900">{{ $fmt($o->subtotal) }}</div>
              </div>
              <div class="rounded-xl bg-purple-50 px-3 py-2">
                <div class="text-purple-800/70 text-xs">Tax</div>
                <div class="font-medium text-purple-900">{{ $fmt($o->tax) }}</div>
              </div>
              <div class="col-span-2 rounded-xl bg-gradient-to-r from-purple-600/10 to-pink-500/10 px-3 py-2">
                <div class="text-purple-800/70 text-xs">Total</div>
                <div class="font-semibold text-purple-900">{{ $fmt($o->total) }}</div>
              </div>
            </div>

            <div class="mt-3 text-xs text-purple-800/70">
              Placed {{ $o->created_at->format('M d, Y · h:i A') }}
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
      {{ $orders->onEachSide(1)->links() }}
    </div>
  @endif
</div>
@endsection
