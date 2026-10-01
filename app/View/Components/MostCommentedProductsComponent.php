<?php

namespace App\View\Components;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use App\Support\CacheKeys;

class MostCommentedProductsComponent extends Component
{
    public $products;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->products = Cache::flexible(CacheKeys::rankings('most-commented-products'), [30, 120], static fn () => Product::query()->with(['images', 'brand', 'category'])->withCount('comments')->orderByDesc('comments_count')->take(10)->get());
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.most-commented-products-component');
    }
}
