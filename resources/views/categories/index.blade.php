@extends('layouts.app')
@section('title', 'Kategoriler')

@section('content')
    <div class="container mx-auto p-4 space-y-8">



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
                    <i class="fas fa-tag text-xs sm:text-sm md:text-base mr-1 sm:mr-2 text-primary"></i>
                    <span class="text-xs sm:text-sm md:text-base truncate max-w-[150px] sm:max-w-[200px] md:max-w-none">
                        Kategoriler
                    </span>
                </li>
            </ul>
        </div>


        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-center bg-base-100 rounded-2xl p-6 shadow-lg">
            <div class="space-y-2">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                    Kategoriler
                </h1>
                <p class="text-base-content/70">Toplam {{ $categories->count() }} kategori bulundu</p>
            </div>

            <!-- Stats -->
            <div class="stats shadow">
                <div class="stat place-items-center">
                    <div class="stat-title">Toplam Ürün</div>
                    <div class="stat-value text-primary">{{ $categories->sum('products_count') }}</div>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($categories as $category)
                <div
                    class="card backdrop-blur-sm hover:bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 group">
                    <div class="card-body">
                        <!-- Category Header -->
                        <div class="flex items-center gap-4 mb-4">
                            @if ($category->icon)
                                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
                                    <i class="{{ $category->icon }} text-2xl text-primary"></i>
                                </div>
                            @endif
                            <div>
                                <h2 class="card-title text-xl group-hover:text-primary transition-colors">
                                    {{ $category->name }}
                                </h2>
                                @if ($category->parent)
                                    <div class="text-sm text-base-content/60">
                                        <a href="{{ route('categories.show', $category->parent->slug) }}"
                                            class="link link-hover">
                                            {{ $category->parent->name }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        @if ($category->description)
                            <p class="text-base-content/70 line-clamp-2">{{ $category->description }}</p>
                        @endif

                        <!-- Statistics -->
                        <div class="flex flex-wrap gap-2 my-4">
                            <div class="badge badge-primary gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                {{ $category->products_count }} Ürün
                            </div>
                            @if ($category->children->count() > 0)
                                <div class="badge badge-secondary gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    {{ $category->children->count() }} Alt Kategori
                                </div>
                            @endif
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

                        <!-- Actions -->
                        <div class="card-actions justify-end mt-auto pt-4 border-t border-base-200">
                            <a href="{{ route('categories.show', $category->slug) }}"
                                class="btn btn-primary btn-sm gap-2 normal-case">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                Ürünleri Gör
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty State -->
        @if ($categories->isEmpty())
            <div class="hero min-h-[400px] bg-base-200 rounded-2xl">
                <div class="hero-content text-center">
                    <div class="max-w-md">
                        <div class="mb-8 text-8xl">🏷️</div>
                        <h2 class="text-2xl font-bold mb-4">Henüz Kategori Bulunmuyor</h2>
                        <p class="mb-6 text-base-content/70">Kategoriler yakında eklenecektir.</p>
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
        @endif
    </div>

    @push('styles')
        <style>
            /* Optional: Add smooth hover transitions */
            .card {
                transition: all 0.3s ease;
            }

            .card:hover {
                transform: translateY(-2px);
            }
        </style>
    @endpush
@endsection
