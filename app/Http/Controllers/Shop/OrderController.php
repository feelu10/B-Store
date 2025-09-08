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

        $data = $request->validate([
            'shipping_name'        => ['required','string','max:160'],

            'shipping_phone'       => ['required','regex:/^(?:\+1)?\d{10}$/'],

            'shipping_address'     => ['required_without:shipping_line1,shipping_city,shipping_state,shipping_postal_code,shipping_country','string','max:255'],

            'shipping_line1'       => ['required_without:shipping_address','string','max:255'],
            'shipping_line2'       => ['nullable','string','max:255'],
            'shipping_city'        => ['required_without:shipping_address','string','max:160'],
            'shipping_state'       => ['required_without:shipping_address','string','size:2'],
            'shipping_postal_code' => ['required_without:shipping_address','regex:/^\d{5}(?:-\d{4})?$/'],
            'shipping_country'     => ['required_without:shipping_address','string','max:120'],

            'shipping_landmark'    => ['nullable','string','max:255'],
        ], [
            'shipping_phone.regex'       => 'Enter a valid US phone (10 digits, with or without +1).',
            'shipping_state.size'        => 'Use the 2-letter state code (e.g., CA, NY).',
            'shipping_postal_code.regex' => 'Use a 5-digit ZIP or ZIP+4 (12345 or 12345-6789).',
            'shipping_address.required_without' => 'Enter a single-line address or fill the detailed address fields.',
            'shipping_line1.required_without'   => 'Address line 1 is required when not using single-line address.',
            'shipping_city.required_without'    => 'City is required when not using single-line address.',
            'shipping_state.required_without'   => 'State is required when not using single-line address.',
            'shipping_postal_code.required_without' => 'ZIP is required when not using single-line address.',
            'shipping_country.required_without' => 'Country is required when not using single-line address.',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return back()->with('success','Your cart is empty.');
        }

        // --- Normalize phone/state/zip ---
        $digits = preg_replace('/\D/', '', $data['shipping_phone']);
        $phone  = strlen($digits) === 11 && str_starts_with($digits, '1')
                ? '+1' . substr($digits, 1)
                : '+1' . substr($digits, 0, 10);
        $state  = isset($data['shipping_state']) ? strtoupper($data['shipping_state']) : null;
        $zip    = isset($data['shipping_postal_code']) ? strtoupper($data['shipping_postal_code']) : null;

        // --- Build single-line address for orders table ---
        if (!empty($data['shipping_address'])) {
            // Old style: use as-is (still trim)
            $oneLineAddress = trim($data['shipping_address']);
        } else {
            // New style: compose
            $parts = array_filter([
                $data['shipping_line1'] ?? null,
                $data['shipping_line2'] ?? null,
                $data['shipping_city'] ?? null,
                $state,
                $zip,
                $data['shipping_country'] ?? null,
            ]);
            $oneLineAddress = implode(', ', $parts);
        }

        DB::transaction(function () use ($cart, $data, $phone, $state, $zip, $oneLineAddress) {
            $subtotal = collect($cart)->sum(fn($i) => $i['qty'] * $i['price']);
            $tax = round($subtotal * 0.12, 2);
            $total = $subtotal + $tax;

            // --- Upsert FULL address to CustomerProfile ---
            $profile = \App\Models\CustomerProfile::firstOrCreate(['user_id' => Auth::id()]);
            // If only single-line was provided, try to keep existing detailed fields (don’t blank them)
            $profile->fill([
                'phone'            => $phone,
                'ship_full_name'   => $data['shipping_name'],

                // detailed fields only if present; otherwise keep old profile values
                'ship_line1'       => $data['shipping_line1'] ?? $profile->ship_line1,
                'ship_line2'       => $data['shipping_line2'] ?? $profile->ship_line2,
                'ship_city'        => $data['shipping_city']  ?? $profile->ship_city,
                'ship_state'       => $state                  ?? $profile->ship_state,
                'ship_postal_code' => $zip                    ?? $profile->ship_postal_code,
                'ship_country'     => $data['shipping_country'] ?? $profile->ship_country ?? 'United States',
                'ship_landmark'    => $data['shipping_landmark'] ?? $profile->ship_landmark,

                'ship_is_default'  => true,
            ])->save();

            // --- Create order snapshot (still using your current orders schema) ---
            $order = \App\Models\Order::create([
                'user_id'         => Auth::id(),
                'status'          => 'pending',
                'subtotal'        => $subtotal,
                'tax'             => $tax,
                'total'           => $total,
                'shipping_name'   => $data['shipping_name'],
                'shipping_phone'  => $phone,
                'shipping_address'=> $oneLineAddress, // snapshot string
            ]);

            foreach ($cart as $row) {
                \App\Models\OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $row['product_id'],
                    'qty'        => $row['qty'],
                    'price'      => $row['price'],
                    'line_total' => $row['qty'] * $row['price'],
                ]);

                \App\Models\Product::where('id', $row['product_id'])
                    ->decrement('stock', $row['qty']);
            }

            session()->forget('cart');
        });

        return redirect()->route('orders.my')->with('success','Order placed! Thank you 💖');
    }
}
