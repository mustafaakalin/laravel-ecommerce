@if ($mode === 'quick' || $mode === 'all')
<div class="flex items-center gap-2">
    <button wire:click="toggleWishlist"
        class="btn btn-circle btn-sm {{ $isLiked ? 'btn-primary' : 'btn-ghost' }} bg-base-100/80 hover:bg-primary hover:text-white tooltip tooltip-left"
        data-tip="{{ $isLiked ? 'Favorilerden Çıkar' : 'Favorilere Ekle' }}">
        <i class="fas fa-heart"></i>
    </button>
    <button type="button"
        class="btn btn-circle btn-sm btn-ghost bg-base-100/80 hover:bg-primary hover:text-white tooltip tooltip-left"
        data-tip="Hızlı Bakış"
        onclick="Livewire.dispatch('show-quick-view', { productId: {{ $productId }} })">
        <i class="fas fa-eye"></i>
    </button>
</div>
@endif

@if ($mode === 'cart' || $mode === 'all')
<button wire:click="addToCart" wire:loading.attr="disabled"
    class="btn btn-primary min-w-[48px] h-12 px-3 md:px-4 flex items-center justify-center gap-2">
    <span wire:loading.remove><i class="fa-solid fa-cart-shopping text-lg"></i><span class="hidden md:inline-block ml-2">Sepete Ekle</span></span>
    <span wire:loading><i class="fa-solid fa-spinner fa-spin text-lg"></i></span>
</button>
@endif
