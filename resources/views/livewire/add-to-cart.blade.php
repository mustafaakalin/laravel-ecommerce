<div class=" tooltip tooltip-info  before:tooltip-content-[attr(data-tip)]"
    data-tip="{{ Auth::check() ? 'Sepete Ekle' : 'Giriş yapmanız gerek' }}">
    <button wire:click="addToCart" wire:loading.attr="disabled"
        class=" btn btn-primary hover:btn-secondary relative group overflow-hidden min-w-[48px] h-12 px-3 md:px-4 flex items-center justify-center gap-2 transition-all duration-300"
        >
        <!-- Normal Durum -->
        <div wire:loading.remove class="relative z-10 flex items-center gap-2">
            <!-- Cart Icon -->
            <i
                class="fa-solid fa-cart-shopping text-lg transition-transform duration-300 group-hover:scale-110 animate-bounce"></i>
            <!-- Text (Hidden on mobile) -->
            <span class="hidden md:inline-block font-medium">Sepete Ekle</span>
        </div>

        <!-- Loading Spinner -->
        <div wire:loading class="relative z-10 flex items-center gap-2">
            <i class="fa-solid fa-spinner fa-spin text-lg"></i>
            <span class="hidden md:inline-block font-medium">Ekleniyor...</span>
        </div>

        <!-- Gradient Background Effect -->
        <div
            class="absolute inset-0 bg-gradient-to-r from-primary/20 to-secondary/20 opacity-0 group-hover:opacity-100 transition-all duration-300">
        </div>

        <!-- Ripple Effect -->
        <div
            class="absolute inset-0 bg-white/20 scale-0 group-active:scale-100 rounded-lg transition-transform duration-300">
        </div>
    </button>
</div>
