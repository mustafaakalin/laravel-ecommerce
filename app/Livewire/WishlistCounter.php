<?php

namespace App\Livewire;

use App\Models\Like;
use Livewire\Component;
use Livewire\Attributes\On;

class WishlistCounter extends Component
{
    public $count = 0;
    public $showDrawer = false;

    

    public function boot()
    {
        $this->updateCount();
    }
    
    #[On('wishlistUpdated')] 
    public function updateCount()
    {
        $this->count = auth()->check() ? Like::where('user_id', auth()->id())->count() : 0;
    }

    public function showDrawer()
    {
        $this->showDrawer = true;
        $this->dispatch('drawerOpened');
    }

    public function hideDrawer()
    {
        $this->showDrawer = false;
    }

    public function openDrawer()
    {
        $this->dispatch('toggleWishlistDrawer');
    }

    public function render()
    {

        $wishlistItems = auth()->check() 
        ? auth()->user()->likes()
            ->with(['product.images'])
            ->latest()
            ->get()
            ->pluck('product')
        : collect([]);

        
        return view('livewire.wishlist-counter',[
            'wishlistItems' => $wishlistItems
        ]);
    }
}