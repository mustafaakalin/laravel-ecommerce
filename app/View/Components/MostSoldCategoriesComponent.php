<?php

namespace App\View\Components;

use Closure;
use App\Models\Category;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use App\Support\CacheKeys;

class MostSoldCategoriesComponent extends Component
{
    public $categories;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->categories = Cache::flexible(CacheKeys::rankings('most-sold-categories'), [30, 120], static fn () => Category::select([
                'categories.id',
                'categories.name',
                'categories.icon',
                'categories.products_count',
                DB::raw('COUNT(order_items.id) as total_sales')
            ])
            ->activeProductsCount()
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->where('categories.is_active', true)
            ->groupBy([
                'categories.id',
                'categories.name',
                'categories.icon',
                'categories.products_count'
            ])
            ->orderByRaw('COUNT(order_items.id) DESC')
            ->limit(10)
            ->get());
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.most-sold-categories-component');
    }
}
