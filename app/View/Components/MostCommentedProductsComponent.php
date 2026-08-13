<?php

namespace App\View\Components;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class MostCommentedProductsComponent extends Component
{
    public $products;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->products = Product::withCount('comments')
            ->orderBy('comments_count', 'desc')
            ->take(10)
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.most-commented-products-component');
    }
}
