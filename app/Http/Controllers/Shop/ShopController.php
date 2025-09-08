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
        $product = Product::with('images')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('shop.show', compact('product'));
    }
}
