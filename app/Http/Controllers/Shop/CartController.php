<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function cart()
    {
        return session()->get('cart', []);
    }

    public function index()
    {
        $cart = $this->cart();
        $items = collect($cart);
        $subtotal = $items->sum(fn($i) => $i['qty'] * $i['price']);

        // --- Prefill object for Blade ---
        $profile = null;

        if (Auth::check()) {
            // Do NOT auto-create a record here; just preload (no DB write).
            $profile = CustomerProfile::firstOrNew(['user_id' => Auth::id()]);

            // If no saved profile yet, seed display-only defaults from users.name
            if (!$profile->exists) {
                $first = null; $last = null;
                if (!empty(Auth::user()->name)) {
                    $parts = preg_split('/\s+/', trim(Auth::user()->name), 2);
                    $first = $parts[0] ?? null;
                    $last  = $parts[1] ?? null;
                }

                // Seed fields used by the checkout form; these are NOT persisted here
                $profile->first_name       = $profile->first_name ?? $first;
                $profile->last_name        = $profile->last_name  ?? $last;
                $profile->ship_full_name   = $profile->ship_full_name ?? trim(($first ?? '').' '.($last ?? '')) ?: null;
                $profile->ship_country     = $profile->ship_country ?? 'United States';
                $profile->bill_country     = $profile->bill_country ?? 'United States';
                // leave address/phone null unless previously saved
            }
        }

        return view('shop.cart', compact('items', 'subtotal', 'profile'));
    }

    public function add(Request $request, Product $product)
    {
        $qty = max(1, (int) $request->input('qty', 1));
        $cart = $this->cart();

        if (!isset($cart[$product->id])) {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => (float) $product->price,
                'image'      => $product->image_path,
                'qty'        => 0,
                'slug'       => $product->slug,
            ];
        }
        $cart[$product->id]['qty'] += $qty;
        session()->put('cart', $cart);

        return back()->with('success', 'Added to cart!');
    }

    public function remove(Request $request, Product $product)
    {
        $cart = $this->cart();
        unset($cart[$product->id]);
        session()->put('cart', $cart);

        return back()->with('success', 'Removed from cart.');
    }
}
