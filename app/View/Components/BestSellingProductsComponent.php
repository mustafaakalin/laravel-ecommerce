<?php

namespace App\View\Components;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;

class BestSellingProductsComponent extends Component
{
    public $products;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Hesaplama: sadece status 'delivered' olan siparişlerin ürünlerini toplayıp
        // satış toplamına göre sıralayarak en çok satanları elde ediyoruz.
        $this->products = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'delivered')
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sales'))
            ->groupBy('order_items.product_id')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                // Ürün detaylarını getiriyoruz
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->sales = $item->total_sales;
                }
                return $product;
            })
            ->filter() // Eklenmemiş ürünleri filtrele
            ->values();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.best-selling-products-component');
    }
}
