@php
    $editing = isset($category);
@endphp

<x-admin.layout
    title="{{ $editing ? 'Edit category' : 'Add category' }}"
    >

    <main class="container py-5">
        <a
            href="{{ route('admin.categories.index') }}"
            class="btn btn-outline-admin mb-4"
        >
            <i class="bi bi-arrow-left me-2"></i>
            Back to categories
        </a>

        <p class="admin-products-eyebrow mb-2">
            CATALOG MANAGEMENT
        </p>

        <h1 class="admin-products-title mb-4">
            {{ $editing ? 'Edit category' : 'Add category' }}
        </h1>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ $editing
                ? route('admin.categories.update', $category)
                : route('admin.categories.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="bg-white border p-4"
        >
            @csrf

            @if ($editing)
                @method('PATCH')
            @endif

            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <label for="name" class="form-label">
                        Category name
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name', $category->name ?? '') }}"
                        maxlength="255"
                        required
                    >
                </div>

                <div class="col-12 col-md-6">
                    <label for="slug" class="form-label">
                        Slug
                    </label>

                    <input
                        id="slug"
                        type="text"
                        name="slug"
                        class="form-control"
                        value="{{ old('slug', $category->slug ?? '') }}"
                        placeholder="e.g. outerwear"
                        pattern="[a-z0-9]+(-[a-z0-9]+)*"
                        maxlength="255"
                        required
                    >

                    <div class="form-text">
                        Use lowercase English letters, numbers and hyphens.
                    </div>
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        maxlength="2000"
                        class="form-control"
                    >{{ old('description', $category->description ?? '') }}</textarea>
                </div>

                <div class="col-12 col-md-8">
                    <label for="image" class="form-label">
                        Category image
                    </label>

                    <input
                        id="image"
                        type="file"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="form-control"
                        aria-describedby="image-help"
                    >

                    <div id="image-help" class="form-text">
                        JPG, PNG or WebP. Maximum 4 MB.
                        @if ($editing)
                            Leave empty to keep the current image.
                        @endif
                    </div>

                    @if ($editing && $category->image)
                        <div class="mt-3">
                            <p class="small text-secondary mb-2">
                                Current image
                            </p>

                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <img
                                        src="{{ asset($category->image) }}"
                                        alt="{{ $category->name }}"
                                        class="img-fluid rounded"
                                    >
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-12 col-md-4">
                    <label for="order" class="form-label">
                        Display order
                    </label>

                    <input
                        id="order"
                        type="number"
                        name="order"
                        min="0"
                        step="1"
                        class="form-control"
                        value="{{ old('order', $category->order ?? 0) }}"
                        required
                    >

                    <div class="form-text">
                        Lower numbers appear first.
                    </div>
                </div>

                <div class="col-12">
                    <input type="hidden" name="is_active" value="0">

                    <div class="form-check form-switch">
                        <input
                            id="is_active"
                            type="checkbox"
                            name="is_active"
                            value="1"
                            class="form-check-input"
                            @checked(old('is_active', $category->is_active ?? true))
                        >

                        <label for="is_active" class="form-check-label">
                            Active category
                        </label>
                    </div>
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                    <button type="submit" class="btn btn-admin-primary px-4">
                        {{ $editing ? 'Save changes' : 'Add category' }}
                    </button>

                    <a
                        href="{{ route('admin.categories.index') }}"
                        class="btn btn-outline-admin px-4"
                    >
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </main>
</x-admin.layout>
