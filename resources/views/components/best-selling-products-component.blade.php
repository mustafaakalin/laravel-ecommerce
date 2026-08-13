<section class="w-full py-4 bg-base-100 backdrop-blur-md bg-transparent">

    <div class="container mx-auto px-4">

        <!-- Header Section -->
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
                    <span class="inline-block animate-bounce"><i class="fas fa-trophy text-yellow-500"></i> </span>
                    En Çok Satılan Ürünler
                    <span class="inline-block animate-bounce">🏆</span>
                </h2>
                <p class="text-base-content/80 text-xs md:text-md">
                    <i class="fas fa-chart-line text-primary"></i>
                    Müşterilerimizin en çok Satın Aldığı 10 ürün
                </p>
            </div>

            <!-- Navigation Buttons (Right) -->
            <div class="w-1/4 flex justify-end gap-2">
                <button
                    class="btn btn-circle relative overflow-hidden bg-gradient-to-r from-primary/70 to-secondary/70 backdrop-blur-sm border-none shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 text-base-100 best-selling-swiper-button-prev">
                    <span class="relative z-10">
                        <i class="fa-solid fa-chevron-left"></i>
                    </span>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-primary/60 to-secondary/60 opacity-0 hover:opacity-100 transition-opacity duration-300">
                    </div>
                </button>
                <button
                    class="btn btn-circle relative overflow-hidden bg-gradient-to-r from-primary/70 to-secondary/70 backdrop-blur-sm border-none shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110  text-base-100 best-selling-swiper-button-next ">
                    <span class="relative z-10">
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-primary to-secondary opacity-0 hover:opacity-100 transition-opacity duration-300">
                    </div>
                </button>
            </div>
        </div>

        <div class="best-selling-swiper swiper">
            <div class="swiper-wrapper">
                @foreach ($products as $index => $product)
                    <div class="swiper-slide">
                        {{-- Mevcut product-card bileşenini kullanıyoruz --}}
                        <x-product-card :product="$product" :rank="$loop->iteration" :bestSelling="$product->sales" />
                    </div>
                @endforeach
            </div>
            <!-- Sayfalama -->
            <div class="swiper-pagination"></div>
        </div>

        @push('style')
            <style>
                /* Akıcı bir geçiş için lineer easing ayarı */
                .best-selling-swiper .swiper-wrapper {
                    transition-timing-function: linear !important;
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    new Swiper('.best-selling-swiper', {
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
                            nextEl: '.best-selling-swiper-button-next',
                            prevEl: '.best-selling-swiper-button-prev',
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

    </div>
</section>
