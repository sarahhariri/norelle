<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
  public function store(Request $request)
{
    $validated = $request->validate([
        'customer_name' => ['required', 'string', 'max:120'],
        'phone' => ['required', 'string', 'max:30'],
        'city' => ['required', 'string', 'max:100'],
        'address' => ['required', 'string', 'max:500'],
        'notes' => ['nullable', 'string', 'max:1000'],
        'items' => ['required', 'array', 'min:1', 'max:30'],
        'items.*' => ['required', 'array:variant_id,quantity'],
        'items.*.variant_id' => ['required', 'integer', 'distinct', 'exists:product_variants,id'],
        'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
    ]);

    $order = DB::transaction(function () use ($validated) {
        $variantIds = collect($validated['items'])->pluck('variant_id');

        $variants = ProductVariant::whereIn('id', $variantIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $products = Product::whereIn('id', $variants->pluck('product_id'))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $lines = [];

        $categories = Category::whereIn(
        'id',
        $products->pluck('category_id')->filter()->unique()
    )
    ->orderBy('id')
    ->lockForUpdate()
    ->get()
    ->keyBy('id');
        $subtotalCents = 0;

        foreach ($validated['items'] as $item) {
            $category = $product
    ? $categories->get($product->category_id)
    : null;
            $variant = $variants->get($item['variant_id']);
            $product = $variant
                ? $products->get($variant->product_id)
                : null;

            if (!$variant || !$product || !$product->is_active || !$category || !$category->is_active) {
                throw ValidationException::withMessages([
                    'items' => 'A product in your bag is no longer available.',
                ]);
            }

            if ($variant->stock < $item['quantity']) {
                throw ValidationException::withMessages([
                    'items' => "{$product->name} ({$variant->size}) has insufficient stock.",
                ]);
            }

            $price = $product->sale_price ?? $product->price;
            $unitCents = (int) round((float) $price * 100);
            $lineCents = $unitCents * $item['quantity'];
            $subtotalCents += $lineCents;

            $lines[] = [
                'variant' => $variant,
                'quantity' => $item['quantity'],
                'product_name' => $product->name,
                'size' => $variant->size,
                'unit_price' => number_format($unitCents / 100, 2, '.', ''),
                'line_total' => number_format($lineCents / 100, 2, '.', ''),
            ];
        }

        if ($subtotalCents > 9_999_999_999) {
            throw ValidationException::withMessages([
                'items' => 'Order total is too large.',
            ]);
        }

        $order = Order::create([
            'customer_name' => $validated['customer_name'],
            'phone' => $validated['phone'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'notes' => $validated['notes'] ?? null,
            'subtotal' => number_format($subtotalCents / 100, 2, '.', ''),
            'payment_method' => 'cod',
            'status' => 'pending',
        ]);

        foreach ($lines as $line) {
            $order->items()->create([
                'product_variant_id' => $line['variant']->id,
                'product_name' => $line['product_name'],
                'size' => $line['size'],
                'unit_price' => $line['unit_price'],
                'quantity' => $line['quantity'],
                'line_total' => $line['line_total'],
            ]);

            $line['variant']->decrement('stock', $line['quantity']);
        }

        return $order;
    });

    return response()->json([
        'success' => true,
        'message' => 'Order placed successfully.',
        'data' => [
            'id' => $order->id,
            'subtotal' => $order->subtotal,
            'status' => $order->status,
        ],
    ], 201);
}
}
