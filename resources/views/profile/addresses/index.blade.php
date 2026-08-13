@extends('layouts.app')

@section('title', 'Adreslerim')

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
                    <li>
                        <a href="{{ route('profile.orders') }}">
                            <i class="fa-solid fa-bag-shopping"></i>
                            Siparişlerim
                        </a>
                    </li>
                    <li class="bordered">
                        <a href="{{ route('profile.addresses.index') }}" class="active">
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
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="card-title">
                                <i class="fa-solid fa-location-dot"></i>
                                Adreslerim
                            </h2>
                            @if ($addresses->count() < 4)
                                <a href="{{ route('profile.addresses.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-plus"></i>
                                    Yeni Adres
                                </a>
                            @endif
                        </div>

                        <p class="text-sm text-base-content/70 mb-4">
                            Toplam {{ $addresses->count() }}/4 adres kullanabilirsiniz.
                        </p>

                        @forelse ($addresses as $address)
                            <div class="card bg-base-200 mb-4 @if ($address->is_default) border-2 border-primary @endif">
                                <div class="card-body p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="font-bold">{{ $address->first_name }} {{ $address->last_name }}</p>
                                                @if ($address->is_default)
                                                    <span class="badge badge-primary badge-sm">Varsayılan</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-base-content/70">{{ $address->phone }}</p>
                                        </div>

                                        @php
                                            $titleLabels = [
                                                'home' => 'Ev Adresi',
                                                'work' => 'İş Adresi',
                                                'summer_house' => 'Yazlık',
                                                'family' => 'Aile Evi',
                                                'other' => 'Diğer',
                                            ];
                                        @endphp
                                        @if ($address->title)
                                            <span class="badge badge-outline badge-sm">
                                                {{ $titleLabels[$address->title] ?? $address->title }}
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-sm mt-2">
                                        {{ $address->address }}<br>
                                        {{ $address->state }}, {{ $address->city }}<br>
                                        {{ $address->zip_code }} {{ $address->country }}
                                    </p>

                                    @if ($address->company_name)
                                        <p class="text-sm text-base-content/70 mt-1">
                                            Firma: {{ $address->company_name }}
                                        </p>
                                    @endif

                                    <div class="card-actions justify-end mt-3">
                                        @if (! $address->is_default)
                                            <form action="{{ route('profile.addresses.default', $address) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-ghost btn-sm">
                                                    <i class="fa-solid fa-star"></i>
                                                    Varsayılan Yap
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('profile.addresses.edit', $address) }}"
                                            class="btn btn-ghost btn-sm">
                                            <i class="fa-solid fa-pen"></i>
                                            Düzenle
                                        </a>
                                        <form action="{{ route('profile.addresses.destroy', $address) }}" method="POST"
                                            onsubmit="return confirm('Bu adresi silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-sm text-error">
                                                <i class="fa-solid fa-trash"></i>
                                                Sil
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>Henüz adresiniz bulunmuyor.</span>
                            </div>
                            <div class="card-actions justify-center mt-4">
                                <a href="{{ route('profile.addresses.create') }}" class="btn btn-primary">
                                    İlk Adresini Ekle
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
