<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        'description' => ['nullable', 'string'],
        'is_active' => ['nullable', 'boolean'],
        'order' => ['nullable', 'integer', 'min:0'],
    ]);

    $validated['slug'] = Str::slug($validated['name']);

    $category = Category::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Category created successfully.',
        'data' => $category,
    ], 201);
}
}