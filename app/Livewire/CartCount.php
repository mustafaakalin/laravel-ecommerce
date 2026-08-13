<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CartCount extends Component
{
    public function render()
    {
        $count = Auth::check() ? Auth::user()->cart?->items()->sum('quantity') ?? 0 : 0;
        return view('livewire.cart-count', ['count' => $count]);
    }
}