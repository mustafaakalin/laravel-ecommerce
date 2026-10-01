<?php

namespace App\Livewire;

use App\Models\Like;
use Livewire\Component;
use Livewire\Attributes\On;

class WishlistCounter extends Component
{
    public int $count = 0;

    #[On('wishlistUpdated')]
    public function updateCount(): void
    {
        $this->count = auth()->check()
            ? Like::where('user_id', auth()->id())->count()
            : 0;
    }

    public function mount(): void
    {
        $this->updateCount();
    }

    public function openDrawer(): void
    {
        $this->dispatch('toggleWishlistDrawer');
    }

    public function render()
    {
        return view('livewire.wishlist-counter');
    }
}
