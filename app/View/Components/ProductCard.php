<?php

namespace App\View\Components;

use App\Models\Product;
use Illuminate\View\Component;

class ProductCard extends Component
{
    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function render()
    {
        return view('components.product-card');
    }
}
