@isset($count)
    
<div class="fixed bottom-8 left-8 z-50">
    <div class="bg-base-100/60 hover:bg-base-100/80 backdrop-blur-sm rounded-xl p-2 shadow-lg transition-all duration-300 hover:shadow-xl">
        <div class="tooltip tooltip-right" data-tip="{{ $count > 0 ? $count . ' ürün favoride' : 'Favorilerim' }}">
            <button class="btn btn-ghost btn-circle relative group" wire:click="openDrawer" aria-label="Favorilerim">
                <i class="fas fa-heart text-lg transition-all duration-300 group-hover:scale-110 
                    {{ $count > 0 ? 'text-primary' : 'group-hover:text-primary' }}"></i>

                @if($count > 0)
                <div class="absolute -top-1 -right-1 flex">
                    <span class="relative inline-flex items-center justify-center min-w-5 h-5 rounded-full text-xs font-bold bg-primary text-primary-content shadow-lg">
                        {{ $count }}
                    </span>
                </div>
                @endif
            </button>
        </div>
    </div>
</div>
@endisset