@extends('layouts.app')

@section('title', 'Favorilerim')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
        Favorilerim
    </h1>

    @if($likedProducts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($likedProducts as $product)
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all hover:-translate-y-1">
                    <figure class="relative group">
                        <img src="{{ $product->images->first()?->image_path ?? asset('images/default_product_image.jpg') }}"
                            alt="{{ $product->name }}"
                            class="h-48 w-full object-cover transition-transform group-hover:scale-110" />
                        @if($product->old_price)
                            <div class="absolute top-2 right-2 badge badge-secondary">İNDİRİM</div>
                        @endif
                    </figure>
                    <div class="card-body">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="card-title text-lg hover:text-primary transition-colors">
                                    <a href="{{ route('products.show', $product->slug) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <p class="text-sm text-base-content/70">{{ $product->category->name }}</p>
                            </div>
                            <div class="flex flex-col items-end">
                                @if($product->old_price)
                                    <span class="text-sm line-through text-base-content/50">
                                        {{ number_format($product->old_price, 2) }} ₺
                                    </span>
                                @endif
                                <span class="text-xl font-bold text-primary">
                                    {{ number_format($product->getCurrentPrice(), 2) }} ₺
                                </span>
                            </div>
                        </div>
                        <div class="card-actions justify-end mt-4">
                            <div class="flex gap-2 items-center">
                                @livewire('toggle-wishlist', ['product' => $product], key('wishlist-'.$product->id))
                                @livewire('add-to-cart', ['product' => $product], key('add-to-cart-'.$product->id))
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Favori Listeniz Boş</h3>
            <p class="text-base-content/70 mb-6">Henüz favori ürününüz bulunmuyor.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                Ürünleri Keşfet
            </a>
        </div>
    @endif
</div>
@endsection