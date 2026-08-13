<div class="relative w-full mb-6 sm:mb-8 md:mb-12">
    <div class="swiper main-slider">
        <div class="swiper-wrapper">
            @forelse ($VarSliderForHomepageData as $slide)
                <div class="swiper-slide">
                    <div
                        class="relative w-full aspect-[16/9] sm:aspect-[16/8] md:aspect-[16/9] overflow-hidden rounded-lg">
                        <!-- Görsel -->
                        <img src="{{ $slide->image ? asset('storage/' . $slide->image) : asset('default_web_HP_slider_1.png') }}"
                            alt="{{ $slide->title }}"
                            class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700">

                        <!-- Gradient Overlay - Mobil için daha koyu -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent md:from-black/60 md:via-black/30">
                        </div>

                        <!-- İçerik - Padding ve font boyutları responsive -->
                        <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 md:p-8 text-white">
                            <!-- Badge -->
                            <div
                                class="inline-flex items-center gap-1 sm:gap-2 px-2 py-1 sm:px-3 sm:py-1.5 rounded-full bg-primary text-white mb-2 sm:mb-3 md:mb-4">
                                <i class="fas fa-bolt text-[10px] sm:text-xs"></i>
                                <span class="text-xs sm:text-sm font-medium">
                                    {{ $slide->status ? 'Aktif Kampanya' : 'Yakında' }}
                                </span>
                            </div>

                            <!-- Başlık - Responsive font boyutları -->
                            <h2
                                class="text-xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl font-bold mb-2 sm:mb-3 md:mb-4 
                                      line-clamp-2 sm:line-clamp-none
                                      transform transition-transform duration-500 hover:scale-105">
                                {{ $slide->title }}
                            </h2>

                            <!-- Açıklama - Mobilde gizle, tablet ve üstünde göster -->
                            <p
                                class="hidden sm:block text-base sm:text-lg md:text-xl text-gray-200 
                                    max-w-[280px] sm:max-w-md md:max-w-2xl 
                                    mb-3 sm:mb-4 md:mb-6
                                    line-clamp-2 sm:line-clamp-3">
                                {{ $slide->description }}
                            </p>

                            <!-- Buton - Responsive boyutlar -->
                            <a href="{{ $slide->button_link }}"
                                class="btn btn-primary btn-sm sm:btn-md md:btn-lg gap-1 sm:gap-2 group">
                                <span class="text-sm sm:text-base">{{ $slide->button_text }}</span>
                                <i
                                    class="fas fa-arrow-right text-xs sm:text-sm transition-transform group-hover:translate-x-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Boş durum için de responsive tasarım -->
                <div class="swiper-slide">
                    <div
                        class="relative w-full aspect-[16/9] sm:aspect-[16/8] md:aspect-[16/9] overflow-hidden rounded-lg">
                        <img src="{{ asset('default_web_HP_slider_1.png') }}" alt="Varsayılan Slider Görseli"
                            class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 md:p-8 text-white text-center">
                            <i class="fas fa-image text-2xl sm:text-3xl md:text-4xl mb-2 sm:mb-3 md:mb-4"></i>
                            <p class="text-base sm:text-lg md:text-xl">Henüz slider içeriği eklenmemiş</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>


        @if ($VarSliderForHomepageData->count() > 1)
            <!-- Pagination - Daha belirgin ve tıklanabilir -->
            <div class="swiper-pagination !bottom-2 sm:!bottom-4"></div>
        @endif
    </div>
</div>


@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        new Swiper('.main-slider', {
            // Her zaman tek slide göster
            slidesPerView: 1,

            // Eğer birden fazla slide varsa loop aktif
            loop: {{ $VarSliderForHomepageData->count() > 1 ? 'true' : 'false' }},

            // Otomatik oynatma
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },

            // Zoom özelliği
            zoom: {
                maxRatio: 2,
                minRatio: 1,
                toggle: true
            },

            // Pagination sadece birden fazla slide varsa
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true, // Dynamic bullets için
                dynamicMainBullets: 3 // Ana bullet sayısı
            }
        });
    </script>
@endpush
