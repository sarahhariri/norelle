<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingOrders = Order::where('status', 'pending')->count();

        $confirmedOrders = Order::where('status', 'confirmed')->count();

        $deliveredRevenue = Order::where('status', 'delivered')
            ->sum('subtotal');

        $lowStockCount = ProductVariant::where('stock', '<=', 2)
            ->count();

        $latestOrders = Order::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'pendingOrders',
            'confirmedOrders',
            'deliveredRevenue',
            'lowStockCount',
            'latestOrders',
        ));
    }
}