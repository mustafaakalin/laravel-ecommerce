@extends('layouts.app')

@section('title', $campaign->name)

@section('content')
    <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
        @if (session('error'))
            <div class="alert alert-error mb-4">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Breadcrumbs -->
        <div class="text-sm sm:text-base breadcrumbs p-3 sm:p-4 rounded-lg mb-4 sm:mb-8  backdrop-blur-sm">
            <ul class="flex flex-wrap items-center gap-1 sm:gap-2">
                <li class="flex items-center">
                    <a href="{{ route('home') }}"
                        class="link link-primary hover:opacity-75 transition-opacity flex items-center">
                        <i class="fas fa-home text-xs sm:text-sm md:text-base mr-1 sm:mr-2 text-primary"></i>
                        <span class="text-xs sm:text-sm md:text-base">Ana Sayfa</span>
                    </a>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('campaigns.index') }}"
                        class="link link-primary hover:opacity-75 transition-opacity flex items-center">
                        <i class="fas fa-bullhorn text-xs sm:text-sm md:text-base mr-1 sm:mr-2 text-primary"></i>
                        <span class="text-xs sm:text-sm md:text-base">Kampanyalar</span>
                    </a>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-tag text-xs sm:text-sm md:text-base mr-1 sm:mr-2 text-primary"></i>
                    <span class="text-xs sm:text-sm md:text-base truncate max-w-[150px] sm:max-w-[200px] md:max-w-none">
                        {{ $campaign->name }}
                    </span>
                </li>
            </ul>
        </div>

        <!-- Campaign Header -->
        <div class="backdrop-blur-lg rounded-lg shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $campaign->name }}</h1>
                    <p class="text-gray-600">{{ $campaign->description }}</p>
                </div>
                <div class="mt-4 md:mt-0 flex flex-col items-end">
                    <div class="bg-primary text-white px-6 py-3 rounded-lg text-xl font-bold">
                        @if ($campaign->discount_type === 'percentage')
                            {{ $campaign->discount_value }}% İNDİRİM
                        @else
                            ₺{{ number_format($campaign->discount_value, 2) }} İNDİRİM
                        @endif
                    </div>
                    <div class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-clock mr-2"></i>
                        {{ $campaign->end_date->diffForHumans() }} Biter
                    </div>
                </div>
            </div>
        </div>

        <!-- Countdown Timer -->
        <div class="mt-4" x-data="countdown" x-init="start()" x-show="!isLoading"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100">

            <div class="flex items-center justify-center space-x-4 tooltip tooltip-info"
                data-tip="Kampanyanın Bitmesine kalan süre" x-show="!isExpired">
                <div
                    class="bg-gradient-to-t from-primary to-secondary backdrop-blur-sm   rounded-lg shadow-lg text-white flex flex-col p-4 min-w-[80px] transform hover:scale-105 transition-transform">
                    <span class="countdown font-mono text-3xl font-bold" x-text="padNumber(days)"></span>
                    <span class="text-sm uppercase tracking-wide mt-1 font-bold">Gün</span>
                </div>
                &nbsp;
                <div
                    class="bg-gradient-to-t from-primary to-secondary backdrop-blur-sm   rounded-lg shadow-lg text-white flex flex-col p-4 min-w-[80px] transform hover:scale-105 transition-transform">
                    <span class="countdown font-mono text-3xl font-bold" x-text="padNumber(hours)"></span>
                    <span class="text-sm uppercase tracking-wide mt-1 font-bold">Saat</span>
                </div>
                &nbsp;
                <div
                    class="bg-gradient-to-t from-primary to-secondary backdrop-blur-sm   rounded-lg shadow-lg text-white flex flex-col p-4 min-w-[80px] transform hover:scale-105 transition-transform">
                    <span class="countdown font-mono text-3xl font-bold" x-text="padNumber(minutes)"></span>
                    <span class="text-sm uppercase tracking-wide mt-1 font-bold">Dakika</span>
                </div>
                &nbsp;
                <div
                    class=" bg-gradient-to-t from-primary to-secondary backdrop-blur-sm  rounded-lg shadow-lg text-white flex flex-col p-4 min-w-[80px] transform hover:scale-105 transition-transform">
                    <span class="countdown font-mono text-3xl font-bold" x-text="padNumber(seconds)"></span>
                    <span class="text-sm uppercase tracking-wide mt-1 font-bold">Saniye</span>
                </div>
            </div>

            <div x-show="isExpired" x-transition class="text-center p-4 bg-red-100 text-red-700 rounded-lg">
                Kampanya sona erdi!
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('countdown', () => ({
                    days: 0,
                    hours: 0,
                    minutes: 0,
                    seconds: 0,
                    timer: null,
                    isLoading: true,
                    isExpired: false,
                    endDate: new Date("{{ $campaign->end_date->format('Y-m-d H:i:s') }}").getTime(),

                    padNumber(number) {
                        return number.toString().padStart(2, '0');
                    },

                    calculateRemaining() {
                        const now = new Date().getTime();
                        const distance = this.endDate - now;

                        if (distance < 0) {
                            this.isExpired = true;
                            this.stopTimer();
                            return;
                        }

                        this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    },

                    start() {
                        this.calculateRemaining();
                        this.isLoading = false;

                        if (!this.isExpired) {
                            this.timer = setInterval(() => {
                                this.calculateRemaining();
                            }, 1000);
                        }
                    },

                    stopTimer() {
                        if (this.timer) {
                            clearInterval(this.timer);
                        }
                    },

                    init() {
                        this.$watch('isExpired', (value) => {
                            if (value) {
                                // Kampanya bittiğinde yapılacak işlemler
                                this.$dispatch('campaign-ended');
                            }
                        });
                    },

                    destroy() {
                        this.stopTimer();
                    }
                }));
            });
        </script>

        <div class="divider"></div>
        <!-- Campaign Products -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-xl">No products available in this campaign.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $products->links() }}
        </div>

    </div>

    @push('scripts')
        <script>
            function addToWishlist(productId) {
                try {
                    // Add your wishlist logic here
                    console.log('Adding product to wishlist:', productId);
                } catch (error) {
                    console.error('Error adding to wishlist:', error);
                }
            }
        </script>
    @endpush
@endsection
