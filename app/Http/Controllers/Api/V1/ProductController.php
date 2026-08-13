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
        $products = Product::with(['category', 'images', 'comments.user','brand','likes','campaigns','campaigns.products'])->paginate(10);
        return ProductResource::collection($products);
    }
    
    


    public function show($slug)
    {
        $product = Product::with('category', 'images', 'comments.user','brand','likes','campaigns')->where('slug', $slug)->firstOrFail();
        return new ProductResource($product);
    }
}
