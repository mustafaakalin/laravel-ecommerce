@extends('layouts.app')

@section('title', 'Anasayfa')

@section('content')

<!-- Hero Section -->
<x-slider-for-homepage />


<!-- Hero Section with Advanced Design -->
<!-- When there is no desire, all things are at peace. - Laozi -->
<div class="relative overflow-hidden rounded-box bg-gradient-to-br from-base-100 to-base-200 mb-24">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -right-10 -top-10 w-48 h-48 bg-primary/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-secondary/10 rounded-full blur-3xl"></div>
        <div
            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-accent/5 rounded-full blur-3xl">
        </div>
    </div>


    <!-- Main Hero Content -->
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-8 items-center min-h-[600px] relative">
            <!-- Left Column: Content -->
            <div class="space-y-6 py-12 text-center lg:text-left">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 text-primary">
                    <i class="fas fa-stars text-xs"></i>
                    <span class="text-sm font-medium">Yeni Sezon İndirimleri</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl lg:text-6xl font-bold">
                    <span class="block mb-2">Alışverişin En</span>
                    <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                        Keyifli Hali
                    </span>
                </h1>

                <!-- Description -->
                <p class="text-base-content/80 text-lg max-w-xl">
                    En yeni ürünleri keşfedin ve en iyi fiyatlarla alışverişin tadını çıkarın.
                    Binlerce ürün arasından size en uygun olanı seçin.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg gap-2 group">
                        <span>Alışverişe Başla</span>
                        <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-ghost btn-lg gap-2">
                        <i class="fas fa-th-large"></i>
                        <span>Kategoriler</span>
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 pt-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary">{{ $products->count() }}+</div>
                        <div class="text-sm text-base-content/70">Ürün</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-secondary">{{ $brands->count() }}+</div>
                        <div class="text-sm text-base-content/70">Marka</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-accent">{{ $categories->count() }}+</div>
                        <div class="text-sm text-base-content/70">Kategori</div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Campaign Stories -->
            <div class="relative hidden lg:block">
                <!-- Floating Cards -->
                <div class="absolute inset-0">
                    @foreach ($campaigns->take(3) as $index => $campaign)
                    <div class="absolute animate-float delay-{{ $index * 200 }}"
                        style="top: {{ 20 + $index * 30 }}%; left: {{ 10 + $index * 20 }}%;">
                        <a href="{{ route('campaigns.show', $campaign->slug) }}">
                            <div class="card bg-base-100 shadow-xl w-64">
                                <div class="card-body">
                                    <div class="flex items-center gap-4">
                                        <div class="rounded-lg bg-primary/10 p-3">
                                            <i class="fas fa-gift text-2xl text-primary"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold">{{ $campaign->name }}</h3>
                                            <div class="text-sm text-base-content/70">
                                                @if ($campaign->discount_type === 'percentage')
                                                %{{ $campaign->discount_value }} İndirim
                                                @else
                                                {{ $campaign->discount_value }}₺ İndirim
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Stories Scrollbar (Mobile) -->
    <div class="lg:hidden overflow-x-auto scrollbar-thin scrollbar-thumb-primary scrollbar-track-base-200 p-4">
        <div class="flex gap-4">
            @foreach ($campaigns as $campaign)
            <a href="{{ route('campaigns.show', $campaign->slug) }}">
                <div class="card bg-base-100 shadow-lg flex-shrink-0 w-64">
                    <div class="card-body">
                        <div class="flex items-center gap-4">
                            <div class="rounded-lg bg-primary/10 p-3">
                                <i class="fas fa-gift text-2xl text-primary"></i>
                            </div>
                            <div>
                                <h3 class="font-bold">{{ $campaign->name }}</h3>
                                <div class="text-sm text-base-content/70">
                                    @if ($campaign->discount_type === 'percentage')
                                    %{{ $campaign->discount_value }} İndirim
                                    @else
                                    {{ $campaign->discount_value }}₺ İndirim
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>



