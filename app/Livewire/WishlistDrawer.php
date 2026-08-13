<?php

namespace App\Livewire;

use App\Models\Like;
use Livewire\Component;
use Livewire\Attributes\On;

class WishlistDrawer extends Component
{
    public $isOpen = false;
    public $likedProducts;

    #[On('toggleWishlistDrawer')]
    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
    }

    #[On('wishlistUpdated')]
    public function refresh()
    {
        $this->likedProducts = auth()->check()
        ? auth()->user()->likes()
            ->with(['product' => function($query) {
                $query->with(['images', 'category']);
            }])
            ->latest()
            ->get()
            ->pluck('product')
        : collect([]);
        
        
    }

    #[On('closeWishlistDrawer')]
    public function close()
    {
        $this->isOpen = false;
    }

    public function mount()
    {
        $this->refresh();
    }

    public function render()
    {
        $this->likedProducts = auth()->check()
        ? auth()->user()->likes()
            ->with(['product' => function($query) {
                $query->with(['images', 'category']);
            }])
            ->latest()
            ->get()
            ->pluck('product')
        : collect([]);
        return view('livewire.wishlist-drawer', [
            'likedProducts' => $this->likedProducts
        ]);
    }
}