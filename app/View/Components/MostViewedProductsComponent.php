<?php

namespace App\View\Components;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use App\Support\CacheKeys;
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
        $this->products = Cache::flexible(CacheKeys::rankings('most-viewed-products'), [30, 120], static fn () => Product::query()->with(['images', 'brand', 'category'])->where('is_active', true)->orderByDesc('view_count')->take(8)->get());
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.most-viewed-products-component');
    }
}
