<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\Product;
use Livewire\Attributes\On;

class QuickView extends Component
{
    public $show = false;
    public $product = null;
    
    protected $listeners = ['show-quick-view' => 'showProduct'];

    
    
    public function showProduct($productId)
    {
        $this->product = Product::with(['category', 'images'])->findOrFail($productId);
        $this->show = true;
    }
    
    public function closeModal()
    {
        $this->show = false;
        $this->product = null;
    }


    public function render()
    {
        return view('livewire.quick-view');
    }
}