@extends('layouts.app')

@section('title', 'Kayıt Ol')

@section('content')
    <div class="container mx-auto py-8 px-4 max-w-md">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl justify-center">Kayıt Ol</h2>
                <p class="text-center text-base-content/70 text-sm">Yeni bir hesap oluşturun ve alışverişe başlayın.</p>

                @if ($errors->any())
                    <div class="alert alert-error text-sm mt-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-4 mt-4">
                    @csrf

                    <div class="form-control">
                        <label class="label" for="name">
                            <span class="label-text">Ad Soyad</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            autofocus autocomplete="name" class="input input-bordered" placeholder="Adınız Soyadınız" />
                    </div>

                    <div class="form-control">
                        <label class="label" for="email">
                            <span class="label-text">E-posta</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            autocomplete="email" class="input input-bordered" placeholder="ornek@eposta.com" />
                    </div>

                    <div class="form-control">
                        <label class="label" for="password">
                            <span class="label-text">Şifre</span>
                        </label>
                        <input type="password" id="password" name="password" required autocomplete="new-password"
                            class="input input-bordered" placeholder="••••••••" />
                    </div>

                    <div class="form-control">
                        <label class="label" for="password_confirmation">
                            <span class="label-text">Şifre (Tekrar)</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            autocomplete="new-password" class="input input-bordered" placeholder="••••••••" />
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fa-solid fa-user-plus"></i>
                        Kayıt Ol
                    </button>
                </form>

                <div class="divider">veya</div>

                <p class="text-center text-sm text-base-content/70">
                    Zaten hesabınız var mı?
                    <a href="{{ route('login') }}" class="link link-primary font-medium">Giriş Yap</a>
                </p>
            </div>
        </div>
    </div>
@endsection
