<x-admin.layout
    title="Products">

    <main class="container py-5">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
            <div>
                <p class="admin-products-eyebrow mb-2">
                    CATALOG MANAGEMENT
                </p>

                <h1 class="admin-products-title mb-0">
                    Products
                    <span>{{ $products->total() }} total</span>
                </h1>
            </div>


            <a href="{{ route('admin.products.create') }}" class="btn btn-admin-primary">
                + Add product
            </a>
        </div>

        @if (request()->boolean('low_stock'))
            <div class="alert alert-admin-stock d-flex flex-column flex-sm-row
               align-items-sm-center justify-content-between gap-3 mb-4"
                role="alert">
                <div>
                    <strong class="d-block">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        Low stock products</strong>

                    <span class="small">
                        Showing products with sizes that have 2 items or fewer.
                    </span>
                </div>

                <a href="{{ route('admin.products.index') }}"
                    class="btn btn-sm btn-outline-admin flex-shrink-0 px-3 fw-medium">
                    Show all products
                </a>
            </div>
        @endif

        <form id="product-search-form" action="{{ route('admin.products.index') }}" method="GET"
            class="row g-2 align-items-center mb-4">
            @if (request()->boolean('low_stock'))
                <input type="hidden" name="low_stock" value="1">
            @endif

            <div class="col-12 col-md-7 col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search" aria-hidden="true"></i>
                    </span>

                    <input id="product-search-input" type="search" name="search" value="{{ request('search') }}"
                        class="form-control" placeholder="Search by product or category..." aria-label="Search products"
                        autocomplete="off">
                    <button type="submit" class="btn btn-admin-primary px-4">
                        Search </button>
                </div>
            </div>

        </form>


        @if (session('success'))
            <div id="successAlert" class="alert alert-success alert-dismissible fade show product-success-alert"
                role="alert">
                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="bg-white border">
            <div class="table-responsive">
                <table class="table admin-products-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Sizes & stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if ($product->main_image)
                                            <img src="{{ asset($product->main_image) }}" alt=""
                                                class="admin-product-image">
                                        @endif

                                        <strong>{{ $product->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $product->category?->name ?? '—' }}</td>
                                <td>
                                    @if ($product->sale_price !== null)
                                        <strong>${{ number_format($product->sale_price, 2) }}</strong>
                                        <del class="text-secondary small ms-1">
                                            ${{ number_format($product->price, 2) }}
                                        </del>
                                    @else
                                        <strong>${{ number_format($product->price, 2) }}</strong>
                                    @endif
                                </td>
                                <td>
                                    @forelse ($product->variants as $variant)
                                        <span
                                            class="admin-stock
            {{ $variant->stock === 0 ? 'out-of-stock' : ($variant->stock <= 2 ? 'low-stock' : '') }}">
                                            {{ $variant->size }}: {{ $variant->stock }}
                                        </span>
                                    @empty
                                        <span class="text-secondary">No sizes</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span class="product-status {{ $product->is_active ? 'active' : 'hidden' }}">
                                        {{ $product->is_active ? 'Active' : 'Hidden' }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                        class="btn btn-sm admin-edit-button">
                                        Edit
                                    </a>
                                </td>

                            </tr>
                        @empty
                            @if ($products->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-secondary">
                                        @if (request()->filled('search'))
                                            No products found for
                                            <strong>“{{ request('search') }}”</strong>
                                            @if (request()->boolean('low_stock'))
                                                within low stock products.
                                            @endif
                                        @elseif (request()->boolean('low_stock'))
                                            No low stock products found.
                                        @else
                                            No products yet.
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            @if ($products->previousPageUrl())
                <a href="{{ $products->previousPageUrl() }}" class="btn admin-page-link">
                    ← Previous
                </a>
            @else
                <span></span>
            @endif

            @if ($products->nextPageUrl())
                <a href="{{ $products->nextPageUrl() }}" class="btn admin-page-link">
                    Next →
                </a>
            @endif
        </div>
    </main>

  

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('product-search-form');
            const input = document.getElementById('product-search-input');

            let searchTimer;

            input.addEventListener('input', function() {
                clearTimeout(searchTimer);

                searchTimer = setTimeout(function() {
                    form.requestSubmit();
                }, 500);
            });

            @if (request()->filled('search'))
                input.focus();
                input.setSelectionRange(
                    input.value.length,
                    input.value.length
                );
            @endif
        });
    </script>
</x-admin.layout>
