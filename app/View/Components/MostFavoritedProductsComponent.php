<?php

namespace App\View\Components;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use App\Support\CacheKeys;

class MostFavoritedProductsComponent extends Component
{
    public $mostFavoritedProducts;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // En çok favorilenen 10 ürünü getir
        $this->mostFavoritedProducts = Cache::flexible(CacheKeys::rankings('most-favorited-products'), [30, 120], static fn () => Product::query()->with(['images', 'brand', 'category'])->withCount('likes')->orderByDesc('likes_count')->take(10)->get());
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.most-favorited-products-component');
    }
}
