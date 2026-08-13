<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('is_active', true)

            ->withCount([
                'products' => function ($query) {
                    $query->where('is_active', true)->distinct();
                }
            ])
            ->get();

        return view('categories.index', compact('categories'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            // ->where('is_active', true)
            ->firstOrFail();

        $productsQuery = Product::with(['category', 'images'])
            ->where('category_id', $category->id)
            ->active()
            ->inStock();

        // Apply filters and sorting
        if (request()->has('filter')) {
            $filter = request()->input('filter');
            switch ($filter) {
                case 'inStock':
                    $productsQuery->inStock();
                    break;
                case 'onSale':
                    $productsQuery->whereNotNull('discount');
                    break;
                // Add more filters as needed
            }
        }

        if (request()->has('sort')) {
            $sort = request()->input('sort');
            switch ($sort) {
                case 'priceAsc':
                    $productsQuery->orderBy('price', 'asc');
                    break;
                case 'priceDesc':
                    $productsQuery->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $productsQuery->orderBy('created_at', 'desc');
                    break;
                // Add more sorting options as needed
            }
        }

        $products = $productsQuery->paginate(12);

        return view('categories.show', compact('category', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
