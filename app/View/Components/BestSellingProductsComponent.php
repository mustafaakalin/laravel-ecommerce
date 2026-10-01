<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;

class BestSellingProductsComponent extends Component
{
    public $products;

    public function __construct()
    {
        // Join the product table once instead of Product::find() per ranking row.
        $this->products = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.status', 'delivered')
            ->select('products.*', DB::raw('SUM(order_items.quantity) as sales'))
            ->groupBy('products.id')
            ->orderByDesc('sales')
            ->limit(10)
            ->get();
    }

    public function render(): View|Closure|string
    {
        return view('components.best-selling-products-component');
    }
}
