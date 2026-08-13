<?php

namespace App\View\Components;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class MostFavoritedProductsComponent extends Component
{
    public $mostFavoritedProducts;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // En çok favorilenen 10 ürünü getir
        $this->mostFavoritedProducts = Product::withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->take(10)
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.most-favorited-products-component');
    }
}
