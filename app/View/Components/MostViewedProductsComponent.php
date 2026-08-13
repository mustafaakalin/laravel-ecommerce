<?php

namespace App\View\Components;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class MostViewedProductsComponent extends Component
{
    public Collection $products;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Get the 8 most viewed active products
        $this->products = Product::where('is_active', true)
            ->orderBy('view_count', 'desc')
            ->take(8)
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.most-viewed-products-component');
    }
}
