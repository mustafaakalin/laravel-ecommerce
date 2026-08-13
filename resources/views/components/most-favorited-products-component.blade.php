<section class="w-full py-8 bg-base-200/50 backdrop-blur-sm">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="flex items-center justify-between mb-6">

            <!-- View All Button (Left) -->
            <div class="w-1/4">
                <a href="{{ route('products.index') }}"
                    class="btn group relative overflow-hidden bg-gradient-to-r from-primary/70 to-secondary/70 backdrop-blur-sm border-none shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 text-base-100">
                    <span class="relative z-10 hidden md:inline mb:block">Tüm Ürünleri Gör</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform relative z-10"></i>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-primary to-secondary opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    </div>
                </a>
            </div>

            <!-- Title and Description (Center) -->
            <div class="text-center w-2/4 p-4">
                <h2 class="text-xl md:text-4xl font-bold mb-4 text-base-content">
                    <span class="inline-block animate-bounce"><i class="fas fa-heart text-red-500"></i></span>
                    En Çok Beğenilenler
                    <span class="inline-block animate-bounce">🏆</span>
                </h2>
                <p class="text-base-content/80 text-xs md:text-md">
                    <i class="fas fa-chart-line text-primary"></i>
                    Müşterilerimizin en çok Beğendiği 10 ürün.
                </p>
            </div>

            <!-- Navigation Buttons (Right) -->
            <div class="w-1/4 flex justify-end gap-2">
                <button
                    class="btn btn-circle relative overflow-hidden bg-gradient-to-r from-primary/70 to-secondary/70 backdrop-blur-sm border-none shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 text-base-100 most-favorited-swiper-button-prev">
                    <span class="relative z-10">
                        <i class="fa-solid fa-chevron-left"></i>
                    </span>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-primary/60 to-secondary/60 opacity-0 hover:opacity-100 transition-opacity duration-300">
                    </div>
                </button>
                <button
                    class="btn btn-circle relative overflow-hidden bg-gradient-to-r from-primary/70 to-secondary/70 backdrop-blur-sm border-none shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110  text-base-100 most-favorited-swiper-button-prev">
                    <span class="relative z-10">
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-primary to-secondary opacity-0 hover:opacity-100 transition-opacity duration-300">
                    </div>
                </button>
            </div>

        </div>

        <!-- Swiper -->
        <div class="swiper mostFavorited">
            <div class="swiper-wrapper">
                @foreach ($mostFavoritedProducts as $index => $product)
                    <div class="swiper-slide">
                        <div class="relative">
                            @if ($index < 10)
                                <div
                                    class="absolute -top-2 left-1/2 -translate-x-1/2 z-10 flex items-center justify-center w-10 h-10 rounded-full bg-primary/30 backdrop-blur-sm text-base-100 font-bold text-xs md:text-xl shadow-lg">
                                    @switch($index)
                                        @case(0)
                                            <span class="flex items-center">1 <i
                                                    class="fas fa-crown text-yellow-300 ml-1"></i></span>
                                        @break

                                        @case(1)
                                            <span class="flex items-center">2 <i
                                                    class="fas fa-medal text-gray-300 ml-1"></i></span>
                                        @break

                                        @case(2)
                                            <span class="flex items-center">3 <i
                                                    class="fas fa-medal text-amber-600 ml-1"></i></span>
                                        @break

                                        @case(3)
                                            <span class="flex items-center">4 <i
                                                    class="fas fa-star text-yellow-400 ml-1"></i></span>
                                        @break

                                        @case(4)
                                            <span class="flex items-center">5 <i
                                                    class="fas fa-star-half-alt text-yellow-400 ml-1"></i></span>
                                        @break

                                        @case(5)
                                            <span class="flex items-center">6 <i
                                                    class="fas fa-certificate text-blue-400 ml-1"></i></span>
                                        @break

                                        @case(6)
                                            <span class="flex items-center">7 <i
                                                    class="fas fa-award text-purple-400 ml-1"></i></span>
                                        @break

                                        @case(7)
                                            <span class="flex items-center">8 <i
                                                    class="fas fa-gem text-pink-400 ml-1"></i></span>
                                        @break

                                        @case(8)
                                            <span class="flex items-center">9 <i
                                                    class="fas fa-trophy text-orange-400 ml-1"></i></span>
                                        @break

                                        @case(9)
                                            <span class="flex items-center">10 <i
                                                    class="fas fa-fire text-red-400 ml-1"></i></span>
                                        @break
                                    @endswitch
                                </div>
                            @endif
                            <x-product-card :product="$product" />
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Sayfalama -->
            <div class="absolute top-5 left-1/2 -translate-x-1/2 z-10">
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
    @push('style')
        <style>
            /* Akıcı bir geçiş için lineer easing ayarı */
            .mostFavorited .swiper-wrapper {
                transition-timing-function: linear !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new Swiper('.mostFavorited', {
                    loop: true,
                    speed: 1500, // Geçiş hızını 1500ms olarak ayarlandı
                    grabCursor: true,
                    centeredSlides: false,
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true
                    },
                    navigation: {
                        nextEl: '.most-favorited-swiper-button-next',
                        prevEl: '.most-favorited-swiper-button-prev',
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        dynamicBullets: true,
                    },
                    breakpoints: {
                        320: {
                            slidesPerView: 2,
                            spaceBetween: 10
                        },
                        640: {
                            slidesPerView: 2,
                            spaceBetween: 15
                        },
                        768: {
                            slidesPerView: 3,
                            spaceBetween: 20
                        },
                        1024: {
                            slidesPerView: 4,
                            spaceBetween: 20
                        }
                    }
                });
            });
        </script>
    @endpush
</section>