<!-- Most Viewed Products Section -->
<x-most-viewed-products-component />



<!-- Best-Selling Products Section -->
<x-best-selling-products-component />



<!-- Most Favorited Products Section -->
<x-most-favorited-products-component />



<!-- Most Commented Products Section -->
<x-most-commented-products-component />



<!-- Most Sold Categories Section -->
<x-most-sold-categories-component />


<!-- Featured Products Section -->
<section class="py-8 lg:py-16">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="flex flex-col gap-4 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="space-y-2 text-center md:text-left mb-4 md:mb-0">
                    <div class="inline-flex items-center gap-2">
                        <div class="badge badge-primary p-3">
                            <i class="fas fa-fire text-lg"></i>
                        </div>
                        <h2
                            class="text-2xl md:text-4xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            Öne Çıkan Ürünler
                        </h2>
                    </div>
                    <p class="text-base-content/70">En çok tercih edilen ürünler</p>
                </div>

                <!-- Sort & Filter (Mobile) -->
                <div class="flex gap-2 md:hidden w-full">
                    <button class="btn btn-outline flex-1 btn-sm"
                        onclick="document.getElementById('filter-modal').showModal()">
                        <i class="fas fa-filter"></i> Filtrele
                    </button>
                    <button class="btn btn-outline flex-1 btn-sm"
                        onclick="document.getElementById('sort-modal').showModal()">
                        <i class="fas fa-sort"></i> Sırala
                    </button>
                </div>

                <!-- Desktop Actions -->
                <div class="hidden md:flex items-center gap-4">
                    <div class="flex gap-2">
                        <button class="btn btn-ghost btn-sm btn-circle"
                            onclick="document.getElementById('filter-modal').showModal()" title="Filtrele">
                            <i class="fas fa-filter"></i>
                        </button>
                        <button class="btn btn-ghost btn-sm btn-circle"
                            onclick="document.getElementById('sort-modal').showModal()" title="Sırala">
                            <i class="fas fa-sort"></i>
                        </button>
                    </div>

                    <div class="divider divider-horizontal"></div>

                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">
                        Tümünü Gör
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

            </div>
        </div>

        <!-- Products Grid -->
        <div
            class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 featured-products-grid">
            @foreach ($featuredProducts as $product)
            <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>

<!-- Filter Modal -->
<dialog id="filter-modal" class="modal  sm:modal-middle">
    <form method="dialog" class="modal-box">
        <h3 class="font-bold text-lg mb-4">Filtrele</h3>

        <!-- Price Range -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Fiyat Aralığı</span>
            </label>
            <div class="flex gap-2">
                <input type="number" id="min-price" placeholder="Min" class="input input-bordered w-full" />
                <input type="number" id="max-price" placeholder="Max" class="input input-bordered w-full" />
            </div>
        </div>

        <!-- Stock Status -->
        <div class="form-control mt-4">
            <label class="label">
                <span class="label-text">Stok Durumu</span>
            </label>
            <select id="stock-status" class="select select-bordered w-full">
                <option value="">Tümü</option>
                <option value="in-stock">Stokta Var</option>
                <option value="out-of-stock">Stokta Yok</option>
            </select>
        </div>

        <div class="modal-action">
            <button class="btn btn-ghost" onclick="resetFilters()">Sıfırla</button>
            <button class="btn btn-primary" onclick="applyFilters()">Uygula</button>
        </div>

        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
    </form>
    <form method="dialog" class="modal-backdrop">
        <button class="btn btn-outline">close</button>
    </form>
</dialog>

