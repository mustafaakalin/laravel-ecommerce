@extends('layouts.app')

@section('title', $product->name)

@section('metatitle', $product->meta_title, $product->name)
@section('metadescription', $product->description, $product->meta_description)
@section('metakeywords', $product->meta_keywords)
@section('metasearchkeywords', $product->search_keywords)


@section('content')
    <div class="container mx-auto px-4 py-8">

        <!-- Breadcrumbs -->
        <div class="mb-8">
            <div class="text-sm breadcrumbs">
                <ul>
                    <li>
                        <a href="{{ route('home') }}" class="flex items-center gap-2">
                            <i class="fas fa-home h-4 w-4"></i>
                            Anasayfa
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}" class="flex items-center gap-2">
                            <i class="fas fa-store h-4 w-4"></i>
                            Ürünler
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('categories.show', $product->category->slug) }}" class="flex items-center gap-2">
                            <i class="fas fa-folder h-4 w-4"></i>
                            {{ $product->category->name }}
                        </a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-shopping-bag h-4 w-4"></i>
                        {{ $product->name }}
                    </li>
                </ul>
            </div>
        </div>

        <!-- Product Details Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Images Section -->
            <figure class="relative pt-20 overflow-hidden w-full aspect-[4/3] md:aspect-square rounded-lg">
                @if ($product->images->count() > 1)
                    <div class="swiper product-swiper-{{ $product->id }} absolute top-0 left-0 w-full h-full">
                        <div class="swiper-wrapper">
                            @foreach ($product->images as $index => $image)
                                <div class="swiper-slide">
                                    <div class="swiper-zoom-container">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                    </div>
                                </div>
                            @endforeach
                        </div>


                        <!-- Navigation Arrows -->
                        <!-- Pagination -->
                        <div class="swiper-pagination text-primary"></div>
                    </div>


                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new Swiper('.product-swiper-{{ $product->id }}', {
                                effect: 'cube',
                                zoom: true,
                                direction: 'vertical',
                                grabCursor: true,
                                centeredSlides: true,
                                slidesPerView: 'auto',
                                mousewheel: true,
                                pagination: {
                                    el: ".swiper-pagination",
                                    clickable: true,
                                },
                                autoplay: {
                                    delay: 3000,
                                    disableOnInteraction: false,
                                    pauseOnMouseEnter: true
                                },
                                cubeEffect: {
                                    shadow: true,
                                    slideShadows: true,
                                    shadowOffset: 20,
                                    shadowScale: 0.94,
                                },
                                speed: 1000,
                            });
                        });
                    </script>
                @elseif ($product->images->count() === 1)
                    <img src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('default_product_image.jpg') }}"
                        alt="{{ $product->name }}"
                        class="absolute top-0 left-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />

                    <!-- Hover Overlay for Price (Single Image) -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <!-- Price Section in Image -->
                        <div
                            class="absolute bottom-4 left-4 transform translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <div class="flex flex-col">
                                @if ($product->discount)
                                    <span class="text-gray-300 line-through text-sm md:text-base">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                    <span class="text-white font-bold text-lg md:text-xl">
                                        ${{ number_format($product->getCurrentPrice(), 2) }}
                                    </span>
                                @else
                                    <span class="text-white font-bold text-lg md:text-xl">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <img src="{{ asset('default_product_image.jpg') }}" alt="{{ $product->name }}"
                        class="absolute top-0 left-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />

                    <!-- Hover Overlay for Price (Single Image) -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <!-- Price Section in Image -->
                        <div
                            class="absolute bottom-4 left-4 transform translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <div class="flex flex-col">
                                @if ($product->discount)
                                    <span class="text-gray-300 line-through text-sm md:text-base">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                    <span class="text-white font-bold text-lg md:text-xl">
                                        ${{ number_format($product->getCurrentPrice(), 2) }}
                                    </span>
                                @else
                                    <span class="text-white font-bold text-lg md:text-xl">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Discount Badge -->
                @if ($product->discount)
                    <div class="absolute top-3 left-3 flex items-center gap-1 z-10">
                        <div class="badge badge-error gap-1 font-semibold text-xs md:text-sm animate-pulse">
                            -{{ $product->discount }}%
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div
                    class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transform translate-x-full group-hover:translate-x-0 transition-all duration-300 z-10">
                    @livewire(
                        'toggle-wishlist',
                        [
                            'product' => $product,
                            'buttonClasses' => 'btn btn-circle btn-sm bg-base-100/90 hover:bg-base-100 shadow-lg hover:shadow-xl backdrop-blur-sm',
                        ],
                        key('wishlist-' . $product->id)
                    )

                    <livewire:quick-view-button :product-id="$product->id" :button-classes="'btn btn-circle btn-sm bg-base-100/90 hover:bg-base-100 shadow-lg hover:shadow-xl backdrop-blur-sm'" />
                </div>
            </figure>

            <!-- Product Information Section -->
            <div class="space-y-8">

                <!-- Product Title , Category, Badge -->
                <div class="space-y-3">
                    <div class="flex items-center gap-3  line-clamp-1">
                        @if ($product->brand)
                            <a href="{{ route('brands.show', $product->brand->slug) }}"
                                class="badge badge-ghost tooltip tooltip-bottom" data-tip="{{ $product->brand->name }}">
                                <i class="fas fa-building mr-1"></i>
                                <span class="hidden md:inline-block ml-1">{{ $product->brand->name }}</span>
                            </a>
                        @endif

                        @if ($product->is_new)
                            <span class="badge badge-success ml-2 animate-pulse truncate text-xs tooltip tooltip-bottom"
                                data-tip="Yeni">
                                <i class="fas fa-bolt mr-1"></i>
                                <span class="hidden md:inline-block ml-1">Yeni</span>

                            </span>
                        @endif

                        {{-- if product is_featured --}}
                        @if ($product->is_featured)
                            <span class="badge badge-primary ml-2 animate-pulse truncate text-xs tooltip tooltip-bottom"
                                data-tip="Öne Çıkan Ürün">
                                <i class="fas fa-star mr-1"></i>
                                <span class="hidden md:inline-block ml-1">Öne Çıkan</span>

                            </span>
                        @endif

                        {{-- if product is_digital --}}
                        @if ($product->is_digital)
                            <span class="badge badge-info ml-2 animate-pulse truncate text-xs tooltip tooltip-bottom"
                                data-tip="Dijital Ürün">
                                <i class="fas fa-cloud-download-alt mr-1"></i>
                                <span class="hidden md:inline-block ml-1">Dijital</span>

                            </span>
                        @endif

                        {{-- if product is_free_shipping --}}
                        @if ($product->is_free_shipping)
                            <span class="badge badge-warning ml-2 animate-pulse truncate text-xs tooltip tooltip-bottom"
                                data-tip="Ücretsiz Kargo">
                                <i class="fas fa-truck mr-1"></i>
                                <span class="hidden md:inline-block ml-1">Ücretsiz Kargo</span>

                            </span>
                        @endif
                    </div>
                    <h1 class="text-4xl font-bold text-base-content">
                        <i class="fas fa-box-open mr-2"></i>{{ $product->name }}
                    </h1>
                    <div class="flex items-center justify-between gap-2">

                        <div class="dropdown dropdown-hover relative z-50">
                            <a tabindex="0" class="flex items-center gap-2">
                                <i class="fas fa-folder h-4 w-4"></i>
                                {{ $product->category->name }}
                                <i class="fas fa-chevron-down h-3 w-3"></i>
                            </a>
                            <ul
                                class="dropdown-content menu menu-sm  z-50 p-2 shadow  bg-gradient-to-t from-primary/10 to-secondary/10 backdrop-blur-lg rounded-box w-56">
                                @if ($product->category->parent)
                                    <li>
                                        <a href="{{ route('categories.show', $product->category->parent->slug) }}">
                                            <i class="fas fa-level-up-alt"></i>
                                            {{ $product->category->parent->name }}
                                        </a>
                                        @if ($product->category->parent->children->count() > 0)
                                            <ul>
                                                @foreach ($product->category->parent->children as $sibling)
                                                    <li>
                                                        <a href="{{ route('categories.show', $sibling->slug) }}">
                                                            {{ $sibling->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endif

                                @if ($product->category->children->count() > 0)
                                    <li>
                                        <a>Alt Kategoriler</a>
                                        <ul>
                                            @foreach ($product->category->children as $child)
                                                <li>
                                                    <a href="{{ route('categories.show', $child->slug) }}">
                                                        {{ $child->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <span class="text-base-content/50"></span>
                        <span class="text-base-content/70">
                            <i class="fas fa-eye mr-1"></i>{{ $product->view_count }} görüntülenme
                        </span>
                    </div>
                </div>



                <!-- Soldout Badge -->
                @if ($purchaseHistory->isNotEmpty())
                    <div class="flex flex-wrap gap-2">

                        @php
                            // Aggregate purchases by user
                            $aggregatedPurchases = $purchaseHistory->groupBy('user.id')->map(function ($purchases) {
                                $user = $purchases->first()->user;
                                $name = $user->name;
                                // Mask the username, keeping first and last characters
                                $maskedName =
                                    strlen($name) > 2
                                        ? $name[0] . str_repeat('*', strlen($name) - 2) . $name[strlen($name) - 1]
                                        : $name;
                                return (object) [
                                    'user' => $user,
                                    'masked_name' => $maskedName,
                                    'total_quantity' => $purchases->sum('quantity'),
                                ];
                            });
                        @endphp

                        @foreach ($aggregatedPurchases as $purchase)
                            <div class="flex items-center gap-2">
                                <div class="avatar">
                                    <div class="w-6 rounded-full">
                                        <img src="{{ $purchase->user->avatar ? Storage::url($purchase->user->avatar) : '/default_user_avatar.jpg' }}"
                                            alt="{{ $purchase->masked_name }}" />
                                    </div>
                                </div>
                                <span class="font-medium">{{ $purchase->masked_name }}</span>
                                <span class="font-bold">{{ $purchase->total_quantity }}</span>

                                @if (
                                    $purchase->user->instagram_account ||
                                        $purchase->user->facebook_account ||
                                        $purchase->user->tiktok_account ||
                                        $purchase->user->x_account)
                                    <div class="dropdown dropdown-hover dropdown-end">
                                        <label tabindex="0" class="btn btn-ghost btn-xs btn-circle">
                                            <!-- Info icon in dropdown -->
                                            <i class="fas fa-info-circle text-base"></i>



                                        </label>
                                        <!-- Social Media Icons -->
                                        <ul tabindex="0"
                                            class="dropdown-content z-[1] menu p-2 shadow-lg bg-base-100 rounded-box w-52">
                                            @if ($purchase->user->instagram_account)
                                                <li>
                                                    <a href="https://instagram.com/{{ $purchase->user->instagram_account }}"
                                                        target="_blank" class="flex items-center gap-2">
                                                        <i class="fab fa-instagram"></i> <!-- Instagram -->
                                                        Instagram
                                                    </a>
                                                </li>
                                            @endif

                                            @if ($purchase->user->facebook_account)
                                                <li>
                                                    <a href="https://facebook.com/{{ $purchase->user->facebook_account }}"
                                                        target="_blank" class="flex items-center gap-2">
                                                        <i class="fab fa-facebook-f"></i> <!-- Facebook -->
                                                        Facebook
                                                    </a>
                                                </li>
                                            @endif

                                            @if ($purchase->user->tiktok_account)
                                                <li>
                                                    <a href="https://tiktok.com/{{ '@' . $purchase->user->tiktok_account }}"
                                                        target="_blank" class="flex items-center gap-2">
                                                        <i class="fab fa-tiktok"></i> <!-- TikTok -->
                                                        TikTok
                                                    </a>
                                                </li>
                                            @endif

                                            @if ($purchase->user->x_account)
                                                <li>
                                                    <a href="https://twitter.com/{{ $purchase->user->x_account }}"
                                                        target="_blank" class="flex items-center gap-2">
                                                        <i class="fab fa-x-twitter"></i> <!-- X/Twitter -->
                                                        X (Twitter)
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        adet satın aldı.
                    </div>
                @endif


                <!-- Rating -->
                <div class="flex items-center gap-2">
                    @auth
                        @if ($product->hasBeenPurchasedBy(auth()->user()))
                            <form action="{{ route('products.rate', $product) }}" method="POST" class="rating">
                                @csrf
                                @for ($i = 1; $i <= 5; $i++)
                                    <input type="radio" name="rating" value="{{ $i }}"
                                        class="mask mask-star-2 bg-orange-400" {{ $i <= $product->rating ? 'checked' : '' }}
                                        onchange="this.form.submit()" />
                                @endfor
                            </form>
                        @else
                            <div class="rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <input type="radio" name="rating" class="mask mask-star-2 bg-orange-400"
                                        {{ $i <= $product->rating ? 'checked' : '' }} disabled />
                                @endfor
                            </div>
                            <span class="text-sm text-base-content/70">(Değerlendirmek için bu ürünü satın alın.)</span>
                        @endif
                    @else
                        <div class="rating">
                            @for ($i = 1; $i <= 5; $i++)
                                <input type="radio" name="rating" class="mask mask-star-2 bg-orange-400"
                                    {{ $i <= $product->rating ? 'checked' : '' }} disabled />
                            @endfor
                        </div>
                        <span class="text-sm text-base-content/70">(Değerlendirmek için giriş yap.)</span>
                    @endauth
                    <div class="tooltip tooltip-info" data-tip="{{ $product->rating }}">
                        <span class="text-base-content/70">{{ (int) $product->rating }} / 5</span>
                    </div>
                </div>



                <!-- Price Section -->
                <div class="card bg-gradient-to-l from-primary/10 to-secondary/10 p-6 rounded-2xl">
                    <div class="flex items-end gap-4">
                        <div class="space-y-1">
                            <span class="text-4xl font-bold text-primary tooltip flex items-center gap-2"
                                data-tip="İndirimli Fiyat">
                                <i class="fas fa-tag"></i>
                                {{ number_format($product->getCurrentPrice(), 2) }}₺
                            </span>
                            @if ($product->discount > 0)
                                <div class="flex items-center gap-3">
                                    <span class="text-xl line-through text-base-content/50 tooltip flex items-center gap-2"
                                        data-tip="Eski Fiyat">
                                        <i class="fas fa-money-bill"></i>
                                        {{ number_format($product->price, 2) }}₺
                                    </span>
                                    <span class="badge badge-error tooltip flex items-center gap-1"
                                        data-tip="İndirim Oranı">
                                        <i class="fas fa-percent"></i>
                                        %{{ $product->discount }}
                                        İndirim
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stock Status and Actions -->
                <div class="flex flex-col gap-2 sm:gap-3 md:gap-4">
                    <div class="flex items-center gap-2 sm:gap-3">
                        @if ($product->stock > 10)
                            <div class="indicator">
                                <span class="indicator-item badge badge-success"></span>
                                <div
                                    class="badge bg-gradient-to-t from-primary/10 to-secondary/10 backdrop-blur-sm gap-1 sm:gap-2 p-2 sm:p-3 text-xs sm:text-sm md:text-base">
                                    <i class="fas fa-check text-xs sm:text-sm md:text-base"></i>
                                    <span class="whitespace-nowrap">Stokta {{ $product->stock }} Adet</span>
                                </div>
                            </div>
                        @elseif($product->stock > 0)
                            <div class="indicator">
                                <div class="badge badge-warning gap-1 sm:gap-2 p-2 sm:p-3 text-xs sm:text-sm md:text-base">
                                    <i class="fas fa-exclamation-triangle text-xs sm:text-sm md:text-base"></i>
                                    <span class="whitespace-nowrap">Son {{ $product->stock }} Ürün</span>
                                </div>
                            </div>
                        @else
                            <div class="indicator">
                                <span class="indicator-item badge badge-error text-xs sm:text-sm">Stokta Yok</span>
                                <div class="badge badge-error gap-1 sm:gap-2 p-2 sm:p-3 text-xs sm:text-sm md:text-base">
                                    <i class="fas fa-times text-xs sm:text-sm md:text-base"></i>
                                    <span class="whitespace-nowrap">Stokta Yok</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if ($product->stock > 0)
                        <div class="flex flex-wrap gap-2 sm:gap-3 md:gap-4">
                            @livewire('add-to-cart', ['product' => $product], key('add-to-cart-' . $product->id))
                            @livewire('toggle-wishlist', ['product' => $product], key('toggle-wishlist-' . $product->id))
                        </div>
                    @endif
                </div>

                <!-- Product Campaigns -->
                @if ($product->campaigns->isNotEmpty())
                    <div class="divider"></div>
                    <div class="space-y-3 px-4 sm:px-6 lg:px-8">
                        <h3 class="text-lg md:text-xl lg:text-2xl font-semibold flex items-center gap-2">
                            <i class="fas fa-gift text-accent"></i>
                            Aktif Kampanyalar
                        </h3>
                        <div class="flex flex-wrap gap-2 sm:gap-3 md:gap-4">
                            @foreach ($product->campaigns as $campaign)
                                <div
                                    class="badge badge-accent p-2 sm:p-3 text-xs sm:text-sm md:text-base flex items-center gap-1">
                                    <i class="fas fa-tag"></i>
                                    {{ $campaign->name }}
                                    @if ($campaign->discount_type === 'percentage')
                                        <i class="fas fa-percent"></i>
                                        %{{ $campaign->discount_value }}
                                        </span>
                                    @else
                                        <i class="fas fa-money-bill"></i>
                                        {{ $campaign->discount_value }}₺
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>


        <div class="container mx-auto px-2 sm:px-4 mt-8 sm:mt-16">


            <!-- Product Tags Section -->
            <section class="w-full py-4 sm:py-6">
                <div class="max-w-full mx-auto">
                    <!-- Section Header -->
                    <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-primary/10">
                            <i class="fas fa-tags text-primary text-lg"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold text-base-content">
                            Ürün Etiketleri
                        </h3>
                    </div>

                    <!-- Tags Container with Horizontal Scroll -->
                    <div class="relative">
                        <div
                            class="flex overflow-x-auto scrollbar-thin scrollbar-thumb-primary/20 scrollbar-track-base-100 
                                    p-4 gap-2 sm:gap-3 snap-x snap-mandatory scroll-smooth">
                            @if (isset($product->tags) && count($product->tags) > 0)
                                @foreach ($product->tags as $tag)
                                    <div class="group flex-shrink-0 snap-start">
                                        <a href="" class="block">
                                            <div
                                                class="relative overflow-hidden rounded-lg border border-primary/20 hover:border-primary 
                                                        bg-base-100 hover:bg-primary/5 transition-all duration-300 transform hover:scale-105">
                                                <div class="flex items-center gap-2 px-4 py-2.5 whitespace-nowrap">
                                                    <i
                                                        class="fas fa-tag text-xs sm:text-sm text-primary 
                                                              group-hover:rotate-12 transition-transform duration-300"></i>
                                                    <span class="text-sm sm:text-base font-medium">
                                                        {{ $tag->name }}
                                                    </span>
                                                </div>

                                                <!-- Hover Effect Overlay -->
                                                <div
                                                    class="absolute inset-0 bg-gradient-to-r from-primary/0 to-primary/5 
                                                            opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <div class="w-full text-center py-8">
                                    <span class="text-base-content/70 italic">
                                        Henüz etiket eklenmemiş
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <!-- Social Share Buttons -->
            <div class="flex items-center gap-2">
                <span class="text-base-content/70">Paylaş:</span>
                <div class="flex flex-wrap gap-2">
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/?url={{ urlencode(route('products.show', ['slug' => $product->slug])) }}"
                        target="_blank" rel="noopener"
                        class="btn btn-square btn-sm bg-[#E4405F] hover:bg-[#d1274a] transition-colors tooltip flex items-center justify-center"
                        data-tip="Share on Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <!-- Facebook -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('products.show', ['slug' => $product->slug])) }}"
                        target="_blank" rel="noopener"
                        class="btn btn-square btn-sm bg-[#1877F2] hover:bg-[#0d65d9] transition-colors tooltip flex items-center justify-center"
                        data-tip="Share on Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>

                    <!-- Twitter/X -->
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('products.show', ['slug' => $product->slug])) }}"
                        target="_blank" rel="noopener"
                        class="btn btn-square btn-sm bg-[#000000] hover:bg-[#232323] transition-colors tooltip flex items-center justify-center"
                        data-tip="Share on X">
                        <i class="fab fa-x-twitter"></i>
                    </a>

                    <!-- LinkedIn -->
                    <a href="https://www.linkedin.com/shareArticle?url={{ urlencode(route('products.show', ['slug' => $product->slug])) }}"
                        target="_blank" rel="noopener"
                        class="btn btn-square btn-sm bg-[#0A66C2] hover:bg-[#094d92] transition-colors tooltip flex items-center justify-center"
                        data-tip="Share on LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>

                    <!-- WhatsApp -->
                    <a href="https://wa.me/?text={{ urlencode(route('products.show', ['slug' => $product->slug])) }}"
                        target="_blank" rel="noopener"
                        class="btn btn-square btn-sm bg-[#25D366] hover:bg-[#128C7E] transition-colors tooltip flex items-center justify-center"
                        data-tip="Share on WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>

                    <!-- Telegram -->
                    <a href="https://t.me/share/url?url={{ urlencode(route('products.show', ['slug' => $product->slug])) }}"
                        target="_blank" rel="noopener"
                        class="btn btn-square btn-sm bg-[#0088cc] hover:bg-[#006699] transition-colors tooltip flex items-center justify-center"
                        data-tip="Share on Telegram">
                        <i class="fab fa-telegram"></i>
                    </a>

                    <!-- Pinterest -->
                    <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(route('products.show', ['slug' => $product->slug])) }}"
                        target="_blank" rel="noopener"
                        class="btn btn-square btn-sm bg-[#E60023] hover:bg-[#ad081b] transition-colors tooltip flex items-center justify-center"
                        data-tip="Share on Pinterest">
                        <i class="fab fa-pinterest"></i>
                    </a>

                    <!-- Reddit -->
                    <a href="https://reddit.com/submit?url={{ urlencode(route('products.show', ['slug' => $product->slug])) }}"
                        target="_blank" rel="noopener"
                        class="btn btn-square btn-sm bg-[#FF4500] hover:bg-[#FF5700] transition-colors tooltip flex items-center justify-center"
                        data-tip="Share on Reddit">
                        <i class="fab fa-reddit-alien"></i>
                    </a>

                    <!-- WeChat -->
                    <a href="weixin://dl/moments?text={{ urlencode(route('products.show', ['slug' => $product->slug])) }}"
                        target="_blank" rel="noopener"
                        class="btn btn-square btn-sm bg-[#07C160] hover:bg-[#06ad53] transition-colors tooltip flex items-center justify-center"
                        data-tip="Share on WeChat">
                        <i class="fab fa-weixin"></i>
                    </a>

                    <!-- Line -->
                    <a href="https://social-plugins.line.me/lineit/share?url={{ urlencode(route('products.show', ['slug' => $product->slug])) }}"
                        target="_blank" rel="noopener"
                        class="btn btn-square btn-sm bg-[#00B900] hover:bg-[#009900] transition-colors tooltip flex items-center justify-center"
                        data-tip="Share on Line">
                        <i class="fab fa-line"></i>
                    </a>

                    <!-- VK -->
                    <a href="https://vk.com/share.php?url={{ urlencode(route('products.show', ['slug' => $product->slug])) }}"
                        target="_blank" rel="noopener"
                        class="btn btn-square btn-sm bg-[#4C75A3] hover:bg-[#3B5998] transition-colors tooltip flex items-center justify-center"
                        data-tip="Share on VK">
                        <i class="fab fa-vk"></i>
                    </a>
                </div>
            </div>

        </div>


        <!-- Tabs Section -->
        <!-- Product Details Section -->
        <div class="container mx-auto px-2 sm:px-4 mt-8 sm:mt-16">
            <div class="card backdrop-blur-sm shadow-xl" x-data="{ activeTab: 'description' }">
                <div class="card-body p-0">
                    <!-- Responsive Tab Navigation -->
                    <div class="tabs tabs-boxed w-full flex flex-wrap sm:flex-nowrap">
                        <button @click="activeTab = 'description'" :class="{ 'tab-active': activeTab === 'description' }"
                            class="tab tab-lifted flex-1 text-sm sm:text-base lg:text-lg py-3 transition-all duration-200 ease-in-out flex items-center justify-center min-h-[48px]">
                            <i class="fa-solid fa-file-lines text-sm sm:text-base lg:text-lg sm:mr-2"></i>
                            <span class="hidden sm:inline">&nbsp;Ürün Açıklaması</span>
                            <span class="sm:hidden">&nbsp;Açıklama</span>
                        </button>
                        <button @click="activeTab = 'specifications'"
                            :class="{ 'tab-active': activeTab === 'specifications' }"
                            class="tab tab-lifted flex-1 text-sm sm:text-base lg:text-lg py-3 transition-all duration-200 ease-in-out flex items-center justify-center min-h-[48px]">
                            <i class="fa-solid fa-list-check text-sm sm:text-base lg:text-lg sm:mr-2"></i>
                            <span class="hidden sm:inline">&nbsp;Teknik Özellikler</span>
                            <span class="sm:hidden">&nbsp;Özellikler</span>
                        </button>
                    </div>

                    <!-- Tab Contents with Responsive Padding -->
                    <div class="p-2 sm:p-4 md:p-6 lg:p-8 my-2 sm:my-4">
                        <!-- Description Tab -->
                        <div x-show="activeTab === 'description'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100" class="w-full">
                            <article
                                class="prose-base w-full prose-img:rounded-xl prose-headings:text-primary max-h-[400px] overflow-y-auto scrollbar-thin scrollbar-thumb-base-300 scrollbar-track-base-100 p-4">
                                @if ($product->description)
                                    <div class="w-full break-words">
                                        {!! str($product->description)->markdown()->sanitizeHtml() !!}
                                    </div>
                                @else
                                    <div
                                        class="flex flex-col items-center justify-center py-4 sm:py-8 text-base-content/70">
                                        <i
                                            class="fa-solid fa-file-circle-question text-3xl sm:text-4xl lg:text-6xl mb-2 sm:mb-4"></i>
                                        <p class="text-base sm:text-lg">Ürün açıklaması bulunmamaktadır.</p>
                                    </div>
                                @endif
                            </article>
                        </div>

                        <!-- Specifications Tab -->
                        <div x-show="activeTab === 'specifications'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100" class="w-full">
                            @if ($product->specifications)
                                <div
                                    class="max-h-[400px] overflow-auto scrollbar-thin scrollbar-thumb-base-300 scrollbar-track-base-100 p-4">
                                    <table class="table table-zebra w-full">
                                        <thead>
                                            <tr class="rounded-lg">
                                                <th
                                                    class="bg-primary/10 backdrop-blur-sm text-primary font-bold text-sm sm:text-base sticky top-0">
                                                    Özellik</th>
                                                <th
                                                    class="bg-primary/10 backdrop-blur-sm text-primary font-bold text-sm sm:text-base sticky top-0">
                                                    Değer
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->specifications as $key => $value)
                                                <tr class="hover:bg-base-200 transition-colors text-sm sm:text-base">
                                                    <td class="font-medium">{{ $key }}</td>
                                                    <td>{{ $value }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-4 sm:py-8 text-base-content/70">
                                    <i
                                        class="fa-solid fa-clipboard-question text-3xl sm:text-4xl lg:text-6xl mb-2 sm:mb-4"></i>
                                    <p class="text-base sm:text-lg">Bu ürün için teknik özellik bulunmamaktadır.</p>
                                </div>
                            @endif
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        @livewire('CommentComponent', ['product' => $product], key('comment-component-' . $product->id))

        <!-- Similar Products Section -->
        @if ($similarProducts->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-2xl font-bold mb-8 flex items-center">
                    <i class="fas fa-layer-group mr-3 text-primary"></i> <!-- Category icon -->
                    <p class="text-secondary">{{ $similarProducts->first()->category->name }}&nbsp;</p> Kategorisinin
                    Ürünleri
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach ($similarProducts as $product)
                        <x-product-card :product="$product">
                            <div class="absolute top-2 right-2 flex gap-2">
                                <i
                                    class="fas fa-heart text-gray-400 hover:text-red-500 transition-colors cursor-pointer"></i>
                                <i
                                    class="fas fa-share text-gray-400 hover:text-blue-500 transition-colors cursor-pointer"></i>
                            </div>
                        </x-product-card>
                    @endforeach
                </div>
            </div>
        @endif


        <!--  Brand Products Section -->
        @if ($brandsimilarProducts->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-2xl font-bold mb-8 flex items-center">
                    <i class="fas fa-store mr-3 text-primary"></i> <!-- Changed to store icon -->
                    <p class="text-secondary">{{ $brandsimilarProducts->first()->brand->name }} </p>
                    <p>&nbsp;Markasının Diğer Ürünleri</p>
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach ($brandsimilarProducts as $product)
                        <x-product-card :product="$product">
                            <div class="absolute top-2 right-2">
                                <i class="fas fa-shopping-bag text-gray-400 hover:text-blue-500 transition-colors"></i>
                            </div>
                        </x-product-card>
                    @endforeach
                </div>
            </div>
        @endif



    </div>

@endsection
