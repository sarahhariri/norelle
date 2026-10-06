<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.form');
    }

    public function store(Request $request)
    {
        $data = $this->validateCategory($request);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $path = $request->file('image')
                ->store('categories', 'public');

            $data['image'] = 'storage/' . $path;
        }

        Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category added successfully.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validateCategory($request, $category);

        $data['is_active'] = $request->boolean('is_active');

        // Keep the current image when no new file is uploaded.
        unset($data['image']);

        $oldImage = $category->image;

        if ($request->hasFile('image')) {
            $path = $request->file('image')
                ->store('categories', 'public');

            $data['image'] = 'storage/' . $path;
        }

        $category->update($data);

        // Delete only replaced images managed by this controller.
        if (
            isset($data['image']) &&
            $oldImage &&
            str_starts_with($oldImage, 'storage/categories/')
        ) {
            Storage::disk('public')->delete(
                substr($oldImage, strlen('storage/'))
            );
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    private function validateCategory(
        Request $request,
        ?Category $category = null
    ): array {
        $uniqueSlug = Rule::unique('categories', 'slug');

        if ($category) {
            $uniqueSlug->ignore($category->id);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $uniqueSlug,
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
            'is_active' => ['sometimes', 'boolean'],
            'order' => ['required', 'integer', 'min:0'],
        ]);
    }

    public function destroy(Category $category)
{
    if ($category->products()->exists()) {
        return redirect()
            ->route('admin.categories.index')
            ->with('error', 'Move this category’s products to another category before deleting it.');
    }

    $image = $category->image;

    $category->delete();

    if ($image && str_starts_with($image, 'storage/categories/')) {
        Storage::disk('public')->delete(
            substr($image, strlen('storage/'))
        );
    }

    return redirect()
        ->route('admin.categories.index')
        ->with('success', 'Category deleted successfully.');
}

}