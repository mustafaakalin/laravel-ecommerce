<?php

namespace App\Livewire;

use App\Models\Cart as CartModel;
use App\Models\CartItem;
use App\Models\Like;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ProductCardActions extends Component
{
    public int $productId;
    public bool $isLiked = false;
    public string $mode = 'all';

    public function mount(int $productId, string $mode = 'all'): void
    {
        $this->productId = $productId;
        $this->mode = $mode;

        if ($mode !== 'cart' && auth()->check()) {
            $this->isLiked = Like::query()
                ->where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->exists();
        }
    }

    public function toggleWishlist(): void
    {
        if (! auth()->check()) {
            $this->dispatch('showToast', message: 'Giriş yapmanız gerek', type: 'warning');
            return;
        }

        $like = Like::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $this->productId)
            ->first();

        if ($like) {
            $like->delete();
            $this->isLiked = false;
            $this->dispatch('showToast', message: 'Ürün favorilerden kaldırıldı', type: 'info');
        } else {
            Like::create([
                'user_id' => auth()->id(),
                'product_id' => $this->productId,
            ]);
            $this->isLiked = true;
            $this->dispatch('showToast', message: 'Ürün favorilere eklendi', type: 'success');
        }

        $this->dispatch('wishlistUpdated');
    }

    public function addToCart(): void
    {
        if (! auth()->check()) {
            $this->dispatch('showToast', message: 'Giriş yapmanız gerek', type: 'warning');
            return;
        }

        try {
            $product = Product::query()->find($this->productId);

            if (! $product || ! $product->is_active) {
                $this->dispatch('showToast', message: 'Ürün satışa kapalı', type: 'error');
                return;
            }

            if (! $product->isInStock()) {
                $this->dispatch('showToast', message: 'Ürün stokta yok', type: 'error');
                return;
            }

            $availableStock = $product->getAvailableStockForUser(auth()->id());

            if ($availableStock <= 0) {
                $this->dispatch('showToast', message: "'{$product->name}' ürününün tüm stoğunu sepete eklediniz", type: 'warning');
                return;
            }

            $cart = CartModel::firstOrCreate(['user_id' => auth()->id()]);

            $cartItem = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity');
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                ]);
            }

            $this->dispatch('cartUpdated');
            $this->dispatch('showToast', message: 'Ürün sepete eklendi', type: 'success');
        } catch (\Throwable $e) {
            Log::error('Product card add-to-cart error', [
                'product_id' => $this->productId,
                'exception' => $e,
            ]);
            $this->dispatch('showToast', message: 'Bir hata oluştu', type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.product-card-actions');
    }
}
