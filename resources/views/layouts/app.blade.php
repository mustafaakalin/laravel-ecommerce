<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ theme: localStorage.getItem('theme') || '{{ session('theme', 'light') }}' }" 
      :data-theme="theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title', 'Anasayfa')</title>
    
    <!-- Meta Tags -->
    @include('partials._meta-tags')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    @include('partials._animations')
    @vite('resources/css/app.css')
    @livewireStyles
    @stack('styles')

    <style>
        /* Background Styles */
        .bg-blur {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image: url('{{ asset(\App\Models\SiteSetting::first()->site_logo) }}');
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            filter: blur(10px) opacity(0.3);
            transform: scale(1.1);
        }

        .site-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            backdrop-filter: blur(8px);
        }
    </style>
</head>

<body class="min-h-screen bg-base-100">
    <!-- Preloader -->
    @include('partials._preloader', [
        'siteLogo' => '/' . \App\Models\SiteSetting::first()->site_logo,
        'siteName' => \App\Models\SiteSetting::first()->site_name
    ])

    <!-- Background with Blurred Logo -->
    <div class="bg-blur"></div>

    <!-- Main Layout -->
    <div class="site-wrapper">
        @include('layouts.navbar')
        
        <!-- Alerts -->
        @include('partials._alerts')

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8 text-base-content">
            @yield('content')
            @include('layouts.footer')
        </main>
    </div>

    <!-- Floating Components -->
    <div class="floating-components">
        @auth
        @livewire('cart')
        @endauth
        @livewire('wishlist-drawer')
        @livewire('toast')
        @livewire('modal')
        @livewire('quick-view')

        @livewire('wishlist-counter')
        @livewire('cart-counter')

    </div>

    <!-- Scripts -->
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @vite('resources/js/app.js')
    @include('partials._scripts')
    @stack('scripts')
</body>
</html>