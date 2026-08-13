@extends('layouts.app')

@section('title', $category->name)

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
                    <a href="{{ route('categories.index') }}"
                        class="link link-primary hover:opacity-75 transition-opacity flex items-center">
                        <i class="fas fa-bullhorn text-xs sm:text-sm md:text-base mr-1 sm:mr-2 text-primary"></i>
                        <span class="text-xs sm:text-sm md:text-base">Kategoriler</span>
                    </a>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-tag text-xs sm:text-sm md:text-base mr-1 sm:mr-2 text-primary"></i>
                    <span class="text-xs sm:text-sm md:text-base truncate max-w-[150px] sm:max-w-[200px] md:max-w-none">
                        {{ $category->name }}
                    </span>
                </li>
            </ul>
        </div>


        <!-- Category Header -->
        <div class="bg-gradient-to-r from-primary/10 to-secondary/10 rounded-box p-8 mb-12">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center gap-4 mb-4">
                    <!-- Category Icon -->
                    <div
                        class="w-16 h-16 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center shadow-lg">
                        <i class="fas fa-{{ $category->icon ?? 'folder' }} text-2xl text-base-100"></i>
                    </div>

                    <!-- Category Title -->
                    <h1
                        class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                        {{ $category->name }}

                        @if ($category->is_active)
                            <!-- Active Badge -->
                            <div class="tooltip tooltip-success" data-tip="Aktif">
                                <span
                                    class="text-sm badge badge-success animate-pulse transition-opacity duration-1000"></span>
                            </div>
                        @else
                            <div class="tooltip tooltip-error" data-tip="Aktif Değil">
                                <span
                                    class="text-sm badge badge-error animate-pulse transition-opacity duration-1000"></span>
                            </div>
                        @endif
                    </h1>

                </div>

                @if ($category->description)
                    <p class="text-lg text-base-content/70 leading-relaxed">
                        {{ $category->description }}
                    </p>
                @endif

                <!-- Category Stats -->
                <div class="flex gap-4 mt-6">
                    <div class="badge badge-primary badge-lg gap-2 inline-flex items-center  tooltip tooltip-bottom" data-tip="{{ $products->total() }} Ürün">
                        <i class="fas fa-box-open"></i>
                        <span>{{ $products->total() }}</span>
                        <span class="hidden md:inline">Ürün</span>
                    </div>
                    @if ($products->where('is_featured', true)->count() > 0)
                        <div class="badge badge-secondary badge-lg gap-2 inline-flex items-center  tooltip tooltip-bottom" data-tip="{{ $products->where('is_featured', true)->count() }} Öne Çıkan Ürün">
                            <i class="fas fa-star"></i>
                            <span>{{ $products->where('is_featured', true)->count() }}</span>
                            <span class="hidden md:inline">Öne Çıkan</span>
                        </div>
                    @endif

                    @if ($category->parent)
                        <div class="text-sm  badge  badge-accent tooltip tooltip-info animate-pulse duraction-300"
                            data-tip="Üst Kategori">
                            <i class="fa-solid fa-turn-up"></i>
                            <a href="{{ route('categories.show', $category->parent->slug) }}" class="link link-hover">
                                {{ $category->parent->name }}
                            </a>
                        </div>
                    @elseif ($category->children->count() > 0)
                        <div class="badge badge-secondary gap-2 inline-flex items-center  tooltip tooltip-bottom" data-tip="{{ $category->children->count() }} Alt Kategori">
                            <i class="fas fa-archive h-4 w-4"></i>
                            <span>{{ $category->children->count() }}</span>
                            <span class="hidden md:inline">Alt Kategori</span>
                        </div>

                        <!-- Subcategories -->
                        @if ($category->children->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach ($category->children->take(3) as $child)
                                    <a href="{{ route('categories.show', $child->slug) }}"
                                        class="text-sm link link-hover text-base-content/70">
                                        {{ $child->name }}
                                    </a>
                                    @if (!$loop->last)
                                        <span class="text-base-content/30">•</span>
                                    @endif
                                @endforeach
                                @if ($category->children->count() > 3)
                                    <span class="text-sm text-base-content/50">+{{ $category->children->count() - 3 }}
                                        daha</span>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>


        @if ($category->is_active)
            <!-- Filters and Sort (Optional) -->
            <div class="flex flex-wrap gap-4 items-center justify-between mb-8">
                <div class="join">
                    <button class="btn join-item " onclick="filterProducts('all')">Tümü</button>
                    <button class="btn join-item " onclick="filterProducts('inStock')">Stokta</button>
                    <button class="btn join-item " onclick="filterProducts('onSale')">İndirimli</button>
                </div>

                <select class="select select-bordered  w-full max-w-[200px]" onchange="sortProducts(this.value)">
                    <option disabled selected>Sırala</option>
                    <option value="priceAsc">Fiyat: Düşükten Yükseğe</option>
                    <option value="priceDesc">Fiyat: Yüksekten Düşüğe</option>
                    <option value="newest">En Yeniler</option>
                </select>
            </div>

            <script>
                function filterProducts(filterValue) {
                    const url = new URL(window.location.href);
                    if (filterValue === 'all') {
                        url.searchParams.delete('filter');
                    } else {
                        url.searchParams.set('filter', filterValue);
                    }
                    window.location.href = url.toString();
                }

                function sortProducts(sortValue) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('sort', sortValue);
                    window.location.href = url.toString();
                }
            </script>
        @endif


        <!-- Products Grid -->

        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>

        <!-- Empty State -->
        @if ($products->isEmpty())
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-base-content/20" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="text-xl font-bold mt-4">Henüz Ürün Yok</h3>
                    <p class="text-base-content/70 mt-2">Bu kategoride henüz ürün bulunmuyor.</p>
                </div>
            </div>
        @endif

        <!-- Pagination (if needed) -->
        @if ($products->hasPages())
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function filterProducts(type) {
                // Filter implementation
                window.location.href = "{{ route('categories.show', $category->slug) }}?filter=" + type;
            }

            function sortProducts(sort) {
                // Sort implementation
                window.location.href = "{{ route('categories.show', $category->slug) }}?sort=" + sort;
            }


            document.addEventListener('DOMContentLoaded', function() {
                const swiperContainers = document.querySelectorAll('.swiper-container');
                swiperContainers.forEach(container => {
                    new Swiper(container, {
                        loop: true,
                        autoplay: {
                            delay: 3000,
                            disableOnInteraction: false,
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                    });
                });
            });
        </script>
    @endpush
@endsection
