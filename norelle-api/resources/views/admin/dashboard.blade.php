<x-admin.layout
    title="Dashboard" body-class="admin-dashboard">

    <main class="container py-5">
        <div class="mb-4">
            <p class="dashboard-eyebrow mb-2">
                STORE OVERVIEW
            </p>

            <h1 class="dashboard-title mb-1">
                Dashboard
            </h1>

            <p class="text-secondary mb-0">
                A quick overview of your store activity.
            </p>
        </div>

        <div class="row g-3 mb-5">
            <div class="col-12 col-sm-6 col-xl-3">
                <a
                    href="{{ route('admin.orders.index', ['filter' => 'in-progress']) }}"
                    class="dashboard-stat pending"
                >
                    <span class="dashboard-stat-label">
                        Pending orders
                    </span>

                    <strong>{{ $pendingOrders }}</strong>

                    <span class="dashboard-stat-link">
                        View orders →
                    </span>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <a
                    href="{{ route('admin.orders.index', ['filter' => 'in-progress']) }}"
                    class="dashboard-stat confirmed"
                >
                    <span class="dashboard-stat-label">
                        Confirmed orders
                    </span>

                    <strong>{{ $confirmedOrders }}</strong>

                    <span class="dashboard-stat-link">
                        View orders →
                    </span>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="dashboard-stat revenue">
                    <span class="dashboard-stat-label">
                        Delivered revenue
                    </span>

                    <strong>
                        ${{ number_format((float) $deliveredRevenue, 2) }}
                    </strong>

                    <span class="dashboard-stat-link">
                        Completed orders
                    </span>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <a
                    href="{{ route('admin.products.index', ['low_stock' => 1]) }}"
                    class="dashboard-stat stock"
                >
                    <span class="dashboard-stat-label">
                        Low stock sizes
                    </span>

                    <strong>{{ $lowStockCount }}</strong>

                    <span class="dashboard-stat-link">
                        Review stock →
                    </span>
                </a>
            </div>
        </div>

        <section class="dashboard-panel">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <div>
                    <p class="dashboard-eyebrow mb-1">
                        RECENT ACTIVITY
                    </p>

                    <h2 class="dashboard-section-title mb-0">
                        Latest orders
                    </h2>
                </div>

                <a
                    href="{{ route('admin.orders.index', ['filter' => 'all']) }}"
                    class="dashboard-view-all"
                >
                    View all
                </a>
            </div>

            @forelse ($latestOrders as $order)
                <div class="dashboard-order d-flex align-items-start align-items-center justify-content-between gap-3">
                    <div>
                        <span class="dashboard-order-number">
                            Order #{{ $order->id }}
                        </span>

                        <h3>{{ $order->customer_name }}</h3>

                        <small>
                            {{ $order->created_at->format('d M Y, h:i A') }}
                        </small>
                    </div>

                    <div class="dashboard-order-meta d-flex flex-column flex-sm-row align-items-end align-items-sm-center gap-2">
                        <strong>
                            ${{ number_format((float) $order->subtotal, 2) }}
                        </strong>

                        <span class="dashboard-status {{ $order->status }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center text-secondary py-5">
                    No orders yet.
                </div>
            @endforelse
        </section>
    </main>
</x-admin.layout>
