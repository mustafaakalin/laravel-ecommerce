@extends('layouts.app')

@section('title', 'Siparişlerim')

@section('content')
    <div class="container mx-auto py-8 px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sol Sidebar -->
            <div class="lg:col-span-1">
                <ul class="menu bg-base-100 shadow-xl rounded-box">
                    <li>
                        <a href="{{ route('profile.show') }}">
                            <i class="fa-solid fa-user"></i>
                            Profilim
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile.edit') }}">
                            <i class="fa-solid fa-user-pen"></i>
                            Profil Düzenle
                        </a>
                    </li>
                    <li class="bordered">
                        <a href="{{ route('profile.orders') }}" class="active">
                            <i class="fa-solid fa-bag-shopping"></i>
                            Siparişlerim
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile.addresses.index') }}">
                            <i class="fa-solid fa-location-dot"></i>
                            Adreslerim
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('wishlist.index') }}">
                            <i class="fa-solid fa-heart"></i>
                            Favorilerim
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Sağ Taraf -->
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-4">
                            <i class="fa-solid fa-bag-shopping"></i>
                            Siparişlerim
                        </h2>

                        @forelse ($orders as $order)
                            <div class="card bg-base-200 mb-4">
                                <div class="card-body p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <p class="font-bold">
                                                Sipariş No: <span class="text-primary">#{{ $order->id }}</span>
                                            </p>
                                            <p class="text-sm text-base-content/70">
                                                {{ $order->created_at->format('d.m.Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="badge badge-lg
                                            @php
                                                echo match ($order->status) {
                                                    'pending' => 'badge-warning',
                                                    'paid' => 'badge-info',
                                                    'shipping' => 'badge-primary',
                                                    'delivered' => 'badge-success',
                                                    'cancelled' => 'badge-error',
                                                    default => 'badge-secondary',
                                                };
                                            @endphp">
                                            @php
                                                echo match ($order->status) {
                                                    'pending' => 'Bekliyor',
                                                    'paid' => 'Ödendi',
                                                    'shipping' => 'Yolda (Kargo)',
                                                    'delivered' => 'Teslim Edildi',
                                                    'cancelled' => 'İptal Edildi',
                                                    default => ucfirst($order->status),
                                                };
                                            @endphp
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-3 text-sm">
                                        <div>
                                            <span class="text-base-content/70">Ürün Adedi:</span>
                                            <strong>{{ $order->items->sum('quantity') }}</strong>
                                        </div>
                                        <div>
                                            <span class="text-base-content/70">Ödeme:</span>
                                            <strong>{{ $order->payment_method ? ucfirst($order->payment_method) : '-' }}</strong>
                                        </div>
                                        <div>
                                            <span class="text-base-content/70">Toplam:</span>
                                            <strong class="text-primary">{{ number_format($order->total_price, 2) }} ₺</strong>
                                        </div>
                                    </div>

                                    <div class="card-actions justify-end mt-3">
                                        <a href="{{ route('profile.orders.show', $order) }}"
                                            class="btn btn-primary btn-sm">
                                            Detay Görüntüle
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>Henüz siparişiniz bulunmuyor.</span>
                            </div>
                            <div class="card-actions justify-center mt-4">
                                <a href="{{ route('products.index') }}" class="btn btn-primary">
                                    Alışverişe Başla
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
