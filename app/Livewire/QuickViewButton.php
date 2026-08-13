<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class QuickViewButton extends Component
{


    public $productId;
    public $buttonClasses;
    public $show = false;
    public $product;

    public function mount($productId, $buttonClasses)
    {
        $this->productId = $productId;
        $this->buttonClasses = $buttonClasses;
    }

    public function loadProduct()
    {
        $this->product = Product::with(['images', 'specifications'])->find($this->productId);
        $this->show = true;
    }

    public function closeModal()
    {
        $this->show = false;
    }
    
    
    public function showQuickView()
    {
        $this->dispatch('show-quick-view', productId: $this->productId);
    }

    public function render()
    {
        return view('livewire.quick-view-button');
    }
}