<!-- Sort Modal -->
<dialog id="sort-modal" class="modal  sm:modal-middle">
    <form method="dialog" class="modal-box">
        <h3 class="font-bold text-lg mb-4">Sırala</h3>
        <div class="flex flex-col gap-2">
            <button class="btn btn-ghost justify-start" onclick="sortProducts('price-asc')">
                <i class="fas fa-sort-amount-up-alt"></i> Fiyat (Düşükten Yükseğe)
            </button>
            <button class="btn btn-ghost justify-start" onclick="sortProducts('price-desc')">
                <i class="fas fa-sort-amount-down"></i> Fiyat (Yüksekten Düşüğe)
            </button>
            <button class="btn btn-ghost justify-start" onclick="sortProducts('name-asc')">
                <i class="fas fa-sort-alpha-down"></i> İsim (A-Z)
            </button>
            <button class="btn btn-ghost justify-start" onclick="sortProducts('name-desc')">
                <i class="fas fa-sort-alpha-up"></i> İsim (Z-A)
            </button>
        </div>

        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
    </form>
    <form method="dialog" class="modal-backdrop">
        <button class="btn btn-outline">close</button>
    </form>
</dialog>

<!-- Categories Section -->
<section class="py-12 lg:py-20">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="flex flex-col lg:flex-row justify-between items-center mb-12">
            <div class="space-y-2 text-center lg:text-left mb-6 lg:mb-0">
                <div class="flex items-center justify-center lg:justify-start gap-2">
                    <span class="badge badge-primary p-3">
                        <i class="fas fa-th-large text-lg"></i>
                    </span>
                    <h2 class="text-2xl lg:text-4xl font-bold">
                        <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            Kategoriler
                        </span>
                    </h2>
                </div>
                <p class="text-base-content/70 text-sm lg:text-base">
                    Tüm ürün kategorilerimizi keşfedin
                </p>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('categories.index') }}" class="btn btn-primary btn-sm lg:btn-md group gap-2">
                    <span>Tümünü Gör</span>
                    <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 lg:gap-6">
            @foreach ($categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}"
                class="group hover:-translate-y-1 transition-all duration-300">
                <div class="card bg-base-100 shadow-lg hover:shadow-xl border border-base-200 h-full">
                    <!-- Icon & Stats -->
                    <div class="card-body p-4 text-center relative overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-10 transition-opacity duration-300 group-hover:opacity-20">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary to-secondary opacity-20">
                            </div>
                            <div class="w-full h-full"
                                style="background-image: url('data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23000000' fill-opacity='0.1' fill-rule='evenodd'%3E%3Ccircle cx='3' cy='3' r='3'/%3E%3Ccircle cx='13' cy='13' r='3'/%3E%3C/g%3E%3C/svg%3E');">
                            </div>
                        </div>

                        <!-- Icon Container -->
                        <div class="mb-4 relative">
                            <div
                                class="w-16 h-16 mx-auto rounded-xl bg-primary/10 flex items-center justify-center
                                     group-hover:bg-primary/20 transition-all duration-300 transform group-hover:scale-110">
                                @if ($category->icon)
                                <!-- Category Icon -->
                                <div
                                    class="w-16 h-16 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center shadow-lg">
                                    <i class="fas fa-{{ $category->icon ?? 'folder' }} text-2xl text-base-100"></i>
                                </div>
                                @else
                                <i class="fas fa-folder text-2xl text-primary fa-layer-group"></i>
                                @endif
                            </div>
                        </div>

                        <!-- Category Info -->
                        <h3
                            class="font-bold text-base lg:text-lg mb-2 group-hover:text-primary transition-colors duration-300 line-clamp-1">
                            {{ $category->name }}
                        </h3>

                        <!-- Stats -->
                        <div class="flex items-center justify-center gap-2">
                            <div class="badge badge-primary gap-2">
                                <i class="fas fa-box text-xs"></i>
                                <span>{{ $category->products_count }}</span>
                            </div>
                            <span class="text-xs text-base-content/70">Ürün</span>
                        </div>

                        <!-- Hover Effect Button -->
                        <div
                            class="absolute bottom-0 left-0 w-full p-2 bg-gradient-to-t from-base-200/80 via-base-200/50 to-transparent 
                                  opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-full group-hover:translate-y-0">
                            <button class="btn btn-ghost btn-sm btn-block">
                                <i class="fas fa-eye"></i>
                                <span>İncele</span>
                            </button>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Mobile View More Button -->
        <div class="mt-6 text-center lg:hidden">
            <a href="{{ route('categories.index') }}" class="btn btn-outline btn-block">
                <i class="fas fa-th-large"></i>
                <span>Tüm Kategoriler</span>
            </a>
        </div>
    </div>
