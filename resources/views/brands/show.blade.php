@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">



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
                    <a href="{{ route('brands.index') }}"
                        class="link link-primary hover:opacity-75 transition-opacity flex items-center">
                        <i class="fas fa-bullhorn text-xs sm:text-sm md:text-base mr-1 sm:mr-2 text-primary"></i>
                        <span class="text-xs sm:text-sm md:text-base">Markalar</span>
                    </a>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-tag text-xs sm:text-sm md:text-base mr-1 sm:mr-2 text-primary"></i>
                    <span class="text-xs sm:text-sm md:text-base truncate max-w-[150px] sm:max-w-[200px] md:max-w-none">
                        {{ $brand->name }}
                    </span>
                </li>
            </ul>
        </div>


        <!-- Brand Header Section -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-12 bg-base-100 rounded-2xl p-6 shadow-lg">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 bg-base-200 rounded-xl p-4 shadow-inner">
                    <img src="{{ asset(Storage::url($brand->logo)) }}" alt="{{ $brand->name }}"
                        class="w-full h-full object-contain filter drop-shadow-md">
                </div>
                <div class="space-y-2">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                        {{ $brand->name }}
                    </h1>
                    @if ($brand->description)
                        <p class="text-base-content/70 max-w-xl">{!! str($brand->description)->markdown()->sanitizeHtml() !!}</p>
                    @endif
                </div>
            </div>
            <div class="stats shadow mt-4 md:mt-0">
                <div class="stat place-items-center">
                    <div class="stat-title">Ürün Sayısı</div>
                    <div class="stat-value text-primary">{{ $products->total() }}</div>
                </div>
            </div>
        </div>

        @if ($products->isEmpty())
            <!-- Empty State -->
            <div class="hero min-h-[400px] bg-base-200 rounded-2xl">
                <div class="hero-content text-center">
                    <div class="max-w-md">
                        <div class="mb-8 text-8xl">🏪</div>
                        <h2 class="text-2xl font-bold mb-4">Henüz Ürün Bulunmuyor</h2>
                        <p class="mb-6 text-base-content/70">Bu markaya ait ürünler yakında eklenecektir.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            Ana Sayfaya Dön
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Products Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Optional: Add smooth scrolling for carousel navigation
            document.querySelectorAll('.carousel-item a').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    document.querySelector(href).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        </script>
    @endpush
@endsection
