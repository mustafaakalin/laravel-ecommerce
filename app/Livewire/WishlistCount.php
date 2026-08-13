<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class WishlistCount extends Component
{
    public function render()
    {
        $count = Auth::check() ? Auth::user()->wishlist()->count() : 0;
        return view('livewire.wishlist-count', ['count' => $count]);
    }
}