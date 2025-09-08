<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','max:160'],
            'category_id' => ['nullable','exists:categories,id'],
            'price' => ['required','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'images.*' => ['nullable','image','max:2048'],
            'short_description' => ['nullable','string'],
            'description' => ['nullable','string'],
            'is_active' => ['nullable','boolean'],
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'slug' => Str::slug($validated['name']).'-'.Str::random(6),
            'is_active' => (bool)($validated['is_active'] ?? true),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products','public');
                ProductImage::create(['product_id' => $product->id, 'path' => $path]);
            }
        }

        return redirect()->route('admin.products.index')->with('success','Product created!');
    }

    public function edit(Product $product)
    {
        $product->load('images');
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product','categories'));
    }

    public function update(Request $request, Product $product)
    {
        // IMPORTANT: allow turning OFF is_active (hidden 0 + checkbox 1 in the view)
        $validated = $request->validate([
            'name' => ['required','max:160'],
            'category_id' => ['nullable','exists:categories,id'],
            'price' => ['required','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'images.*' => ['nullable','image','max:2048'],
            'short_description' => ['nullable','string'],
            'description' => ['nullable','string'],
            'is_active' => ['required','boolean'],
        ]);

        $product->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => (bool)$validated['is_active'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products','public');
                ProductImage::create(['product_id' => $product->id, 'path' => $path]);
            }
        }

        return redirect()->route('admin.products.index')->with('success','Updated!');
    }

    public function destroy(Product $product)
    {
        // delete all related images from storage
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $product->images()->delete();
        $product->delete();

        return back()->with('success','Deleted.');
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        abort_if($image->product_id !== $product->id, 404);
        Storage::disk('public')->delete($image->path);
        $image->delete();
        return back()->with('success', 'Image removed.');
    }

    public function stock(Product $product): JsonResponse
    {
        if (!$product->is_active) {
            return response()->json(['stock' => 0]);
        }
        return response()->json(['stock' => (int) $product->stock]);
    }
}
