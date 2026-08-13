<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\Cart as CartModel;
use App\Models\CartItem;
use Livewire\Attributes\On;

class Cart extends Component
{
    public $cart;
    public $drawer = false;


    public function mount()
    {
        $this->refreshCart();
    }

    #[On('cartUpdated')]
    public function refreshCart()
    {
        if (auth()->check()) {
            $this->cart = CartModel::with(['items.product'])->where('user_id', auth()->id())->first();
        }
    }

    #[On(['toggleDrawer','togglecartdrawer'])]
    public function toggleDrawer()
    {
        $this->drawer = !$this->drawer;
        $this->dispatch('updateCart');
    }

    public function updateQuantity($itemId, $change)
    {
        $item = CartItem::find($itemId);
        
        if (!$item) {
            $this->dispatch('showToast', message: 'Ürün bulunamadı', type: 'error');
            $this->dispatch('showModal', message: 'Ürün bulunamadı', type: 'error');
            return;
        }
    
        $product = $item->product;
        $newQuantity = $item->quantity + $change;
    
        // Stok kontrolü
        if ($change > 0 && $newQuantity > $product->stock) {
            $this->dispatch('showToast', 
                message: 'Üzgünüz, stokta yeterli ürün bulunmamaktadır', 
                type: 'error'
            );
            $this->dispatch('showModal', message: 'Üzgünüz, stokta yeterli ürün bulunmamaktadır', type: 'error');
            return;
        }
    
        if ($newQuantity > 0) {
            $item->quantity = $newQuantity;
            $item->save();
    
            $this->dispatch('cartUpdated');
            $this->dispatch('showToast', 
                message: 'Sepet güncellendi', 
                type: 'success'
            );

            $this->dispatch('updateCart');
            $this->dispatch('showModal', message: 'Sepet güncellendi', type: 'success');
        } else {
            $this->removeItem($itemId);
        }
    }

    public function removeItem($itemId)
    {
        CartItem::destroy($itemId);


        $this->dispatch('updateCart');
        $this->dispatch('cartUpdated');
        $this->dispatch('showToast', message: 'Ürün sepetten kaldırıldı', type: 'info');
        $this->dispatch('showModal', message: 'Ürün sepetten kaldırıldı', type: 'info');
    }



    public function render()
    {
        return view('livewire.cart');
    }
}
