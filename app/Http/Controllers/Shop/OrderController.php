<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function myOrders()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->paginate(10);
        return view('shop.orders', compact('orders'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'shipping_name' => ['required','max:120'],
            'shipping_phone' => ['required','max:40'],
            'shipping_address' => ['required','max:255'],
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return back()->with('success','Your cart is empty.');
        }

        DB::transaction(function () use ($cart, $request) {
            $subtotal = collect($cart)->sum(fn($i) => $i['qty'] * $i['price']);
            $tax = round($subtotal * 0.12, 2);
            $total = $subtotal + $tax;

            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
            ]);

            foreach ($cart as $row) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $row['product_id'],
                    'qty' => $row['qty'],
                    'price' => $row['price'],
                    'line_total' => $row['qty'] * $row['price'],
                ]);

                // simple stock decrement (optional)
                Product::where('id', $row['product_id'])->decrement('stock', $row['qty']);
            }

            session()->forget('cart');
        });

        return redirect()->route('orders.my')->with('success','Order placed! Thank you 💖');
    }
}
