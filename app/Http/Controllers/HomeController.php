<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\Campaign;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->get();
        
        $featuredProducts = Product::with(['category', 'images'])
            ->active()
            ->featured()
            ->inStock()
            ->latest()
            ->take(4)
            ->get();
            
        $newProducts = Product::with(['category', 'images'])
            ->active()
            ->new()
            ->inStock()
            ->latest()
            ->take(4)
            ->get();

        $campaigns = Campaign::with('products')
            ->orderBy('start_date', 'desc')
            ->where('is_active',1)
            ->latest()
            ->get();


        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->activeProductsCount() // Model'deki scope'u kullan
            ->latest()
            ->get();


        $brands = Brand::with('products')
        ->where('is_active', true)
        ->latest()
        ->get();



        $testimonials = Testimonial::where('is_active', true)->get();

        return view('home', compact(
            'products',
            'featuredProducts', 
            'newProducts', 
            'categories',
            'campaigns',
            'brands',
            'testimonials'
        ));
    }
}





