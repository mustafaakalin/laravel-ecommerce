<div class="fixed bottom-4 right-4 flex flex-col gap-2 z-50">
    @auth
        <!-- Wishlist Button -->
        <a href="{{ route('wishlist.index') }}" 
           class="btn btn-circle btn-primary shadow-lg hover:scale-110 transition-transform"
           data-tip="Favorilerim"
           class="tooltip tooltip-left">
            <div class="indicator">
                <i class="fa-solid fa-heart text-lg"></i>
                @if($wishlistCount > 0)
                    <span class="indicator-item badge badge-secondary badge-sm">{{ $wishlistCount }}</span>
                @endif
            </div>
        </a>

        <!-- Cart Button -->
        <button 
            class="btn btn-circle btn-secondary shadow-lg hover:scale-110 transition-transform"
            onclick="document.querySelector('[data-cart-drawer]').showModal()"
            data-tip="Sepetim"
            class="tooltip tooltip-left">
            <div class="indicator">
                <i class="fa-solid fa-shopping-cart text-lg"></i>
                @if($cartCount > 0)
                    <span class="indicator-item badge badge-primary badge-sm">{{ $cartCount }}</span>
                @endif
            </div>
        </button>
    @endauth
</div>