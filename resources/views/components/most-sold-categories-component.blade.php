<section class="w-full py-8 bg-base-100 backdrop-blur-md bg-transparent">
    <div class="container mx-auto px-4">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl md:text-3xl font-bold text-primary">
                <i class="fas fa-crown text-warning"></i> En Çok Satan Kategoriler 🏆
            </h2>

            <!-- Navigation Buttons (Right) -->
            <div class="w-1/4 flex justify-end gap-2">
                <button class="btn btn-circle relative overflow-hidden bg-gradient-to-r from-primary/70 to-secondary/70 backdrop-blur-sm border-none shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 text-base-100 most-sold-categories-sold-prev">
                    <span class="relative z-10">
                        <i class="fa-solid fa-chevron-left"></i>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-primary/60 to-secondary/60 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                </button>
                <button class="btn btn-circle relative overflow-hidden bg-gradient-to-r from-primary/70 to-secondary/70 backdrop-blur-sm border-none shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110  text-base-100 most-sold-categories-sold-next">
                    <span class="relative z-10">
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                </button>
            </div>
        </div>

        <!-- Swiper Slider -->
        <div class="swiper mostSoldCategories">
            <div class="swiper-wrapper">
                @foreach ($categories as $index => $category)
                    <div class="swiper-slide my-4 px-1">
                        <div class="card bg-base-200 backdrop-blur-md bg-transparent hover:shadow-xl transition-all duration-300 cursor-pointer h-full">
                            <div class="card-body items-center text-center p-4">
                                <!-- Rank Badge -->
                                <div class="absolute -top-2 -left-2 badge badge-primary badge-lg font-bold text-base-100">
                                    #{!! $index + 1 !!}</div>

                                <!-- Icon -->
                                <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                                    @if ($category->icon)
                                        <i class="{{ $category->icon }} text-2xl text-primary"></i>
                                    @else
                                        <i class="fas fa-folder text-2xl text-primary"></i>
                                    @endif
                                </div>

                                <!-- Category Name -->
                                <h3 class="card-title text-base md:text-xl mb-2">{{ $category->name }}</h3>

                                <!-- Products Count -->
                                <p class="text-sm opacity-75">
                                    <i class="fas fa-box-open mr-1"></i>
                                    {{ $category->active_products_count ?? 0 }} Ürün
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Swiper(".mostSoldCategories", {
                slidesPerView: 2,
                spaceBetween: 16,
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: ".most-sold-categories-sold-next",
                    prevEl: ".most-sold-categories-sold-prev",
                },
                breakpoints: {
                    640: {
                        slidesPerView: 3,
                    },
                    768: {
                        slidesPerView: 4,
                    },
                    1024: {
                        slidesPerView: 5,
                    },
                    1280: {
                        slidesPerView: 6,
                    }
                }
            });
        });
    </script>
@endpush
