<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

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
        return view('shop.cart', compact('items', 'subtotal'));
    }

    public function add(Request $request, Product $product)
    {
        $qty = max(1, (int)$request->input('qty', 1));
        $cart = $this->cart();

        if (!isset($cart[$product->id])) {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float)$product->price,
                'image' => $product->image_path,
                'qty' => 0,
                'slug' => $product->slug,
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

        return back()->with('success','Removed from cart.');
    }
}
