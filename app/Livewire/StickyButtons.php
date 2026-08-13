<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class StickyButtons extends Component
{
    public function render()
    {
        return view('livewire.sticky-buttons', [
            'wishlistCount' => Auth::check() ? Auth::user()->wishlist()->count() : 0,
            'cartCount' => Auth::check() ? Auth::user()->cart()->sum('quantity') : 0
        ]);
    }
}