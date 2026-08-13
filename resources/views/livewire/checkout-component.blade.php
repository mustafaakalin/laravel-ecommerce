<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">🛒🛍️💳✅👉&nbsp;Ödeme&nbsp;👈🛒💸💰👀</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Address & Payment -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Shipping Address Section -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    @if ($useraddresses->isEmpty())
                        <div class="flex flex-col items-center justify-center p-8">
                            <h2 class="text-xl font-semibold mb-4">Adres Bulunamadı!</h2>
                            <p class="text-gray-600 mb-4">Ödeme işlemine devam etmek için lütfen bir teslimat adresi
                                ekleyin.</p>
                            <a href="{{ route('profile.addresses.create') }}" class="btn btn-primary">
                                Yeni Adres Ekle
                            </a>
                        </div>
                    @elseif($useraddresses->count() === 1)
                        @php
                            $address = $useraddresses->first();
                        @endphp
                        <div class="space-y-4">
                            <h2 class="text-xl font-semibold">🚚📦✈️🚢🚀🚛👉&nbsp;Teslimat Adresi&nbsp;👈✨🌟</h2>
                            <div class="p-4 bg-base-200 rounded-lg space-y-2">
                                <p class="font-medium">{{ $address->title }}</p>
                                <p>{{ $address->address }}</p>
                                <p>{{ $address->city }}, {{ $address->state }} {{ $address->zip_code }}</p>
                                <p>{{ $address->country }}</p>
                                <p>Phone: {{ $address->phone }}</p>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            <h2 class="text-xl font-semibold">🚚📦✈️🚢🚀🚛👉&nbsp;Teslimat Adresi Seçiniz&nbsp;👈✨🌟
                            </h2>
                            <select wire:model.live="selectedAddress" class="select select-bordered w-full">
                                <option value="">Bir adres seçin</option>
                                @foreach ($useraddresses as $address)
                                    <option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }}>
                                        {{ $address->title }} - {{ $address->city }}, {{ $address->state }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>

            @if (!$useraddresses->isEmpty())
                <!-- Payment Section -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="text-xl font-semibold mb-6">Ödeme Detayları</h2>

                        <!-- Payment Method Selection -->
                        <div class="mb-6">
                            <select wire:model.live="paymentMethod" class="select select-bordered w-full">
                                <option value="iyzico">iyzico</option>
                            </select>
                        </div>

                        <!-- Credit Card Inputs -->
                        <div class="space-y-4">
                            <div class="form-control">
                                <input type="text" wire:model="card_name" placeholder="Kart Sahibi Adı"
                                    class="input input-bordered w-full">
                                @error('card_name')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control">

                                <input type="number" wire:model="card_number" placeholder="Kart Numarası"
                                    class="input input-bordered w-full" title="iyzico test kartı: 5890040000000016">
                                @error('card_number')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div class="form-control">
                                    <input type="number" wire:model="expire_month" placeholder="MM (Ay)"
                                        class="input input-bordered w-full">
                                    @error('expire_month')
                                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <input type="number" wire:model="expire_year" placeholder="YY (Yıl)"
                                        class="input input-bordered w-full">
                                    @error('expire_year')
                                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <input type="number" wire:model="cvc" placeholder="CVC"
                                        class="input input-bordered w-full">
                                    @error('cvc')
                                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if (!$useraddresses->isEmpty())
            <!-- Right Column - Order Summary -->
            <div class="lg:col-span-1">
                <div
                    class="card bg-gradient-to-t from-primary/10 to-secondary/10 backdrop-blur-2xl  shadow-xl sticky top-4">
                    <div class="card-body">
                        <h2 class="text-xl font-semibold mb-4">Sipariş Özeti</h2>

                        <!-- Cart Items -->
                        <div class="divide-y divide-base-300">
                            @foreach ($cart->items as $item)
                                <div class="py-3 space-y-2">
                                    <div class="flex flex-wrap justify-between items-start gap-2">
                                        <!-- Product Name and Quantity -->
                                        <div>
                                            <span class="font-medium">
                                                {{ $item->product->name }} × {{ $item->quantity }}
                                            </span>
                                        </div>

                                        <!-- Original Price -->
                                        <div>
                                            <span class="line-through text-gray-500">
                                                {{ number_format($item->getOriginalPrice() * $item->quantity, 2) }} ₺
                                            </span>
                                        </div>

                                        <!-- Helper Dropdown with FontAwesome Icon and Emojis -->
                                        <div class="dropdown dropdown-hover dropdown-end">
                                            <div tabindex="0" class="btn btn-circle btn-ghost btn-xs text-info">
                                                <i class="fa-solid fa-info"></i>
                                            </div>
                                            <div tabindex="0" class="card compact dropdown-content bg-base-100 rounded-box z-[1] w-72 shadow">
                                                <div class="card-body text-xs">
                                                    <h2 class="card-title">🔎📝 Hesaplama Detayları</h2>
                                                    @php
                                                        $basePrice = $item->getOriginalPrice();
                                                        $productDiscountAmount = $basePrice * ($item->product->discount / 100);
                                                        $campaign = $item->campaign();
                                                        $campaignDiscount = 0;

                                                        if ($campaign) {
                                                            if ($campaign->discount_type === 'percentage') {
                                                                $campaignDiscount = ($basePrice - $productDiscountAmount) * ($campaign->discount_value / 100);
                                                            } else {
                                                                $campaignDiscount = $campaign->discount_value;
                                                            }
                                                        }
                                                        $finalPricePerItem = $basePrice - $productDiscountAmount - $campaignDiscount;
                                                        $finalPriceTotal = $finalPricePerItem * $item->quantity;
                                                    @endphp
                                                    <p>
                                                        • Orijinal Fiyat (Tekil): {{ number_format($basePrice, 2) }} ₺ <br>
                                                        • Ürün İndirimi ({{ $item->product->discount }}%): 
                                                          {{ number_format($productDiscountAmount, 2) }} ₺ <br>
                                                        • Kampanya İndirimi: {{ number_format($campaignDiscount, 2) }} ₺ <br>
                                                        • Son Fiyat (Tekil): {{ number_format($finalPricePerItem, 2) }} ₺ <br>
                                                        • Son Fiyat (Toplam): {{ number_format($finalPriceTotal, 2) }} ₺
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ürün İndirimi -->
                                    @if ($item->product->discount > 0)
                                        <div class="flex justify-between text-sm text-success">
                                            <span>Ürün İndirimi (-{{ $item->product->discount }}%)</span>
                                            <span>
                                                {{ number_format($item->getOriginalPrice() * $item->quantity * ($item->product->discount / 100), 2) }} ₺
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Kampanya İndirimi -->
                                    @if ($campaign = $item->campaign())
                                        <div class="flex justify-between text-sm text-success">
                                            <span>
                                                {{ $campaign->name }}
                                                @if ($campaign->discount_type === 'percentage')
                                                    (-{{ $campaign->discount_value }}%)
                                                @else
                                                    (-{{ number_format($campaign->discount_value, 2) }} ₺)
                                                @endif
                                            </span>
                                            <span>
                                                @if ($campaign->discount_type === 'percentage')
                                                    @php
                                                        $basePriceAfterProductDiscount = $item->product->discount > 0
                                                            ? $item->getOriginalPrice() * (1 - $item->product->discount / 100)
                                                            : $item->getOriginalPrice();
                                                        $campaignDisc = $basePriceAfterProductDiscount * $item->quantity * ($campaign->discount_value / 100);
                                                    @endphp
                                                    {{ number_format($campaignDisc, 2) }} ₺
                                                @else
                                                    {{ number_format($campaign->discount_value * $item->quantity, 2) }} ₺
                                                @endif
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Son Fiyat -->
                                    <div class="flex justify-between font-semibold text-primary">
                                        <span>Son Fiyat</span>
                                        <span>{{ number_format($item->getTotalPrice(), 2) }} ₺</span>
                                    </div>

                                    <!-- Toplam Tasarruf -->
                                    @if ($item->getDiscountPercentage() > 0)
                                        <div class="text-xs text-success">
                                            Toplam tasarruf: {{ $item->getDiscountPercentage() }}% 
                                            ({{ number_format(($item->getOriginalPrice() - $item->getDiscountedPrice()) * $item->quantity, 2) }} ₺)
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Totals Summary -->
                        <div class="mt-6 space-y-3 border-t pt-4">
                            <div class="flex justify-between">
                                <span>Orijinal Toplam</span>
                                <span class="line-through text-gray-500">
                                    {{ number_format($cart->items->sum(fn($item) => $item->getOriginalPrice() * $item->quantity), 2) }}
                                    ₺
                                </span>
                            </div>

                            <!-- Total Product Discounts -->
                            @php
                                $totalProductDiscounts = $cart->items->sum(function ($item) {
                                    return $item->product->discount > 0
                                        ? $item->getOriginalPrice() * $item->quantity * ($item->product->discount / 100)
                                        : 0;
                                });
                                $originaltotal = $cart->items->sum(
                                    fn($item) => $item->getOriginalPrice() * $item->quantity,
                                );
                                $shipmentPrice = App\Models\SiteSetting::first()->site_shipment_price;
                                $shipmentDiscountprice = App\Models\ShipmentDiscount::first()->price;

                                if ($originaltotal > $shipmentDiscountprice) {
                                    $shipmentPrice = 0;
                                }

                            @endphp
                            @if ($totalProductDiscounts > 0)
                                <div class="flex justify-between text-success">
                                    <span>Ürün İndirimleri</span>
                                    <span>-{{ number_format($totalProductDiscounts, 2) }} ₺</span>
                                </div>
                            @endif
                            @if (isset($shipmentPrice) > 0)
                                <div class="flex justify-between text-gray-500">
                                    <span>Kargo Fiyatı {{ $shipmentDiscountprice }} ₺ ve üzeri kargo bedava </span>
                                    <span>{{ number_format($shipmentPrice, 2) }} ₺</span>
                                </div>
                            @endif

                            <!-- Total Campaign Discounts -->
                            @php
                                $totalCampaignDiscounts = $cart->items->sum(function ($item) {
                                    $campaign = $item->campaign();
                                    if (!$campaign) {
                                        return 0;
                                    }

                                    // Önce ürünün kendi indirimi varsa, indirimli fiyatı al
                                    $basePrice =
                                        $item->product->discount > 0
                                            ? $item->getOriginalPrice() * (1 - $item->product->discount / 100)
                                            : $item->getOriginalPrice();

                                    // Kampanya indirimini indirimli fiyat üzerinden hesapla
                                    return $campaign->discount_type === 'percentage'
                                        ? $basePrice * $item->quantity * ($campaign->discount_value / 100)
                                        : $campaign->discount_value * $item->quantity;
                                });
                            @endphp
                            @if ($totalCampaignDiscounts > 0)
                                <div class="flex justify-between text-success">
                                    <span>Kampanya İndirimleri</span>
                                    <span>-{{ number_format($totalCampaignDiscounts, 2) }} ₺</span>
                                </div>
                            @endif


                            <!-- Final Total Section -->
                            @php
                                // Her ürünün Son Fiyat'larının toplamı (ürün ve kampanya indirimleri dahil)
                                $finalTotal = $cart->items->sum(function ($item) {
                                    return $item->getTotalPrice();
                                });

                                // Kargo ücreti kontrolü
                                $shipmentPrice = App\Models\SiteSetting::first()->site_shipment_price ?? 0;
                                $shipmentDiscountPrice = App\Models\ShipmentDiscount::first()->price ?? 0;

                                // Kupon indirimi (wire:model ile senkronize)
                                if ($couponDiscount > 0) {
                                    // Kupon indirimi Nihai Toplamdan büyük olamaz
                                    $couponDiscount = min($couponDiscount, $finalTotal);
                                    $finalTotal = max(0, $finalTotal - $couponDiscount);
                                }

                                // En son kargo ücreti eklenir
                                if ($finalTotal < $shipmentDiscountPrice) {
                                    $finalTotal += $shipmentPrice;
                                }
                            @endphp

                            @if ($couponDiscount > 0 && $couponDiscountValue > 0)
                                <div class="flex justify-between text-success">
                                    <span>
                                        Kupon İndirimi 
                                        ( -{{ $couponDiscountValue }}{{ $couponDiscountType === 'percentage' ? '%' : '₺' }} )
                                    </span>
                                    <span>-{{ number_format($couponDiscount, 2) }} ₺</span>
                                </div>
                            @endif

                            <!-- Final Total Display -->
                            <div class="flex justify-between font-bold text-lg pt-2 border-t">
                                <span>Nihai Toplam</span>
                                <span>{{ number_format($finalTotal, 2) }} ₺</span>
                            </div>

                            <!-- Total Savings -->
                            <div class="text-success text-sm">
                                Toplam Tasarruf:
                                {{ number_format(
                                    $cart->items->sum(fn($item) => $item->getOriginalPrice() * $item->quantity) -
                                        $finalTotal +
                                        ($finalTotal >= $shipmentDiscountPrice ? $shipmentPrice : 0) +
                                        $couponDiscount,
                                    2,
                                ) }}
                                ₺
                            </div>

                            <!-- Coupon Input -->
                            <div class="mt-4">
                                <div class="join w-full">
                                    <input type="text" wire:model="couponCode" placeholder="Kupon kodunu girin"
                                        class="input input-bordered join-item w-2/3">
                                    <button wire:click="applyCoupon" wire:loading.attr="disabled"
                                        class="btn btn-primary join-item w-1/3">
                                        <span wire:loading.remove wire:target="applyCoupon">Uygula</span>
                                        <span wire:loading wire:target="applyCoupon">
                                            <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                                                <!-- Loading spinner -->
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <!-- Place Order Button -->
                            <button wire:click="placeOrder" wire:loading.attr="disabled"
                                class="btn btn-primary w-full mt-6">
                                <span wire:loading wire:target="placeOrder">
                                    İşleniyor...
                                </span>
                                <span wire:loading.remove>
                                    Sipariş Ver
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
        @endif
    </div>
</div>
