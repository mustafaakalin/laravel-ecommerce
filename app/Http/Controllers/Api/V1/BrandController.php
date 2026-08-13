<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::with('products','products.images','products.comments','products.category','products.campaigns')->paginate(10);
        return BrandResource::collection($brands);
    }

    public function show($slug)
    {
        $brand = Brand::with('products','products.images','products.comments','products.category','products.campaigns')->where('slug', $slug)->firstOrFail();
        return new BrandResource($brand);
    }
}
