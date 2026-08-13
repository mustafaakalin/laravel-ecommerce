@props(['product', 'rank' => null, 'bestSelling' => null])
<div class="group card bg-base-100/80 hover:bg-transparent backdrop-blur-0 hover:backdrop-blur-sm shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden product-card isolate"
    data-price="{{ $product->getCurrentPrice() }}" data-name="{{ $product->name }}" data-stock="{{ $product->stock }}">




    @if ($rank)
        <div
            class="absolute top-10 left-2 bg-gradient-to-r from-primary/10  to-secondary/10 backdrop-blur-md  rounded-full px-3 py-1 text-xs md:text-xl font-bold text-primary shadow z-20">
            #{{ $rank }}
        </div>
    @endif


    @if ($bestSelling)
        <div
            class="absolute bottom-48 md:bottom-56 right-2 bg-gradient-to-t from-secondary/10  to-primary/10  bg-opacity-30  rounded-full px-3 py-1 text-xs md:text-xl font-bold text-secondary shadow z-20">
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-secondary to-primary">
                {{ $bestSelling }} adet satıldı.
            </span>
        </div>
    @endif



    <!-- Image Section -->
    <figure class="relative w-full aspect-square overflow-hidden">
        @if ($product->images->count() > 1)
            <div class="swiper product-card-swiper-{{ $product->id }} !absolute inset-0">
                <div class="swiper-wrapper h-full">
                    @foreach ($product->images as $index => $image)
                        <div class="swiper-slide h-full">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy"  />
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="swiper-pagination text-primary !bottom-2"></div>
            </div>

            <!-- Hover Overlay for Price -->
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-[1]">
                <!-- Price Section in Image -->
                <div
                    class="absolute bottom-4 left-4 transform translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                    <div class="flex flex-col">
                        @if ($product->discount)
                            <span class="text-gray-300 line-through text-sm md:text-base">
                                <i class="fa-solid fa-turkish-lira-sign"></i>{{ number_format($product->price, 2) }}
                            </span>
                            <span class="text-white font-bold text-lg md:text-xl">
                                <i
                                    class="fa-solid fa-turkish-lira-sign"></i>{{ number_format($product->getCurrentPrice(), 2) }}
                            </span>
                        @else
                            <span class="text-white font-bold text-lg md:text-xl">
                                <i class="fa-solid fa-turkish-lira-sign"></i>{{ number_format($product->price, 2) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    new Swiper('.product-card-swiper-{{ $product->id }}', {
                        // Varsayılan ayarlar (mobile first)
                        effect: 'cards',
                        direction: 'horizontal',
                        grabCursor: true,
                        centeredSlides: true,
                        slidesPerView: 1,
                        mousewheel: false,

                        // Pagination
                        pagination: {
                            el: ".swiper-pagination",
                            clickable: true,
                            dynamicBullets: true,
                        },

                        // Otomatik oynatma
                        autoplay: {
                            delay: 3000,
                            disableOnInteraction: false,
                            pauseOnMouseEnter: true
                        },

                        // Geçiş hızı
                        speed: 800,

                        // Responsive breakpoints
                        breakpoints: {
                            // >= 640px (sm)
                            640: {
                                effect: 'creative',
                                creativeEffect: {
                                    prev: {
                                        translate: [0, 0, -400],
                                    },
                                    next: {
                                        translate: ['100%', 0, 0],
                                    },
                                },
                                direction: 'horizontal',
                            },
                            // >= 768px (md)
                            768: {
                                effect: 'flip',
                                flipEffect: {
                                    slideShadows: true,
                                    limitRotation: true
                                },
                                direction: 'horizontal',
                                mousewheel: true,
                            },
                            // >= 1024px (lg)
                            1024: {
                                effect: 'cube',
                                direction: 'vertical',
                                mousewheel: true,
                                cubeEffect: {
                                    shadow: true,
                                    slideShadows: true,
                                    shadowOffset: 20,
                                    shadowScale: 0.94,
                                },
                                zoom: true,
                            },
                            // >= 1280px (xl)
                            1280: {
                                effect: 'cube',
                                direction: 'vertical',
                                mousewheel: true,
                                zoom: {
                                    maxRatio: 1.5,
                                    minRatio: 1
                                },
                                cubeEffect: {
                                    shadow: true,
                                    slideShadows: true,
                                    shadowOffset: 20,
                                    shadowScale: 0.94,
                                }
                            }
                        },

                        // Lazy loading
                        lazy: {
                            loadPrevNext: true,
                            loadOnTransitionStart: true
                        },

                        // A11y
                        a11y: {
                            enabled: true,
                            prevSlideMessage: 'Önceki görsel',
                            nextSlideMessage: 'Sonraki görsel',
                            firstSlideMessage: 'İlk görsel',
                            lastSlideMessage: 'Son görsel',
                        }
                    });
                });
            </script>

            {{-- <script>
                document.addEventListener('DOMContentLoaded', function() {
                    new Swiper('.product-card-swiper-{{ $product->id }}', {
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
            </script> --}}
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
                                <i class="fa-solid fa-turkish-lira-sign"></i>{{ number_format($product->price, 2) }}
                            </span>
                            <span class="text-white font-bold text-lg md:text-xl">
                                <i
                                    class="fa-solid fa-turkish-lira-sign"></i>{{ number_format($product->getCurrentPrice(), 2) }}
                            </span>
                        @else
                            <span class="text-white font-bold text-lg md:text-xl">
                                <i class="fa-solid fa-turkish-lira-sign"></i>{{ number_format($product->price, 2) }}
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
                                <i class="fa-solid fa-turkish-lira-sign"></i>{{ number_format($product->price, 2) }}
                            </span>
                            <span class="text-white font-bold text-lg md:text-xl">
                                <i
                                    class="fa-solid fa-turkish-lira-sign"></i>{{ number_format($product->getCurrentPrice(), 2) }}
                            </span>
                        @else
                            <span class="text-white font-bold text-lg md:text-xl">
                                <i class="fa-solid fa-turkish-lira-sign"></i>{{ number_format($product->price, 2) }}
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

    <!-- Content Section -->
    <div class="card-body p-4 md:p-6">
        <!-- Product Name -->
        <div class="grid grid-rows-2">
            <h2 class="card-title tooltip tooltip-info z-20  md:text-lg font-semibold line-clamp-1 hover:link-primary hover:link-primary-focus"
                data-tip="{{ $product->name }}">
                <a href="{{ route('products.show', $product->slug) }}"
                    class="text-xs md:text-xl">{{ $product->name }}</a>
            </h2>
            <div class=" {{ $product->is_new || $product->is_featured || $product->is_digital || $product->is_free_shipping ? 'tooltip tooltip-info tooltip-bottom' : null }}"
                data-tip="{{ $product->is_new ? 'Yeni' : null }} {{ $product->is_featured ? 'Öne Çıkan' : null }} {{ $product->is_digital ? 'Dijital' : null }} {{ $product->is_free_shipping ? 'Ücretsiz Kargo' : null }}">
                <div class="line-clamp-1">

                    @if ($product->is_new)
                        {{-- if product is_new --}}
                        <span class="badge badge-success ml-2 animate-pulse">
                            <i class="fa-solid fa-wand-magic-sparkles"></i></span>
                    @endif

                    {{-- if product is_featured --}}
                    @if ($product->is_featured)
                        <span class="badge badge-primary ml-2 animate-pulse">
                            <i class="fas fa-star text-yellow-500"></i>
                        </span>
                    @endif

                    {{-- if product is_digital --}}
                    @if ($product->is_digital)
                        <span class="badge badge-info ml-2 animate-pulse">
                            <i class="fa-solid fa-cloud-arrow-down animate-pulse"></i>
                        </span>
                    @endif

                    {{-- if product is_free_shipping --}}
                    @if ($product->is_free_shipping)
                        <span class="badge badge-warning ml-2 animate-pulse">
                            <i class="fas fa-truck animate-pulse" data-tip="Ücretsiz Kargo"></i>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Brand and Category Links -->
        <div class="flex flex-wrap gap-2 justify-between mt-1  text-xs  md:text-md lg:text-lg line-clamp-1">
            @if ($product->brand)
                <a href="{{ route('brands.show', $product->brand->slug) }}"
                    class="link link-primary hover:link-primary-focus">
                    <i class="fas fa-industry mr-1"></i>{{ $product->brand->name }}
                </a>
            @endif

            @if ($product->category)
                <a href="{{ route('categories.show', $product->category->slug) }}"
                    class="link link-primary hover:link-primary-focus">
                    <i class="fas fa-tag mr-1"></i>{{ $product->category->name }}
                </a>
            @endif
        </div>


        <!-- Stock Status -->
        <div class="mt-3 flex justify-between">
            @if ($product->stock)
                @if ($product->stock > 0 && $product->stock <= 10)
                    <div class="flex items-center gap-1.5 text-yellow-500 text-xs md:text-md lg:text-lg animate-pulse tooltip tooltip-warning"
                        data-tip="Stok:{{ $product->stock }}!">
                        <i class="fas fa-warning"></i>
                        <span class="text-base-content">{{ $product->stock }}!</span>
                    </div>
                @elseif ($product->stock > 10)
                    <div class="flex items-center gap-1.5 text-success text-xs  md:text-md lg:text-lg animate-pulse tooltip tooltip-success"
                        data-tip="Stok:{{ $product->stock }}">
                        <i class="fas fa-check-circle"></i>
                        <span class="text-base-content">{{ $product->stock }}</span>
                    </div>
                @else
                    <div class="flex items-center gap-1.5 text-error   text-xs  md:text-md lg:text-lg   tooltip "
                        data-tip="Stokta Yok">
                        <i class="fas  fa-ban"></i>
                        <span>Tükendi</span>

                    </div>
                @endif
            @endif
            <div class="  text-xs  md:text-md lg:text-lg  tooltip tooltip-info tooltip-top"
                data-tip=" {{ $product->view_count }}">
                <span class="text-secondary line-clamp-1">
                    <i class="fas fa-eye"></i> {{ $product->view_count }}
            </div>
        </div>
        <!-- Rating & Add to Cart Section -->
        <div class="flex items-center justify-between gap-4 mx-auto w-full">

            <div class="w-full flex justify-start gap-1 text-warning">
                <i class="fas fa-star text-2xl"></i>
                <span class="text-base-content font-bold">{{ number_format($product->rating, 1) }}</span>
            </div>

            <div class="w-full flex justify-end">
                <div
                    class="w-full sm:w-auto md:w-auto lg:w-auto xl:w-auto transform transition-transform duration-300 ease-out group-hover:translate-y-0 translate-y-2">
                    @livewire(
                        'add-to-cart',
                        [
                            'product' => $product,
                            'buttonClasses' => 'btn btn-primary w-full sm:w-auto md:w-auto lg:w-auto xl:w-auto bg-gradient-to-r from-primary to-primary-focus hover:shadow-lg hover:shadow-primary/30 transition-all duration-500 ease-in-out',
                        ],
                        key('add-to-cart-' . $product->id)
                    )
                </div>
            </div>
        </div>
    </div>
    @if ($product->is_active)
        {{-- Yellow  Dot --}}
        <div class="animate-pulse absolute bottom-2 left-2 w-3 h-3 bg-green-500 rounded-full tooltip tooltip-warning tooltip-right"
            data-tip="Aktif"></div>
    @else
        {{-- Red Dot --}}
        <div class="animate-pulse absolute bottom-2 left-2 w-3 h-3 bg-red-500 rounded-full tooltip tooltip-error tooltip-right"
            data-tip="Aktif Değil."></div>
    @endif
</div>
