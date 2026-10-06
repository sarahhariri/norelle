<x-admin.layout
    title="Orders"
    body-class="admin-orders">

    <main class="shell">
        <p class="eyebrow">Order management</p>
        <h1>Orders <span>{{ $orders->total() }} total</span></h1>
        <nav class="d-flex flex-wrap gap-2 mb-4" aria-label="Filter orders">
            @foreach ([
        'in-progress' => 'In progress',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
        'all' => 'All',
             ] as $value => $label)
                <a href="{{ url('/admin/orders') }}?filter={{ $value }}"
                    class="btn order-filter {{ $filter === $value ? 'active' : '' }}"
                    @if ($filter === $value) aria-current="page" @endif>
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        @forelse ($orders as $order)
            <article class="card">
                <div class="top">
                    <div>
                        <div class="eyebrow">
                            Order #{{ $order->id }} ·
                            {{ $order->created_at->format('d M Y, h:i A') }}
                        </div>
                        <h2>{{ $order->customer_name }}</h2>
                    </div>
                    <span class="badge status status-{{ $order->status }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="details">
                    <div>
                        <div class="eyebrow">Contact</div>
                        <p>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $order->phone) }}">
                                {{ $order->phone }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <div class="eyebrow">Delivery address</div>
                        <p>{{ $order->city }} · {{ $order->address }}</p>
                    </div>
                </div>

                @if ($order->notes)
                    <p class="notes"><strong>Note:</strong> {{ $order->notes }}</p>
                @endif

                <div class="items">
                    <div class="eyebrow" style="margin-bottom:10px">Items</div>
                    @foreach ($order->items as $item)
                        <div class="item">
                            <span>
                                {{ $item->product_name }} · Size {{ $item->size }}
                                × {{ $item->quantity }}
                            </span>
                            <strong>${{ number_format($item->line_total, 2) }}</strong>
                        </div>
                    @endforeach
                </div>

                <div class="total">
                    <span>Subtotal · Cash on delivery</span>
                    <strong>${{ number_format($order->subtotal, 2) }}</strong>
                </div>
                @if (in_array($order->status, ['pending', 'confirmed']))
                    <div class="actions">
                        @if ($order->status === 'pending')
                            <form method="POST" action="{{ url('/admin/orders/' . $order->id . '/status') }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="action-btn primary">Confirm order</button>
                            </form>
                        @endif

                        @if ($order->status === 'confirmed')
                            <form method="POST" action="{{ url('/admin/orders/' . $order->id . '/status') }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="btn action-btn primary">Mark as delivered</button>
                            </form>
                        @endif

                        <form method="POST" action="{{ url('/admin/orders/' . $order->id . '/status') }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn action-btn danger">Cancel order</button>
                        </form>
                    </div>
                @endif
            </article>
        @empty
            <div class="empty">No orders yet.</div>
        @endforelse

        @if ($orders->hasPages())
            <nav class="pages" aria-label="Order pages">
                <div>
                    @if ($orders->previousPageUrl())
                        <a href="{{ $orders->previousPageUrl() }}">← Previous</a>
                    @endif
                </div>
                <div>
                    @if ($orders->nextPageUrl())
                        <a href="{{ $orders->nextPageUrl() }}">Next →</a>
                    @endif
                </div>
            </nav>
        @endif
    </main>
</x-admin.layout>
