<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $q          = trim((string) $request->get('q', ''));
        $categoryId = $request->integer('category_id');
        $sort       = (string) $request->get('sort', 'latest');

        // Sorting map (secure: only allow these)
        $sortMap = [
            'latest'     => ['column' => 'created_at', 'dir' => 'desc'],
            'oldest'     => ['column' => 'created_at', 'dir' => 'asc'],
            'price_low'  => ['column' => 'price',      'dir' => 'asc'],
            'price_high' => ['column' => 'price',      'dir' => 'desc'],
            'name_az'    => ['column' => 'name',       'dir' => 'asc'],
            'name_za'    => ['column' => 'name',       'dir' => 'desc'],
        ];
        $chosen = $sortMap[$sort] ?? $sortMap['latest'];

        $products = Product::query()
            ->with(['images' => fn($q) => $q->orderBy('id', 'asc')])
            ->where('is_active', true)
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                          ->orWhere('short_description', 'like', "%{$q}%")
                          ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->when($categoryId, function ($qb) use ($categoryId) {
                $qb->where('category_id', $categoryId);
            })
            ->orderBy($chosen['column'], $chosen['dir'])
            ->paginate(16)
            ->appends($request->query()); // keep filters in pagination

        $categories = Category::orderBy('name')->get(['id','name']);

        return view('shop.index', compact('products', 'categories', 'q', 'categoryId', 'sort'));
    }

    public function show(string $slug)
    {
        $product = Product::with(['images','category'])   // make sure category is eager-loaded
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // If the product doesn’t have a category, fall back later to generic picks
        $categoryId = $product->category_id;

        // ---- Related (same category, newest)
        $related = collect();
        if ($categoryId) {
            $related = Product::query()
                ->with(['images' => fn($q) => $q->orderBy('id')])
                ->where('is_active', true)
                ->where('category_id', $categoryId)
                ->where('id', '!=', $product->id)
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        }

        // ---- You may also like (same category, randomized)
        $youMayAlsoLike = collect();
        if ($categoryId) {
            $youMayAlsoLike = Product::query()
                ->with(['images' => fn($q) => $q->orderBy('id')])
                ->where('is_active', true)
                ->where('category_id', $categoryId)
                ->where('id', '!=', $product->id)
                ->inRandomOrder()
                ->take(8)
                ->get();
        }

        // ---- Recently viewed (session-based, exclude current)
        $rvKey = 'recently_viewed_product_ids';
        $recentIds = collect(session($rvKey, []))
            ->prepend($product->id)->unique()->take(20)->values();
        session([$rvKey => $recentIds->all()]);

        $recentlyViewed = Product::query()
            ->with(['images' => fn($q) => $q->orderBy('id')])
            ->whereIn('id', $recentIds->reject(fn($id) => $id === $product->id)->take(12))
            ->get()
            ->sortBy(fn($p) => $recentIds->search($p->id))
            ->values();

        // ---- Fallbacks if category is missing or sets ended up empty:
        if ($related->isEmpty()) {
            $related = Product::query()
                ->with(['images' => fn($q) => $q->orderBy('id')])
                ->where('is_active', true)
                ->where('id', '!=', $product->id)
                ->latest()->take(8)->get();
        }
        if ($youMayAlsoLike->isEmpty()) {
            $youMayAlsoLike = Product::query()
                ->with(['images' => fn($q) => $q->orderBy('id')])
                ->where('is_active', true)
                ->where('id', '!=', $product->id)
                ->inRandomOrder()->take(8)->get();
        }

        return view('shop.show', compact('product', 'related', 'youMayAlsoLike', 'recentlyViewed'));
    }

}
