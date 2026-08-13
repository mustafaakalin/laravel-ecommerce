<div class="drawer drawer-start" x-data="{ open: @entangle('isOpen') }">
    <input id="wishlist-drawer" type="checkbox" class="drawer-toggle" x-model="open" />

    <div class="drawer-side z-50">
        <label for="wishlist-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
        <div class="menu p-4 w-80 md:w-96 min-h-full bg-base-100 text-base-content">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <h2 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                    <i class="fas fa-heart text-primary mr-2"></i>
                    Favorilerim ({{ $likedProducts->count() }})
                </h2>
                <button class="btn btn-circle btn-ghost btn-sm" wire:click="close">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            @auth
                @if($likedProducts->isEmpty())
                <div class="flex flex-col items-center justify-center h-[60vh] text-base-content/70">
                    <i class="fas fa-heart-broken text-4xl md:text-5xl mb-4 text-primary/60"></i>
                    <p class="text-lg font-medium mb-2">Favorileriniz Boş</p>
                    <p class="text-sm mb-4">Ürünleri keşfetmeye başlayın!</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm gap-2">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="hidden md:inline">Ürünlere Git</span>
                        <span class="inline md:hidden">Ürünler</span>
                    </a>
                </div>
                @else
                    <!-- Products List -->
                    <div class="flex flex-col gap-3 md:gap-4 flex-1">
                        <div class="flex-1 overflow-y-auto space-y-3 md:space-y-4 pr-2">
                            @foreach($likedProducts as $product)
                            <div class="card bg-base-200 shadow-sm hover:shadow-md transition-shadow">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    <div class="card-body p-3 md:p-4">
                                        <div class="flex gap-3 md:gap-4">
                                            <!-- Product Image -->
                                            <div class="w-16 md:w-20 h-16 md:h-20 rounded-lg bg-base-300 overflow-hidden">
                                                @if($product->images->count() > 0)
                                                    <div class="swiper product-swiper-{{ $product->id }}">
                                                        <div class="swiper-wrapper">
                                                            @foreach($product->images as $image)
                                                                <div class="swiper-slide">
                                                                    <img src="/storage/{{ $image->image_path }}"
                                                                        alt="{{ $product->name }}" 
                                                                        class="w-full h-full object-cover">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        @if($product->images->count() > 1)
                                                            <div class="swiper-pagination"></div>
                                                        @endif
                                                    </div>
                                                    @push('scripts')
                                                    <script>
                                                        new Swiper('.product-swiper-{{ $product->id }}', {
                                                            loop: true,
                                                            pagination: {
                                                                el: '.swiper-pagination',
                                                                clickable: true,
                                                            },
                                                            autoplay: {
                                                                delay: 3000,
                                                                disableOnInteraction: false,
                                                            },
                                                        });
                                                    </script>
                                                    @endpush
                                                @else
                                                    <img src="/default_product_image.jpg"
                                                        alt="{{ $product->name }}"
                                                        class="w-full h-full object-cover">
                                                @endif
                                            </div>

                                            <!-- Product Details -->
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-sm md:text-base">{{ $product->name }}</h3>
                                                <p class="text-xs md:text-sm text-base-content/70">
                                                    <i class="fas fa-tag text-xs mr-1"></i>
                                                    {{ $product->category->name }}
                                                </p>

                                                <div class="flex items-center justify-between mt-2">
                                                    <div class="text-base md:text-lg font-bold text-primary">
                                                        <i class="fas fa-turkish-lira-sign text-sm mr-1"></i>
                                                        {{ $product->price }}
                                                    </div>

                                                    <div class="flex items-center space-x-3">
                                                        {{-- Stock Status Indicator --}}
                                                        <div class="flex items-center gap-2 transition-all duration-200">
                                                            @if($product->stock === 0)
                                                                <span class="inline-flex items-center text-sm text-red-600">
                                                                    <i class="fas fa-times-circle mr-2 text-base text-red-600"></i>
                                                                    <span class="font-medium text-red-600">Tükendi</span>
                                                                </span>
                                                            @elseif($product->stock <= 5)
                                                                <span class="inline-flex items-center text-sm text-amber-600">
                                                                    <i class="fas fa-exclamation-triangle mr-2 text-base text-amber-600"></i>
                                                                    <span class="font-medium text-amber-600">Sınırlı Stok ({{ $product->stock }} adet)</span>
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center text-sm text-emerald-600">
                                                                    <i class="fas fa-check-circle mr-2 text-base text-emerald-600"></i>
                                                                    <span class="font-medium text-emerald-600">Stokta</span>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="border-t pt-4 space-y-4">
                        <a href="{{ route('wishlist.index') }}" class="btn btn-primary w-full btn-lg gap-2">
                            <i class="fas fa-heart text-xl"></i>
                            <span class="hidden md:inline">Tüm Favorilerim</span>
                            <span class="inline md:hidden">Favoriler</span>
                        </a>
                    </div>
                @endif
            @else
                <div class="flex flex-col items-center justify-center h-[60vh] text-base-content/70">
                    <i class="fas fa-user-lock text-4xl md:text-5xl mb-4 text-primary/60"></i>
                    <p class="text-lg font-medium mb-2">Lütfen Giriş Yapın</p>
                    <p class="text-sm mb-4">Alışverişe başlamak için giriş yapmalısınız</p>
                    <a href="{{ route('filament.admin.auth.login') }}" class="btn btn-primary btn-sm gap-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="hidden md:inline">Giriş Yap</span>
                        <span class="inline md:hidden">Giriş</span>
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>