<x-admin.layout
    title="'Edit ' . $product->name"
    bodyClass="admin-products">

    <main class="container py-5">
        <div class="product-edit-wrapper">
            <p class="admin-products-eyebrow mb-2">
                CATALOG MANAGEMENT
            </p>

            <h1 class="product-edit-title mb-4">
                Edit product
            </h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    Please check the highlighted fields.
                </div>
            @endif

            <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data"
                class="product-edit-card">
                
                @method('PATCH')

                <div class="mb-4">
                    <label for="name" class="form-label">
                        Product name
                    </label>

                    <input id="name" name="name" type="text"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $product->name) }}" required>

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="category_id" class="form-label">
                        Category
                    </label>

                    <select id="category_id" name="category_id"
                        class="form-select @error('category_id') is-invalid @enderror" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
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

                    <textarea id="description" name="description" rows="4"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="main_image" class="form-label">
                        Product image
                    </label>

                    @if ($product->main_image)
                        <div class="current-product-image mb-3">
                            <img src="{{ asset($product->main_image) }}" alt="{{ $product->name }}" class="admin-edit-preview">

                            <div>
                                <strong>Current image</strong>
                                <p class="text-secondary small mb-0">
                                    Choose a new image only if you want to replace it.
                                </p>
                            </div>
                        </div>
                    @endif

                    <input id="main_image" name="main_image" type="file" accept=".jpg,.jpeg,.png,.webp"
                        class="form-control @error('main_image') is-invalid @enderror">

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
                            <input id="price" name="price" type="number" step="0.01" min="0"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price', $product->price) }}" required>
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
                            <input id="sale_price" name="sale_price" type="number" step="0.01" min="0"
                                class="form-control @error('sale_price') is-invalid @enderror"
                                value="{{ old('sale_price', $product->sale_price) }}">
                        </div>

                        @error('sale_price')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h2 class="variants-title mb-1">Sizes & stock</h2>
                            <p class="text-secondary small mb-0">
                                Update the available quantity for every size.
                            </p>
                        </div>
                    </div>

                    <div class="variants-list">
                        @foreach ($product->variants as $variant)
                            <div class="variant-row">
                                <input type="hidden" name="variants[{{ $loop->index }}][id]"
                                    value="{{ $variant->id }}">

                                <div>
                                    <label for="size-{{ $variant->id }}" class="form-label">
                                        Size
                                    </label>

                                    <input id="size-{{ $variant->id }}" name="variants[{{ $loop->index }}][size]"
                                        type="text" class="form-control"
                                        value="{{ old('variants.' . $loop->index . '.size', $variant->size) }}"
                                        required>

                                    @error('variants.' . $loop->index . '.size')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="stock-{{ $variant->id }}" class="form-label">
                                        Stock
                                    </label>

                                    <input id="stock-{{ $variant->id }}"
                                        name="variants[{{ $loop->index }}][stock]" type="number" min="0"
                                        class="form-control"
                                        value="{{ old('variants.' . $loop->index . '.stock', $variant->stock) }}"
                                        required>

                                    @error('variants.' . $loop->index . '.stock')
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

                    <input id="is_active" name="is_active" type="checkbox" value="1" class="form-check-input"
                        @checked(old('is_active', $product->is_active))>

                    <label for="is_active" class="form-check-label">
                        Product is visible in the store
                    </label>
                </div>

                <div class="d-flex flex-wrap justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('admin.products.index') }}" class="btn product-cancel-button">
                        Cancel
                    </a>

                    <button type="submit" class="btn product-save-button">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </main>
</x-admin.layout>
