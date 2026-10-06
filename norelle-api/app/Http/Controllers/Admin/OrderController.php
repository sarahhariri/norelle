<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
        public function index(Request $request)
{
    $filter = $request->query('filter', 'in-progress');

    if (! in_array($filter, ['in-progress', 'delivered', 'cancelled', 'all'], true)) {
        $filter = 'in-progress';
    }

    $orders = Order::with('items')
        ->when($filter === 'in-progress', fn ($query) =>
            $query->whereIn('status', ['pending', 'confirmed'])
        )
        ->when(in_array($filter, ['delivered', 'cancelled'], true), fn ($query) =>
            $query->where('status', $filter)
        )
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('admin.orders.index', compact('orders', 'filter'));
}

    public function updateStatus(Request $request, Order $order)
{
    $data = $request->validate([
        'status' => ['required', 'in:confirmed,delivered,cancelled'],
    ]);

    DB::transaction(function () use ($order, $data) {
        $currentOrder = Order::whereKey($order->id)
            ->lockForUpdate()
            ->firstOrFail();

        $allowed = match ($currentOrder->status) {
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['delivered', 'cancelled'],
            default => [],
        };

        if (!in_array($data['status'], $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => 'This status change is not allowed.',
            ]);
        }

        if ($data['status'] === 'cancelled') {
            $items = $currentOrder->items()
                ->orderBy('product_variant_id')
                ->get();

            foreach ($items as $item) {
                if (!$item->product_variant_id) {
                    continue;
                }

                $variant = ProductVariant::whereKey($item->product_variant_id)
                    ->lockForUpdate()
                    ->first();

                $variant?->increment('stock', $item->quantity);
            }
        }

        $currentOrder->update(['status' => $data['status']]);
    }, attempts: 3);

    return back()->with('success', 'Order status updated.');
}
}
