<?php

namespace App\Livewire;

use App\Models\Like;
use App\Models\Product;
use Livewire\Component;

class ToggleWishlist extends Component
{
    public $product;
    public $isLiked;
    public $productId;

    public function mount($product)
    {
        // Eğer product bir ID ise, Product modelini yükle
        if (is_numeric($product)) {
            $this->productId = $product;
            $this->product = Product::find($product);
        } else {
            // Eğer zaten bir Product objesi ise, direkt kullan
            $this->product = $product;
            $this->productId = $product->id;
        }

        $this->isLiked = auth()->check() ? $this->product->isLikedBy(auth()->user()) : false;
    }

    public function toggleWishlist()
    {
        if (!auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $like = Like::where('user_id', auth()->id())
            ->where('product_id', $this->productId)
            ->first();

        if ($like) {
            $like->delete();
            $this->isLiked = false;
            $this->dispatch('showToast', 'Ürün favorilerden kaldırıldı', 'info');
            $this->dispatch('showModal', 'Ürün favorilere kaldırıldı', 'info');
        } else {
            Like::create([
                'user_id' => auth()->id(),
                'product_id' => $this->productId
            ]);
            $this->isLiked = true;
            $this->dispatch('showToast', 'Ürün favorilere eklendi', 'success');
            $this->dispatch('showModal', 'Ürün favorilere eklendi', 'success');
        }

        // Tüm ilgili komponentleri güncelle
        $this->dispatch('wishlistUpdated');
    }

    public function render()
    {
        return view('livewire.toggle-wishlist');
    }
}