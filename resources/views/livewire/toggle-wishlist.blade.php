<button wire:click="toggleWishlist" 
        class="btn btn-circle btn-sm {{ $isLiked ? 'btn-primary' : 'btn-ghost' }} btn-sm btn-ghost bg-base-100/80 hover:bg-primary hover:text-white tooltip tooltip-left flex items-center justify-center hover:scale-110 transition-transform   animate-bounce hover:animate-none "
        data-tip="{{ $isLiked ? 'Favorilerden Çıkar' : 'Favorilere Ekle' }}">
    <svg xmlns="http://www.w3.org/2000/svg" 
         class="h-5 w-5" 
         fill="{{ $isLiked ? 'currentColor' : 'none' }}" 
         viewBox="0 0 24 24" 
         stroke="currentColor">
        <path stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
</button>