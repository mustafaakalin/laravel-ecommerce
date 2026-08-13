@extends('layouts.app')

@section('title', 'Active Campaigns')


@section('content')
    <div class="container mx-auto px-4 py-8">

        <!-- breadcrumbs -->
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
                    <i class="fas fa-bullhorn text-xs sm:text-sm md:text-base mr-1 sm:mr-2 text-primary"></i>
                    <span class="text-xs sm:text-sm md:text-base">Kampanyalar</span>
                </li>
            </ul>
        </div>


        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-base-content mb-4">Aktif Kampanyalar</h1>
            <p class="text-info-content">En son tekliflerimizi ve özel fırsatlarımızı keşfedin</p>
        </div>

        <!-- Campaigns Grid Section -->
        <section class="py-8 px-4 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($campaigns as $campaign)
                    <div class="group relative">
                        <div
                            class="card hover:bg-transparent bg-gradient-to-t from-primary/10 to-secondary/10  shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden backdrop-blur-lg border border-base-200">
                            @if ($campaign->products->first()?->images->first())
                                <figure class="relative h-48 overflow-hidden">
                                    <!-- Background Image with Gradient Overlay -->
                                    <img src="{{ asset('storage/' . $campaign->products->first()->images->first()->image_path) }}"
                                        alt="{{ $campaign->name }}"
                                        class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">

                                    <!-- Gradient Overlay -->
                                    <div
                                        class="absolute inset-0 backdrop-blur-sm bg-gradient-to-t from-base-100/80 to-transparent">
                                    </div>

                                </figure>
                            @endif

                            <!-- Discount Badge -->
                            <div class="absolute top-4 right-4 badge badge-secondary backdrop-blur-sm hover:bg-transparentc bg-gradient-to-r from-teal-400 to-blue-500  bg-opacity-80 badge-lg gap-2 shadow-lg">
                                @if ($campaign->discount_type === 'percentage')
                                    <i class="fas fa-percent text-sm"></i>
                                    <span class="font-bold">{{ $campaign->discount_value }}% İNDİRİM</span>
                                @else
                                    <i class="fas fa-tag text-sm"></i>
                                    <span class="font-bold">₺&nbsp;{{ number_format($campaign->discount_value, 2) }} İNDİRİM</span>
                                @endif
                            </div>


                            <div class="card-body p-6">
                                <!-- Campaign Title -->
                                <h2
                                    class="card-title text-xl font-bold text-base-content group-hover:text-primary transition-colors duration-300">
                                    {{ $campaign->name }}
                                </h2>

                                <!-- Campaign Description -->
                                <p class="text-sm mt-2 line-clamp-2">
                                    {{ $campaign->description }}
                                </p>

                                <!-- Campaign Meta Information -->
                                <div class="flex flex-col gap-3 mt-4">
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center gap-2 text-sm ">
                                            <i class="far fa-calendar-alt"></i>
                                            <span>{{ $campaign->start_date->format('M d') }} -
                                                {{ $campaign->end_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm ">
                                            <i class="fas fa-gift"></i>
                                            <span>{{ $campaign->products_count }} Ürünler</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="card-actions justify-end mt-4">
                                    <a href="{{ route('campaigns.show', $campaign->slug) }}"
                                        class="btn btn-primary btn-sm gap-2 normal-case group-hover:btn-secondary transition-colors duration-300">
                                        Kampanyayı Keşfet
                                        <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-16 text-base-content/50">
                        <i class="fas fa-store-alt-slash text-5xl mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2">Aktif Kampanya Yok</h3>
                        <p class="text-sm">Yeni heyecan verici teklifler için daha sonra tekrar kontrol edin!</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $campaigns->links() }}
        </div>
    </div>
@endsection
