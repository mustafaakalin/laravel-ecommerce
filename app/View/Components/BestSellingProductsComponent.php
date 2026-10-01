<?php

namespace App\View\Components;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use App\Support\CacheKeys;

class BestSellingProductsComponent extends Component
{
    public $products;

    public function __construct()
    {
        // One aggregate query + eager-loaded relations, instead of one Product::find()
        // query for every ranked product.
        $this->products = Cache::flexible(CacheKeys::rankings('best-selling-products'), [30, 120], static fn () => Product::query()
            ->with(['images', 'brand', 'category'])
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'delivered')
            ->select('products.*', DB::raw('SUM(order_items.quantity) as sales'))
            ->groupBy('products.id')
            ->orderByDesc('sales')
            ->limit(10)
            ->get());
    }

    public function render(): View|Closure|string
    {
        return view('components.best-selling-products-component');
    }
}
