<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart as CartModel;
use Livewire\Attributes\On;

class CartCounter extends Component
{
    public $cartCount = 0;
    public $cartTotal = 0;


    public function mount()
    {
        $this->updateCart();
    }


    #[On('cartUpdated')]
    public function updateCart()
    {
        if (auth()->check()) {
            $cart = CartModel::where('user_id', auth()->id())->first();
            if ($cart) {
                $this->cartCount = $cart->getTotalItems();
                $this->cartTotal = $cart->calculateTotalPrice();
            } else {
                $this->cartCount = 0;
                $this->cartTotal = 0;
            }
        }
    }

    public function toggleCart()
    {
        // user is not logged in
        if (!auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }
        $this->dispatch('toggleDrawer');
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }

}
