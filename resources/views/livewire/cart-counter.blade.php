<div class="fixed bottom-8 right-8 z-50">
    <div class="bg-base-100/60 hover:bg-base-100/80 backdrop-blur-sm rounded-xl p-2 shadow-lg transition-all duration-300 hover:shadow-xl">
        <div class="tooltip tooltip-left" data-tip="{{ $cartCount > 0 ? number_format($cartTotal ?? 0, 2) . ' ₺' : 'Sepetiniz Boş' }}">
            <button class="btn btn-ghost btn-circle relative group" wire:click="toggleCart" aria-label="Sepet">
                <i class="fas fa-shopping-cart text-lg transition-all duration-300 group-hover:scale-110 group-hover:text-primary"></i>


                @if($cartCount > 0)
                <div class="absolute -top-1 -right-1 flex">
                    <span class="animate-ping absolute inline-flex h-5 w-5 rounded-full bg-primary/60"></span>
                    <span class="relative inline-flex items-center justify-center min-w-5 h-5 rounded-full text-xs font-bold bg-primary text-primary-content shadow-lg">
                        {{ $cartCount }}
                    </span>
                </div>
                @endif
            </button>
        </div>
    </div>
</div>