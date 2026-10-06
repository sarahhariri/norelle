<x-admin.layout title="Add product" bodyClass="admin-products">

    <main class="container py-5">
        <div class="product-edit-wrapper">
            <p class="admin-products-eyebrow mb-2">
                CATALOG MANAGEMENT
            </p>

            <h1 class="product-edit-title mb-4">
                Add product
            </h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    Please check the highlighted fields.
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('admin.products.store') }}"
                enctype="multipart/form-data"
                class="product-edit-card"
            >
                @csrf

                <div class="mb-4">
                    <label for="name" class="form-label">
                        Product name
                    </label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="category_id" class="form-label">
                        Category
                    </label>

                    <select
                        id="category_id"
                        name="category_id"
                        class="form-select @error('category_id') is-invalid @enderror"
                        required
                    >
                        <option value="">Choose a category</option>

                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected(old('category_id') == $category->id)
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="form-control @error('description') is-invalid @enderror"
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="main_image" class="form-label">
                        Product image
                    </label>

                    <input
                        id="main_image"
                        name="main_image"
                        type="file"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="form-control @error('main_image') is-invalid @enderror"
                        required
                    >

                    <div class="form-text">
                        JPG, PNG or WEBP — maximum 4 MB.
                    </div>

                    @error('main_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="price" class="form-label">
                            Regular price
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">$</span>

                            <input
                                id="price"
                                name="price"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control"
                                value="{{ old('price') }}"
                                required
                            >
                        </div>

                        @error('price')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="sale_price" class="form-label">
                            Sale price
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">$</span>

                            <input
                                id="sale_price"
                                name="sale_price"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control"
                                value="{{ old('sale_price') }}"
                            >
                        </div>

                        @error('sale_price')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                @php
                    $variantRows = old('variants', [
                        ['size' => 'S', 'stock' => 0],
                        ['size' => 'M', 'stock' => 0],
                        ['size' => 'L', 'stock' => 0],
                    ]);
                @endphp

                <div class="mb-4">
                    <h2 class="variants-title mb-1">
                        Sizes & stock
                    </h2>

                    <p class="text-secondary small mb-3">
                        Enter the available quantity for every size.
                    </p>

                    <div class="variants-list">
                        @foreach ($variantRows as $index => $variant)
                            <div class="variant-row">
                                <div>
                                    <label
                                        for="size-{{ $index }}"
                                        class="form-label"
                                    >
                                        Size
                                    </label>

                                    <input
                                        id="size-{{ $index }}"
                                        name="variants[{{ $index }}][size]"
                                        type="text"
                                        class="form-control"
                                        value="{{ $variant['size'] ?? '' }}"
                                        required
                                    >

                                    @error('variants.' . $index . '.size')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        for="stock-{{ $index }}"
                                        class="form-label"
                                    >
                                        Stock
                                    </label>

                                    <input
                                        id="stock-{{ $index }}"
                                        name="variants[{{ $index }}][stock]"
                                        type="number"
                                        min="0"
                                        class="form-control"
                                        value="{{ $variant['stock'] ?? 0 }}"
                                        required
                                    >

                                    @error('variants.' . $index . '.stock')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input type="hidden" name="is_active" value="0">

                    <input
                        id="is_active"
                        name="is_active"
                        type="checkbox"
                        value="1"
                        class="form-check-input"
                        @checked(old('is_active', 1))
                    >

                    <label for="is_active" class="form-check-label">
                        Product is visible in the store
                    </label>
                </div>

                <div class="d-flex flex-wrap justify-content-end gap-2 pt-3 border-top">
                    <a
                        href="{{ route('admin.products.index') }}"
                        class="btn product-cancel-button"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn product-save-button"
                    >
                        Add product
                    </button>
                </div>
            </form>
        </div>
    </main>
</x-admin.layout>