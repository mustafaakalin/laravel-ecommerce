@extends('layouts.app')

@section('title', 'Giriş Yap')

@section('content')
    <div class="container mx-auto py-8 px-4 max-w-md">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl justify-center">Giriş Yap</h2>
                <p class="text-center text-base-content/70 text-sm">Hesabınıza giriş yaparak alışverişe devam edin.</p>

                @if ($errors->any())
                    <div class="alert alert-error text-sm mt-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4 mt-4">
                    @csrf

                    <div class="form-control">
                        <label class="label" for="email">
                            <span class="label-text">E-posta</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            autofocus autocomplete="email" class="input input-bordered"
                            placeholder="ornek@eposta.com" />
                    </div>

                    <div class="form-control">
                        <label class="label" for="password">
                            <span class="label-text">Şifre</span>
                        </label>
                        <input type="password" id="password" name="password" required
                            autocomplete="current-password" class="input input-bordered" placeholder="••••••••" />
                        <label class="label">
                            <a href="{{ route('filament.admin.auth.password-reset.request') }}"
                                class="label-text-alt link link-primary">Şifremi unuttum</a>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="cursor-pointer label">
                            <span class="label-text">Beni hatırla</span>
                            <input type="checkbox" name="remember" class="checkbox checkbox-primary" />
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fa-solid fa-sign-in-alt"></i>
                        Giriş Yap
                    </button>
                </form>

                <div class="divider">veya</div>

                <p class="text-center text-sm text-base-content/70">
                    Hesabınız yok mu?
                    <a href="{{ route('register') }}" class="link link-primary font-medium">Kayıt Ol</a>
                </p>
            </div>
        </div>
    </div>
@endsection
