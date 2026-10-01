<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{


    public function index()
    {
        $products = Product::query()
            ->with(['category', 'comments.user', 'brand', 'tags', 'campaigns', 'campaigns.products', 'media'])
            ->withCount(['likes', 'ratings'])
            ->withAvg('ratings', 'rating')
            ->paginate(10);
        return ProductResource::collection($products);
    }
    
    


    public function show($slug)
    {
        $product = Product::query()
            ->with(['category', 'comments.user', 'brand', 'tags', 'campaigns', 'media'])
            ->withCount(['likes', 'ratings'])
            ->withAvg('ratings', 'rating')
            ->where('slug', $slug)
            ->firstOrFail();
        return new ProductResource($product);
    }
}
