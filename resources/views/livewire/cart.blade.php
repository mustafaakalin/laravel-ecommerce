<div class="drawer drawer-end" x-data="{ open: @entangle('drawer') }">
    <input id="cart-drawer" type="checkbox" class="drawer-toggle" x-model="open" />

    <div class="drawer-side z-50">
        <label for="cart-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
        <div class="menu  min-h-full  bg-base-100    backdrop-blur-lg text-base-content">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                    Sepetim 
                    @auth
                        @if ($cart && $cart->items->count() > 0)
                            ({{ $cart->getTotalItems() }} Ürün)
                        @else
                            (0 Ürün)
                        @endif 
                    @endauth
                </h2>
                <button class="btn btn-circle btn-ghost btn-sm" wire:click="toggleDrawer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            

            <!-- Scrollable Cart Items Container -->
            <div class="overflow-y-auto pr-2" style="max-height: calc(100vh - 400px);">
                @auth
                @if($cart && $cart->items->count() > 0)
                <div class="flex flex-col gap-4 flex-1">
                    <!-- Cart Items -->
                    @foreach($cart->items as $item)
                    <div class="card bg-gradient-to-t from-primary/20 to-secondary/20   shadow-sm hover:shadow-md transition-shadow">
                        <div class="card-body p-4">
                            <div class="flex gap-4">
                                <!-- Product Image -->
                                <div class="w-20 h-20 rounded-lg bg-base-300 overflow-hidden">
                                    @if ($item->product->images->count() > 1)
                                    <img src="/storage/{{ $item->product->images->first()?->image_path ?? 'default_product_image.jpg' }}"
                                        alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    @endif

                                    <img src="/default_product_image.jpg" alt="{{ $item->product->name }}"
                                        class="w-full h-full object-cover">
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1">
                                    <a href="{{ route('products.show', $item->product->slug) }}"
                                        class="link link-primary">
                                        <h3 class="font-semibold text-base line-clamp-1">{{ $item->product->name }}</h3>
                                    </a>
                                    <p class="text-sm text-base-content/70">{{ $item->product->category->name }}</p>

                                    <!-- Price Display Section -->
                                    <div class="flex flex-col gap-1 mt-2">
                                        <!-- Original and Final Price -->
                                        <div class="flex items-center gap-2 text-xl">
                                            <span class="font-bold text-primary">
                                                {{ number_format($item->getDiscountedPrice(), 2) }} ₺
                                            </span>
                                            @if($item->getDiscountPercentage() > 0)
                                            <span class="text-sm line-through text-base-content/50">
                                                {{ number_format($item->getOriginalPrice(), 2) }} ₺
                                            </span>
                                            @endif
                                        </div>

                                        <!-- Discount Information -->
                                        @php $discounts = $item->getDiscountInfo(); @endphp
                                        @if(!empty($discounts))
                                        <div class="text-xs space-y-1">
                                            @foreach($discounts as $type => $discount)
                                            <div class="flex items-center gap-1">
                                                <span
                                                    class="badge badge-sm {{ $type === 'campaign' ? 'badge-secondary' : 'badge-accent' }}">
                                                    @if($type === 'campaign')
                                                    {{ $discount['name'] }}:
                                                    @else
                                                    Ürün İndirimi:
                                                    @endif
                                                    -{{ $discount['type'] === 'percentage' ? $discount['value'] .
                                                    '%' :
                                                    number_format($discount['value'], 2) . '₺' }}
                                                </span>
                                            </div>
                                            @endforeach
                                            @if(count($discounts) > 1)
                                            <div class="text-success text-xs">
                                                En iyi fiyat uygulandı!
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Quantity Controls and Total -->
                                    <div class="flex items-center justify-between mt-2">
                                        <div class="join">
                                            <button class="btn btn-sm join-item"
                                                wire:click="updateQuantity({{ $item->id }}, -1)">-</button>
                                            <span class="btn btn-sm join-item no-animation bg-base-100">
                                                {{ $item->quantity }}
                                            </span>
                                            <button class="btn btn-sm join-item"
                                                wire:click="updateQuantity({{ $item->id }}, 1)" {{ $item->quantity
                                                >=
                                                $item->product->stock ? 'disabled' : '' }}>+</button>
                                        </div>

                                        <!-- Remove -->
                                        <div class="flex items-center gap-2">
                                            <button class="btn btn-ghost btn-sm btn-circle text-error"
                                                wire:click="removeItem({{ $item->id }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @elseif($cart && $cart->items->count() === 0)
                <div class="flex flex-col items-center justify-center h-[60vh] text-base-content/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <p class="text-lg font-medium mb-2">Sepetiniz Boş</p>
                    <p class="text-sm mb-4">Ürünleri keşfetmeye başlayın!</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">
                        Alışverişe Başla
                    </a>
                </div>
                @endauth
                @else
                <div class="flex flex-col items-center justify-center h-[60vh] text-base-content/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <p class="text-lg font-medium mb-2">Lütfen Giriş Yapın</p>
                    <p class="text-sm mb-4">Alışverişe başlamak için giriş yapmalısınız</p>
                    <a href="{{ route('filament.admin.auth.login') }}" class="btn btn-primary btn-sm">
                        Giriş Yap
                    </a>
                </div>
                @endif
            </div>


            @if($cart && $cart->items->count() > 0)
            <!-- Cart Summary -->
            <div class="border-t pt-4 space-y-4">
                <div class="divider divider-primary my-2"></div>
                <div class="flex justify-between items-center text-lg">
                    <span class="font-medium">Ara Toplam:</span>
                    <span class="font-bold">
                        @auth
                        @if ($cart && $cart->items->count() > 0)
                            {{ number_format($cart->calculateTotalPrice(), 2) }}
                        @else
                        0,00
                        @endif    
                        @endauth ₺</span>
                </div>

                <div class="flex justify-between items-center text-sm text-base-content/70">
                    <span>Kargo (₺):</span>
                    @php
                        $siteSetting = App\Models\SiteSetting::first();
                        $shipmentDiscount = App\Models\ShipmentDiscount::first();
                        $siteShipmentPrice = $siteSetting->site_shipment_price;
                        if($cart->calculateTotalPrice() >= $shipmentDiscount->price){
                            $siteShipmentPrice = 'Ücretsiz';
                        }
                    @endphp 
                    <span>{{ $siteShipmentPrice  ?? 'Ücretsiz' }}</span>
                </div>

                <div class="divider divider-primary my-2"></div>

                <div class="flex justify-between items-center text-xl">
                    <span class="font-bold">Toplam:</span>
                    <span class="font-bold text-primary">
                        
                        @auth
                        @if ($cart && $cart->items->count() > 0)
                            {{ number_format($cart->calculateTotalPrice(), 2) }}
                        @else
                        0,00
                        @endif    
                        @endauth  ₺
                    </span>
                </div>
                

                <a href="{{ route('checkout.index') }}" class="btn btn-primary w-full btn-lg gap-2">
                    <i class="fa-solid fa-wallet"></i>
                    Ödemeye Geç
                </a>
            </div>
            @endif
        </div>
    </div>
</div>