@extends('layouts.app')

@section('title', 'Profilimi Düzenle')

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
                    <li class="menu-title">Ayarlar</li>
                    <li class="bordered">
                        <a href="{{ route('profile.edit') }}" class="active">
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
                <!-- Profil Bilgileri Formu -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-4">
                            <i class="fa-solid fa-user-pen"></i>
                            Profil Bilgileri
                        </h2>

                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Ad</span>
                                    </label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                        class="input input-bordered @error('name') input-error @enderror" required>
                                    @error('name')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Soyad</span>
                                    </label>
                                    <input type="text" name="surname" value="{{ old('surname', $user->surname) }}"
                                        class="input input-bordered @error('surname') input-error @enderror">
                                    @error('surname')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">E-posta</span>
                                    </label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                        class="input input-bordered @error('email') input-error @enderror" required>
                                    @error('email')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Telefon</span>
                                    </label>
                                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                        class="input input-bordered @error('phone') input-error @enderror">
                                    @error('phone')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Kimlik Numarası</span>
                                    </label>
                                    <input type="text" name="identity_number" value="{{ old('identity_number', $user->identity_number) }}"
                                        class="input input-bordered @error('identity_number') input-error @enderror" maxlength="11">
                                    @error('identity_number')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Profil Fotoğrafı</span>
                                    </label>
                                    <input type="file" name="avatar" accept="image/*"
                                        class="file-input file-input-bordered w-full @error('avatar') file-input-error @enderror">
                                    @error('avatar')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>
                            </div>

                            <div class="divider"></div>
                            <h3 class="font-semibold mb-4">
                                <i class="fa-solid fa-share-nodes"></i>
                                Sosyal Medya Hesapları
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Instagram</span>
                                    </label>
                                    <label class="input input-bordered flex items-center gap-2">
                                        <i class="fa-brands fa-instagram"></i>
                                        <input type="text" name="instagram_account" value="{{ old('instagram_account', $user->instagram_account) }}"
                                            class="grow" placeholder="kullanici_adi">
                                    </label>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Facebook</span>
                                    </label>
                                    <label class="input input-bordered flex items-center gap-2">
                                        <i class="fa-brands fa-facebook"></i>
                                        <input type="text" name="facebook_account" value="{{ old('facebook_account', $user->facebook_account) }}"
                                            class="grow" placeholder="kullanici_adi">
                                    </label>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">TikTok</span>
                                    </label>
                                    <label class="input input-bordered flex items-center gap-2">
                                        <i class="fa-brands fa-tiktok"></i>
                                        <input type="text" name="tiktok_account" value="{{ old('tiktok_account', $user->tiktok_account) }}"
                                            class="grow" placeholder="kullanici_adi">
                                    </label>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">X (Twitter)</span>
                                    </label>
                                    <label class="input input-bordered flex items-center gap-2">
                                        <i class="fa-brands fa-x-twitter"></i>
                                        <input type="text" name="x_account" value="{{ old('x_account', $user->x_account) }}"
                                            class="grow" placeholder="kullanici_adi">
                                    </label>
                                </div>
                            </div>

                            <div class="card-actions justify-end mt-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Şifre Değiştirme Formu -->
                <div class="card bg-base-100 shadow-xl mt-6">
                    <div class="card-body">
                        <h2 class="card-title mb-4">
                            <i class="fa-solid fa-key"></i>
                            Şifre Değiştir
                        </h2>

                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Mevcut Şifre</span>
                                    </label>
                                    <input type="password" name="current_password"
                                        class="input input-bordered @error('current_password') input-error @enderror" required>
                                    @error('current_password')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                                <div></div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Yeni Şifre</span>
                                    </label>
                                    <input type="password" name="password"
                                        class="input input-bordered @error('password') input-error @enderror" required>
                                    @error('password')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Yeni Şifre (Tekrar)</span>
                                    </label>
                                    <input type="password" name="password_confirmation"
                                        class="input input-bordered" required>
                                </div>
                            </div>

                            <div class="card-actions justify-end mt-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-key"></i>
                                    Şifreyi Güncelle
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
