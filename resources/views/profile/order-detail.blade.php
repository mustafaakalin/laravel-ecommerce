@extends('layouts.app')

@section('title', 'Sipariş Detayı')

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
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold">
                        Sipariş #{{ $order->id }}
                    </h1>
                    <a href="{{ route('profile.orders') }}" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-arrow-left"></i>
                        Siparişlerime Dön
                    </a>
                </div>

                <!-- Sipariş Özeti -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="stat bg-base-100 shadow rounded-box">
                        <div class="stat-title">Sipariş Tarihi</div>
                        <div class="stat-value text-xl">{{ $order->created_at->format('d.m.Y') }}</div>
                    </div>
                    <div class="stat bg-base-100 shadow rounded-box">
                        <div class="stat-title">Durum</div>
                        <div class="stat-value text-xl">
                            <span class="badge badge-lg
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
                            </span>
                        </div>
                    </div>
                    <div class="stat bg-base-100 shadow rounded-box">
                        <div class="stat-title">Toplam Tutar</div>
                        <div class="stat-value text-xl text-primary">{{ number_format($order->total_price, 2) }} ₺</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ürünler -->
                    <div class="card bg-base-100 shadow-xl md:col-span-2">
                        <div class="card-body">
                            <h2 class="card-title mb-4">
                                <i class="fa-solid fa-box"></i>
                                Siparişteki Ürünler
                            </h2>

                            <div class="overflow-x-auto">
                                <table class="table table-zebra">
                                    <thead>
                                        <tr>
                                            <th>Ürün</th>
                                            <th>Adet</th>
                                            <th>Birim Fiyat</th>
                                            <th>Ara Toplam</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->items as $item)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('products.show', $item->product->slug) }}"
                                                        class="link link-primary">
                                                        {{ $item->product->name }}
                                                    </a>
                                                </td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format($item->price, 2) }} ₺</td>
                                                <td>{{ number_format($item->price * $item->quantity, 2) }} ₺</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-right">Toplam</th>
                                            <th>{{ number_format($order->total_price, 2) }} ₺</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Teslimat Adresi -->
                    @if ($order->address)
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h2 class="card-title mb-4">
                                    <i class="fa-solid fa-location-dot"></i>
                                    Teslimat Adresi
                                </h2>

                                <p class="font-semibold">{{ $order->address->first_name }} {{ $order->address->last_name }}</p>
                                @if ($order->address->phone)
                                    <p class="text-sm text-base-content/70">{{ $order->address->phone }}</p>
                                @endif
                                <p class="text-sm mt-2">
                                    {{ $order->address->address }}<br>
                                    {{ $order->address->state }}, {{ $order->address->city }}<br>
                                    {{ $order->address->zip_code }} {{ $order->address->country }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Ödeme Bilgileri -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title mb-4">
                                <i class="fa-solid fa-credit-card"></i>
                                Ödeme Bilgileri
                            </h2>

                            <div class="flex flex-col gap-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-base-content/70">Ödeme Yöntemi</span>
                                    <strong>{{ $order->payment_method ? ucfirst($order->payment_method) : '-' }}</strong>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-base-content/70">Ödeme ID</span>
                                    <strong>{{ $order->payment_id ?? '-' }}</strong>
                                </div>
                                <div class="divider my-1"></div>
                                <div class="flex justify-between text-base">
                                    <span class="text-base-content/70">Toplam</span>
                                    <strong class="text-primary">{{ number_format($order->total_price, 2) }} ₺</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
