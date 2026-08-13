@extends('layouts.app')

@section('title', 'Profilim')

@section('content')
    <div class="container mx-auto py-8 px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sol Sidebar - Kullanıcı Bilgileri -->
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body items-center text-center">
                        <div class="avatar online placeholder">
                            <div class="w-32 rounded-full bg-neutral text-neutral-content ring ring-primary ring-offset-2">
                                @if (isset($user->avatar))
                                    <img src="/storage/{{ $user->avatar }}" alt="{{ $user->full_name }}" />
                                @else
                                    <span
                                        class="text-3xl">{{ substr($user->name, 0, 1) }}{{ substr($user->surname, 0, 1) }}</span>
                                @endif
                            </div>
                        </div>
                        <h2 class="card-title text-2xl">{{ $user->name }}</h2>
                        <p class="text-base-content/70">Üyelik Tarihi: {{ $user->created_at->format('d.m.Y') }}</p>

                        <div class="flex items-center justify-center gap-4 mt-4">
                            @if ($user->instagram_account)
                                <a href="https://instagram.com/{{ $user->instagram_account }}" target="_blank"
                                    class="btn btn-circle btn-ghost btn-sm hover:text-pink-500 tooltip"
                                    data-tip="Instagram">
                                    <i class="fa-brands fa-instagram fa-2xl"></i>
                                </a>
                            @endif

                            @if ($user->facebook_account)
                                <a href="https://facebook.com/{{ $user->facebook_account }}" target="_blank"
                                    class="btn btn-circle btn-ghost btn-sm hover:text-blue-600 tooltip" data-tip="Facebook">
                                    <i class="fa-brands fa-facebook fa-2xl"></i>
                                </a>
                            @endif

                            @if ($user->tiktok_account)
                                <a href="https://tiktok.com/@ {{ $user->tiktok_account }}" target="_blank"
                                    class="btn btn-circle btn-ghost btn-sm hover:text-black tooltip" data-tip="TikTok">
                                    <i class="fa-brands fa-tiktok fa-2xl"></i>
                                </a>
                            @endif

                            @if ($user->x_account)
                                <a href="https://x.com/{{ $user->x_account }}" target="_blank"
                                    class="btn btn-circle btn-ghost btn-sm hover:text-neutral-900 tooltip"
                                    data-tip="X (Twitter)">

                                    <i class="fa-brands fa-x-twitter fa-2xl"></i>
                                </a>
                            @endif
                        </div>


                        <div class="stats shadow mt-4">
                            <div class="stat place-items-center">
                                <div class="stat-title">Siparişler</div>
                                <div class="stat-value">{{ $user->orders->count() }}</div>
                            </div>
                            <div class="stat place-items-center">
                                <div class="stat-title">Favoriler</div>
                                <div class="stat-value">{{ $user->likes->count() }}</div>
                            </div>
                            <div class="stat place-items-center">
                                <div class="stat-title">Yorumlar</div>
                                <div class="stat-value">{{ $user->comments->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hızlı Erişim Menüsü -->
                <ul class="menu bg-base-100 shadow-xl rounded-box mt-4">
                    <li>
                        <a href="{{ route('filament.admin.resources.orders.index') }}" class="flex justify-between">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-bag-shopping"></i>
                                Siparişlerim
                            </span>
                            <span class="badge badge-primary">{{ $user->orders->count() }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('wishlist.index') }}" class="flex justify-between">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-heart"></i>
                                Favorilerim
                            </span>
                            <span class="badge badge-primary">{{ $user->likes->count() }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('filament.admin.resources.addresses.index') }}" class="flex justify-between">
                            <span class="flex items-center gap-2">
                                <i class="fa-solid fa-location-dot"></i>
                                Adreslerim
                            </span>
                            <span class="badge badge-primary">{{ $user->addresses->count() }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Sağ Taraf - Detaylı Bilgiler -->
            <div class="lg:col-span-2">
                <!-- Kişisel Bilgiler -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title flex items-center gap-2 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Kişisel Bilgiler
                            <button class="btn btn-ghost btn-circle btn-sm ml-auto" onclick="editProfile.showModal()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if (isset($user->name))
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Ad</span>
                                    </label>
                                    <input type="text" value="{{ $user->name }}" class="input input-bordered"
                                        readonly>
                                </div>
                            @endif

                            @if (isset($user->surname))
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Soyad</span>
                                    </label>
                                    <input type="text" value="{{ $user->surname }}" class="input input-bordered"
                                        readonly>
                                </div>
                            @endif

                            @if (isset($user->email))
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">E-posta</span>
                                    </label>
                                    <input type="email" value="{{ $user->email }}" class="input input-bordered"
                                        readonly>
                                </div>
                            @endif

                            @if (isset($user->phone))
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Telefon</span>
                                    </label>
                                    <input type="tel" value="{{ $user->phone }}" class="input input-bordered"
                                        readonly>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                @if (isset($user->address))
                    <!-- Adres Bilgileri -->
                    <div class="card bg-base-100 shadow-xl mt-6">
                        <div class="card-body">
                            <h2 class="card-title flex items-center gap-2 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Varsayılan Adres
                                <button class="btn btn-ghost btn-circle btn-sm ml-auto" onclick="editAddress.showModal()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Adres</span>
                                    </label>
                                    <textarea class="textarea textarea-bordered h-24" readonly>{{ $user->address }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 gap-4">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text">Şehir</span>
                                        </label>
                                        <input type="text" value="{{ $user->city }}" class="input input-bordered"
                                            readonly>
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text">Posta Kodu</span>
                                        </label>
                                        <input type="text" value="{{ $user->zip_code }}" class="input input-bordered"
                                            readonly>
                                    </div>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">İlçe/Eyalet</span>
                                    </label>
                                    <input type="text" value="{{ $user->state }}" class="input input-bordered"
                                        readonly>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Ülke</span>
                                    </label>
                                    <input type="text" value="{{ $user->country }}" class="input input-bordered"
                                        readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($user->orders->count() > 0)
                    <!-- Son Siparişler -->
                    <div class="card bg-base-100 shadow-xl mt-6">
                        <div class="card-body">
                            <h2 class="card-title flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                Son Siparişlerim
                            </h2>

                            <div class="overflow-x-auto">
                                <table class="table table-zebra">
                                    <thead>
                                        <tr>
                                            <th>Sipariş No</th>
                                            <th>Tarih</th>
                                            <th>Tutar</th>
                                            <th>Durum</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user->orders->take(5) as $order)
                                            <tr>
                                                <td>#{{ $order->id }}</td>
                                                <td>{{ $order->created_at->format('d.m.Y') }}</td>
                                                <td>{{ number_format($order->total_price, 2) }} ₺</td>
                                                <td>
                                                    <div class="badge badge-{{ 
                                                        $order->status === 'completed' ? 'success' : 
                                                        ($order->status === 'pending' ? 'warning' : 
                                                        ($order->status === 'shipping' ? 'info' : 'secondary')) 
                                                    }}">
                                                        {{ $order->status }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('filament.admin.resources.orders.view', $order) }}"
                                                        class="btn btn-ghost btn-xs">Detay</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-actions justify-end mt-4">
                                <a href="{{ route('filament.admin.resources.orders.index') }}"
                                    class="btn btn-primary btn-sm">
                                    Tüm Siparişlerim
                                </a>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Henüz hiç sipariş verme işlemi yapmadınız.</span>
                            </div>
                        </div>
                    </div>

                @endif
            </div>
        </div>
    </div>
@endsection
