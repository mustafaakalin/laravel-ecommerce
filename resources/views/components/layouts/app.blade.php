<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title', 'Anasayfa')</title>
    @php
    $categories = App\Models\Category::whereNull('parent_id')
    ->where('is_active', true)
    ->withCount('products')
    ->get();
    @endphp
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>

<body class="min-h-screen bg-base-100">


    <!-- Feedback toast -->
    <livewire:toast />

    <!-- Sepet Drawer'ı -->
    <livewire:cart />

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8 text-base-content">
        <!-- Navbar -->
        @include('layouts.navbar')




        @yield('content')



        @include('layouts.footer')
    </main>


    @livewireScripts
    {{-- // Flash mesajları için otomatik kapanma --}}
    <script>
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.display = 'none';
            });
        }, 3000);
    </script>


    @if(session()->has('success'))
    <div class="alert alert-success mb-4 z-30">
        {{ session('success') }}
    </div>
    @endif

    @if(session()->has('error'))
    <div class="alert alert-error mb-4 z-30">
        {{ session('error') }}
    </div>
    @endif

    <!-- Success and Error Modals (unchanged) -->
    @if(session('success'))
    <div id="successModal" class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg text-green-600">Success!</h3>
            <p class="py-4">{{ session('success') }}</p>
            <div class="modal-action">
                <button onclick="closeModal('successModal')" class="btn btn-primary">Close</button>
            </div>
        </div>
    </div>
    @endif

    @if(session('warning'))
    <div id="warningModal" class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg text-yellow-600">Warning!</h3>
            <p class="py-4">{{ session('warning') }}</p>
            <div class="modal-action">
                <button onclick="closeModal('warningModal')" class="btn btn-primary">Close</button>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div id="errorModal" class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg text-red-600">Error!</h3>
            <p class="py-4">{{ session('error') }}</p>
            <div class="modal-action">
                <button onclick="closeModal('errorModal')" class="btn btn-primary">Close</button>
            </div>
        </div>
    </div>
    @endif

    <script>
        // Modal close function
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('modal-open');
    }

    // Automatically close the modal after a few seconds
    window.addEventListener('DOMContentLoaded', (event) => {
        const successModal = document.getElementById('successModal');
        const errorModal = document.getElementById('errorModal');

        if (successModal) {
            setTimeout(() => closeModal('successModal'), 3000); // Closes after 3 seconds
        }
        
        if (warningModal) {
            setTimeout(() => closeModal('warningModal'), 3000); // Closes after 3 seconds
        }
        
        if (errorModal) {
            setTimeout(() => closeModal('errorModal'), 3000); // Closes after 3 seconds
        }
    });
    </script>


    <!-- Theme Switcher (unchanged) -->

    <script>
        // LocalStorage'de kayıtlı tema varsa onu uygula
        document.addEventListener('DOMContentLoaded', function () {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
                const radios = document.querySelectorAll('.theme-controller');
                radios.forEach(radio => {
                    if (radio.value === savedTheme) {
                        radio.checked = true;
                    }
                });
            }

            // Tema değiştiğinde localStorage'a kaydet
            const radios = document.querySelectorAll('.theme-controller');
            radios.forEach(radio => {
                radio.addEventListener('change', function () {
                    const selectedTheme = this.value;
                    document.documentElement.setAttribute('data-theme', selectedTheme);
                    localStorage.setItem('theme', selectedTheme);
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- ... içerik ... -->
    @stack('scripts')
    @livewire('wishlist-drawer')
</body>

</html>