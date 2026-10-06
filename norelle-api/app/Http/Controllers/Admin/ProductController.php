<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
public function index(Request $request)
{
    $search = trim($request->input('search', ''));

    $products = Product::with(['category', 'variants'])
        ->when($request->boolean('low_stock'), function ($query) {
            $query->whereHas('variants', function ($variantQuery) {
                $variantQuery->where('stock', '<=', 2);
            });
        })
        ->when($search !== '', function ($query) use ($search) {
            $query->where(function ($productQuery) use ($search) {
                $productQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    });
            });
        })
        ->latest()
        ->paginate(12)
        ->withQueryString();

    return view('admin.products.index', compact('products'));
}
    public function edit(Product $product)
    {
        $product->load('variants');

        $categories = Category::orderBy('name')->get();

        return view(
            'admin.products.edit',
            compact('product', 'categories')
        );
    }
    public function update(Request $request, Product $product)
    {


        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($product),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'is_active' => ['required', 'boolean'],

            'main_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'variants' => ['required', 'array', 'min:1'],
            'variants.*.id' => ['required', 'integer'],
            'variants.*.size' => ['required', 'string', 'max:20', 'distinct'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
        ]);

       $oldImage = $product->main_image;
$newImagePath = null;

if ($request->hasFile('main_image')) {
    $newImagePath = $request
        ->file('main_image')
        ->store('products', 'public');
}

try {
    DB::transaction(function () use (
        $product,
        $validated,
        $newImagePath
    ) {
        $productData = $validated;

        unset(
            $productData['variants'],
            $productData['main_image']
        );

        $productData['slug'] = Str::slug($productData['name']);

        if ($newImagePath !== null) {
            $productData['main_image'] = 'storage/' . $newImagePath;
        }

        $product->update($productData);

        foreach ($validated['variants'] as $variantData) {
            $product->variants()
                ->whereKey($variantData['id'])
                ->update([
                    'size' => $variantData['size'],
                    'stock' => $variantData['stock'],
                ]);
        }
    });
} catch (\Throwable $exception) {
    if ($newImagePath !== null) {
        Storage::disk('public')->delete($newImagePath);
    }

    throw $exception;
}

if (
    $newImagePath !== null &&
    $oldImage &&
    str_starts_with($oldImage, 'storage/')
) {
    Storage::disk('public')->delete(
        substr($oldImage, strlen('storage/'))
    );
}

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'main_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
            'is_active' => ['required', 'boolean'],

            'variants' => ['required', 'array', 'min:1'],
            'variants.*.size' => [
                'required',
                'string',
                'max:20',
                'distinct',
            ],
            'variants.*.stock' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $imagePath = $request
            ->file('main_image')
            ->store('products', 'public');

        try {
            DB::transaction(function () use ($validated, $imagePath) {
                $productData = $validated;

                unset(
                    $productData['main_image'],
                    $productData['variants']
                );

                $productData['slug'] = Str::slug($productData['name']);
                $productData['main_image'] = 'storage/' . $imagePath;

                $product = Product::create($productData);

                foreach ($validated['variants'] as $variantData) {
                    $product->variants()->create([
                        'size' => $variantData['size'],
                        'stock' => $variantData['stock'],
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($imagePath);

            throw $exception;
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product added successfully.');
    }
}
