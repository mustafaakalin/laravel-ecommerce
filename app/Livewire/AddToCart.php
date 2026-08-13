<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\CartItem;
use App\Models\Cart as CartModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddToCart extends Component
{

    public $product;
    public $quantity = 1;

    public function addToCart()
    {
        if (!auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }
    
        try {
            // Basic validations
            if (!$this->product) {
                $this->dispatch('showToast', message: 'Ürün bulunamadı', type: 'error');
                return;
            }
    
            if (!$this->product->is_active) {
                $this->dispatch('showToast', message: 'Ürün satışa kapalı', type: 'error');
                return;
            }
    
            if (!$this->product->isInStock()) {
                $this->dispatch('showToast', message: 'Ürün stokta yok', type: 'error');
                return;
            }
    
            // Validate quantity
            if ($this->quantity <= 0) {
                $this->dispatch('showToast', message: 'Geçersiz miktar', type: 'error');
                return;
            }
    
            // Check available stock for user
            $availableStock = $this->product->getAvailableStockForUser(auth()->id());
            
            if ($availableStock <= 0) {
                $this->dispatch('showToast', 
                    message: "'{$this->product->name}' ürününün tüm stoğunu sepete eklediniz", 
                    type: 'warning'
                );
                return;
            }
    
            if (!$this->product->hasSufficientStock($this->quantity, auth()->id())) {
                $this->dispatch('showToast', 
                    message: "'{$this->product->name}' ürününden sepete en fazla {$availableStock} adet daha ekleyebilirsiniz", 
                    type: 'warning'
                );
                return;
            }
    
            // Validate campaign if exists
            $campaign = $this->product->activeCampaign();
            if ($campaign) {
                if (!$campaign->is_active || $campaign->end_date < now()) {
                    $this->dispatch('showToast', message: 'Kampanya süresi dolmuş', type: 'warning');
                    return;
                }
            }
    
            // Add to cart
            $cart = CartModel::firstOrCreate([
                'user_id' => auth()->id()
            ]);
    
            $cartItem = CartItem::updateOrCreate(
                [
                    'cart_id' => $cart->id,
                    'product_id' => $this->product->id
                ],
                [
                    'quantity' => DB::raw('quantity + ' . $this->quantity)
                ]
            );
    
            // Success notifications
            $this->dispatch('cartUpdated');
            $this->dispatch('showToast', 
                message: 'Ürün sepete eklendi' . ($campaign ? ' (Kampanyalı Fiyat)' : ''), 
                type: 'success'
            );
    
        } catch (\Exception $e) {
            Log::error('Add to cart error: ' . $e->getMessage());
            $this->dispatch('showToast', message: 'Bir hata oluştu', type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
