<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
  public function index()
{
    $products = Product::with(['category', 'variants'])
        ->where('is_active', true)
        ->whereHas('category', function ($query) {
            $query->where('is_active', true);
        })
        ->orderBy('order')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $products,
    ]);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $product = Product::create($validated);
        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $product,
        ], 201);
    }

   public function show(string $slug)
{
    $product = Product::with(['category', 'variants'])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->whereHas('category', function ($query) {
            $query->where('is_active', true);
        })
        ->firstOrFail();

    return response()->json([
        'success' => true,
        'data' => $product,
    ]);
}
}