<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $orders = Order::with(['user'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending'   => Order::where('status','pending')->count(),
            'paid'      => Order::where('status','paid')->count(),
            'shipped'   => Order::where('status','shipped')->count(),
            'completed' => Order::where('status','completed')->count(),
            'cancelled' => Order::where('status','cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders','status','stats'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product','user']);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required','in:pending,paid,shipped,completed,cancelled'],
        ]);
        $order->update(['status' => $request->status]);

        return back()->with('success','Order status updated.');
    }
}
