<x-admin.layout
    title="Categories"
    bodyClass="admin-products admin-categories"
>   

    <main class="container py-5">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
            <div>
                <p class="admin-products-eyebrow mb-2">
                    CATALOG MANAGEMENT
                </p>

                <h1 class="admin-products-title mb-0">
                    Categories
                    <span>{{ $categories->count() }} total</span>
                </h1>
            </div>

            <a href="{{ route('admin.categories.create') }}" class="btn btn-admin-primary">
                <i class="bi bi-plus-lg me-2"></i>
                Add category
            </a>
        </div>


        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div id="successAlert" class="alert alert-success alert-dismissible fade show product-success-alert"
                role="alert">
                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="bg-white border category-table-panel">
            <div class="table-responsive">
                <table class="table admin-products-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if ($category->image)
                                            <img src="{{ asset($category->image) }}" alt=""
                                                class="admin-product-image">
                                        @else
                                            <i class="bi bi-image text-secondary fs-4" aria-hidden="true"></i>
                                        @endif

                                        <strong>{{ $category->name }}</strong>
                                    </div>
                                </td>

                                <td>{{ $category->slug }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td>{{ $category->order }}</td>

                                <td>
                                    <span class="product-status {{ $category->is_active ? 'active' : 'hidden' }}">
                                        {{ $category->is_active ? 'Active' : 'Hidden' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                            class="btn btn-sm admin-edit-button">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.categories.destroy', $category) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this category permanently?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-secondary">
                                    No categories yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

   

</x-admin.layout>