</section>

<!-- New Products Section with Carousel -->
<div class="container mx-auto px-4 mb-16">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
            Yeni Ürünler
        </h2>
        <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="btn btn-ghost btn-sm">
            Tümünü Gör →
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
        @foreach ($newProducts as $product)
        <x-product-card :product="$product" />
        @endforeach
    </div>
</div>


<!-- Brands Section -->
<section class="py-12 lg:py-20 bg-base-200/50 backdrop-blur-sm">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="flex flex-col lg:flex-row justify-between items-center mb-12">
            <div class="space-y-2 text-center lg:text-left mb-6 lg:mb-0">
                <div class="flex items-center justify-center lg:justify-start gap-2">
                    <span class="badge badge-primary p-3">
                        <i class="fas fa-building text-lg"></i>
                    </span>
                    <h2 class="text-2xl lg:text-4xl font-bold">
                        <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            Markalar
                        </span>
                    </h2>
                </div>
                <p class="text-base-content/70 text-sm lg:text-base">
                    {{ $brands->count() }}+ Marka ile Hizmetinizdeyiz
                </p>
            </div>

            <div class="flex items-center gap-4">
                <!-- View Toggle -->
                <div class="join hidden lg:flex">
                    <button class="btn btn-sm join-item" data-view="grid">
                        <i class="fas fa-grip-horizontal"></i>
                    </button>
                    <button class="btn btn-sm join-item" data-view="carousel">
                        <i class="fas fa-stream"></i>
                    </button>
                </div>

                <a href="{{ route('brands.index') }}" class="btn btn-primary btn-sm lg:btn-md group gap-2">
                    <span>Tüm Markalar</span>
                    <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- Brands Grid/Carousel -->
        <div class="relative">
            <!-- Desktop Grid View -->
            <div class="hidden lg:grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 lg:gap-6">
                @foreach ($brands->take(12) as $brand)
                <a href="{{ route('brands.show', $brand->slug) }}" class="group">
                    <div class="card bg-base-100 hover:bg-base-200 shadow-md hover:shadow-xl 
                              transition-all duration-300 hover:-translate-y-1">
                        <!-- Brand Logo -->
                        <figure class="px-6 pt-6">
                            <div class="w-full aspect-[4/3] bg-white rounded-xl flex items-center justify-center p-4
                                      group-hover:bg-base-200/50 transition-colors duration-300">
                                <img src="/{{ $brand->logo }}" alt="{{ $brand->name }}" class="max-h-20 w-auto object-contain transform transition-all duration-300 
                                            group-hover:scale-110 filter group-hover:brightness-110">
                            </div>
                        </figure>

                        <div class="card-body p-4 text-center">
                            <!-- Brand Info -->
                            <div class="space-y-2">
                                <h3 class="font-semibold text-base group-hover:text-primary transition-colors">
                                    {{ $brand->name }}
                                </h3>
                                <div class="flex items-center justify-center gap-2 text-sm">
                                    <span class="badge badge-primary badge-sm">
                                        {{ $brand->products()->count() }}
                                    </span>
                                    <span class="text-base-content/70">Ürün</span>
                                </div>
                            </div>

                            <!-- Hover Action -->
                            <div
                                class="mt-4 opacity-0 group-hover:opacity-100 transition-all duration-300 -translate-y-2 group-hover:translate-y-0">
                                <button class="btn btn-primary btn-sm btn-block gap-2">
                                    <span>Keşfet</span>
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Mobile/Tablet Carousel -->
            <div class="lg:hidden relative">
                <!-- Gradient Overlays -->
                <div class="absolute left-0 top-0 w-16 h-full bg-gradient-to-r from-base-200/50 to-transparent z-10">
                </div>
                <div class="absolute right-0 top-0 w-16 h-full bg-gradient-to-l from-base-200/50 to-transparent z-10">
                </div>

                <!-- Scrollable Container -->
                <div class="flex gap-4 overflow-x-auto snap-x snap-mandatory scrollbar-hide py-4 px-2">
                    @foreach ($brands as $brand)
                    <div class="snap-start flex-none w-48 sm:w-64">
                        <div class="card bg-base-100 shadow-md hover:shadow-xl transition-all duration-300">
                            <figure class="px-4 pt-4">
                                <div
                                    class="w-full aspect-video bg-white rounded-xl flex items-center justify-center p-4">
                                    <img src="/{{ $brand->logo }}" alt="{{ $brand->name }}"
                                        class="max-h-16 w-auto object-contain">
                                </div>
                            </figure>
                            <div class="card-body p-4 text-center">
                                <h3 class="font-semibold">{{ $brand->name }}</h3>
                                <div class="flex items-center justify-center gap-2 text-sm">
                                    <span class="badge badge-sm">{{ $brand->products()->count() }}</span>
                                    <span class="text-base-content/70">Ürün</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Mobile View More -->
        <div class="mt-8 text-center lg:hidden">
            <a href="{{ route('brands.index') }}" class="btn btn-outline btn-block">
                <i class="fas fa-building"></i>
                <span>Tüm Markaları Gör</span>
            </a>
        </div>
    </div>
