<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::with('products', 'parent', 'children','products.images','products.brand','products.campaigns')->paginate(10);
        return CategoryResource::collection($category);
    }

    public function show($slug)
    {
        $category = Category::with('products', 'parent', 'children','products.images','products.brand','products.campaigns')->where('slug', $slug)->firstOrFail();
        return new CategoryResource($category);
    }
}