</section>


<!-- Testimonials Section -->
<section class="py-16 lg:py-24 relative">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-base-200/30">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-secondary/5"></div>
        <div class="absolute inset-0"
            style="background-image: url('data:image/svg+xml,%3Csvg width=\'30\' height=\'30\' viewBox=\'0 0 30 30\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\' fill=\'rgba(0,0,0,0.07)\'/%3E%3C/svg%3E')">
        </div>
    </div>

    <div class="container mx-auto px-4 relative">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <div class="inline-flex flex-col items-center">
                <span class="badge badge-primary mb-4">
                    <i class="fas fa-comments text-lg"></i>
                </span>
                <h2 class="text-3xl lg:text-4xl font-bold mb-4">
                    Müşterilerimizin Deneyimleri
                </h2>
                <p class="text-base-content/70 max-w-xl mx-auto">
                    Müşterilerimizin bizimle ilgili düşüncelerini ve deneyimlerini keşfedin
                </p>
            </div>
        </div>

        <!-- Testimonials Grid with Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
            <!-- Stats Card -->
            <div class="lg:sticky lg:top-24 space-y-6 lg:space-y-8">
                <div class="stats stats-vertical shadow-lg bg-base-100">
                    <div class="stat">
                        <div class="stat-figure text-primary">
                            <i class="fas fa-smile-beam text-3xl"></i>
                        </div>
                        <div class="stat-title">Mutlu Müşteri</div>
                        <div class="stat-value text-primary">{{ $testimonials->count() }}+</div>
                    </div>

                    <div class="stat">
                        <div class="stat-figure text-secondary">
                            <i class="fas fa-star text-3xl"></i>
                        </div>
                        <div class="stat-title">Ortalama Puan</div>
                        <div class="stat-value text-secondary">{{ number_format($testimonials->avg('rating'), 1) }}
                        </div>
                    </div>
                </div>

                <!-- Action Card -->
            </div>

            <!-- Testimonials Cards -->
            <div class="lg:col-span-3 grid md:grid-cols-2 gap-6">
                @foreach ($testimonials as $testimonial)
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-all duration-300 group">
                    <div class="card-body">
                        <!-- Quote Icon -->
                        <div class="mb-4">
                            <i class="fas fa-quote-left text-4xl text-primary/20"></i>
                        </div>

                        <!-- Content -->
                        <div class="space-y-4">
                            <p class="text-base-content/80 text-lg leading-relaxed">
                                "{{ Str::limit($testimonial->content, 150) }}"
                            </p>

                            <!-- Rating -->
                            <div class="flex items-center gap-2">
                                <div class="rating rating-sm">
                                    @for ($i = 1; $i <= 5; $i++) <input type="radio"
                                        name="rating-{{ $testimonial->id }}" class="mask mask-star-2 bg-warning"
                                        disabled @if ($i <=$testimonial->rating) checked @endif />
                                        @endfor
                                </div>
                                <span class="text-warning font-medium">
                                    {{ number_format($testimonial->rating, 1) }}
                                </span>
                            </div>

                            <!-- Author Info -->
                            <div class="flex items-center gap-4 pt-4 border-t border-base-200">
                                <div class="avatar">
                                    <div
                                        class="w-12 h-12 rounded-full ring ring-primary ring-offset-2 ring-offset-base-100">
                                        <img src="/{{ $testimonial->avatar }}" alt="{{ $testimonial->author }}" />
                                    </div>
                                </div>
                                <div>
                                    <h3 class="font-bold">{{ $testimonial->author }}</h3>
                                    <p class="text-sm text-base-content/70">{{ $testimonial->position }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Verified Badge -->
                        <div class="absolute top-4 right-4">
                            <div class="tooltip" data-tip="Onaylı Müşteri">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Mobile View More -->
        <div class="mt-8 text-center lg:hidden">
            <button class="btn btn-outline gap-2">
                <i class="fas fa-plus"></i>
                <span>Daha Fazla Göster</span>
            </button>
        </div>
    </div>
</section>

<script>
    function applyFilters() {
            const minPrice = document.getElementById('min-price').value;
            const maxPrice = document.getElementById('max-price').value;
            const stockStatus = document.getElementById('stock-status').value;

            const products = document.querySelectorAll('.product-card');

            products.forEach(product => {
                const price = parseFloat(product.dataset.price);
                const inStock = product.dataset.stock > 0;

                let showProduct = true;

                if (minPrice && price < parseFloat(minPrice)) showProduct = false;
                if (maxPrice && price > parseFloat(maxPrice)) showProduct = false;
                if (stockStatus === 'in-stock' && !inStock) showProduct = false;
                if (stockStatus === 'out-of-stock' && inStock) showProduct = false;

                product.style.display = showProduct ? '' : 'none';
            });

            document.getElementById('filter-modal').close();
        }

        function resetFilters() {
            document.getElementById('min-price').value = '';
            document.getElementById('max-price').value = '';
            document.getElementById('stock-status').value = '';

            const products = document.querySelectorAll('.product-card');
            products.forEach(product => {
                product.style.display = '';
            });
        }

        function sortProducts(sortType) {
            // Featured products container'ını doğru şekilde seç
            const productsContainer = document.querySelector('.featured-products-grid'); // Bu class'ı ekleyelim
            const products = Array.from(productsContainer.getElementsByClassName('product-card'));

            products.sort((a, b) => {
                switch (sortType) {
                    case 'price-asc':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price-desc':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    case 'name-asc':
                        return a.dataset.name.localeCompare(b.dataset.name, 'tr');
                    case 'name-desc':
                        return b.dataset.name.localeCompare(a.dataset.name, 'tr');
                    default:
                        return 0;
                }
            });

            // Sıralanmış ürünleri container'a yerleştir
            products.forEach(product => {
                productsContainer.appendChild(product);
            });

            document.getElementById('sort-modal').close();
        }
</script>

@endsection