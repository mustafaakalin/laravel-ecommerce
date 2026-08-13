<!-- Header -->
<header
    class="sticky top-0 z-20 bg-base-100/95 text-base-content backdrop-blur supports-[backdrop-filter]:bg-base-100/60 shadow-sm">
    <div class="container mx-auto px-4">
        <nav class="navbar min-h-[4rem]">
            <!-- Sol Kısım -->
            <div class="navbar-start">
                <div class="dropdown">
                    <label tabindex="0" class="btn btn-ghost btn-circle lg:hidden">
                        <i class="fas fa-bars h-5 w-5"></i>
                    </label>
                    <ul tabindex="0"
                        class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow-lg bg-base-100 rounded-box w-52 gap-1">
                        <li>
                            <a href="" class="font-medium hover:bg-base-200 active:bg-base-300">
                                Anasayfa
                            </a>
                        </li>
                        <li>
                            <a href="" class="font-medium hover:bg-base-200 active:bg-base-300">
                                Hakkımızda
                            </a>
                        </li>
                        <li>
                            <a href="" class="font-medium hover:bg-base-200 active:bg-base-300">
                                İletişim
                            </a>
                        </li>
                        <li>
                            <div class="tooltip" data-tip="Sıkça sorulan sorular">
                                <a href="" class="font-medium hover:bg-base-200 active:bg-base-300">
                                    SSS
                                </a>
                            </div>
                        </li>
                        <li>
                            <a href="{{ url('/products') }}" class="font-medium hover:bg-base-200 active:bg-base-300">
                                Ürünler
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tooltip tooltip-bottom" data-tip="{{ \App\Models\SiteSetting::first()->site_name }}">
                    <a href="{{ route('home') }}" class="btn btn-ghost normal-case px-2 sm:px-4">
                        <img src="/{{ \App\Models\SiteSetting::first()->site_logo }}" alt="Logo" class="w-auto h-24">
                        <span class="hidden sm:inline text-xl truncate">{{ \App\Models\SiteSetting::first()->site_name }}</span>
                    </a>
                </div>
            </div>

            <!-- Orta Kısım -->
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal gap-1">
                    <li>
                        <a href="" class="btn btn-ghost btn-sm rounded-lg">
                            Anasayfa
                        </a>
                    </li>
                    <li>
                        <a href="" class="btn btn-ghost btn-sm rounded-lg">
                            Hakkımızda
                        </a>
                    </li>
                    <li>
                        <a href="" class="btn btn-ghost btn-sm rounded-lg">
                            İletişim
                        </a>
                    </li>
                    <li>
                        <a href="" class="btn btn-ghost btn-sm rounded-lg">
                            <div class="tooltip" data-tip="Sıkça sorulan sorular">
                                SSS
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="/products" class="btn btn-ghost btn-sm rounded-lg">
                            Ürünler
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Sağ Kısım -->
            <div class="navbar-end gap-2">
                <!-- Tema Seçici -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle">
                        <i class="fas fa-palette h-5 w-5"></i>
                    </label>
                    <ul tabindex="0"
                        class="dropdown-content z-[1] p-2 shadow-lg bg-base-100 rounded-box w-52 max-h-[80vh] overflow-y-auto">
                        <li>
                            <div class="font-bold text-sm opacity-70 px-4 py-2">Temalar</div>
                        </li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Light"
                                value="light" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Dark"
                                value="dark" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Cupcake" value="cupcake" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Bumblebee" value="bumblebee" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Emerald" value="emerald" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Corporate" value="corporate" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Synthwave" value="synthwave" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Retro"
                                value="retro" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Cyberpunk" value="cyberpunk" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Valentine" value="valentine" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Halloween" value="halloween" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Garden" value="garden" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Forest" value="forest" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Aqua"
                                value="aqua" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Lofi"
                                value="lofi" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Pastel" value="pastel" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Fantasy" value="fantasy" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Wireframe" value="wireframe" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Black"
                                value="black" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Luxury" value="luxury" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Dracula" value="dracula" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="CMYK"
                                value="cmyk" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Autumn" value="autumn" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Business" value="business" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Acid"
                                value="acid" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Lemonade" value="lemonade" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Night"
                                value="night" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Coffee" value="coffee" /></li>
                        <li><input type="radio" name="theme-dropdown"
                                class="theme-controller btn btn-sm btn-block btn-ghost justify-start"
                                aria-label="Winter" value="winter" /></li>
                    </ul>
                </div>

                <!-- Arama -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle lg:hidden">
                        <i class="fas fa-search h-5 w-5"></i>
                    </label>
                    <div tabindex="0" class="dropdown-content z-[1] w-72 p-2 shadow-lg bg-base-100 rounded-box">
                        <form action="{{ route('products.index') }}" method="GET" class="form-control">
                            <input type="text" name="search" placeholder="Ara..." class="input input-bordered w-full"
                                value="{{ request('search') }}">
                        </form>
                    </div>
                </div>

                <div class="hidden lg:block form-control">
                    <form action="{{ route('products.index') }}" method="GET">
                        <input type="text" name="search" placeholder="Ara..."
                            class="input input-bordered input-sm w-40 md:w-auto" value="{{ request('search') }}">
                    </form>
                </div>

                @auth
                <!-- Favori Sayacı -->
                <div class="tooltip tooltip-bottom">
                    @livewire('wishlist-counter')
                </div>
                <!-- Sepet Sayacı -->
                <div class="tooltip tooltip-bottom" data-tip="Sepetim">
                    <livewire:cart-counter />
                </div>

                <!-- Profil Dropdown -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle avatar online">
                        <div class="w-10 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                            <img src="{{ Auth::user()->avatar }}" alt="avatar" />
                        </div>
                    </label>
                    <ul tabindex="0"
                        class="dropdown-content menu menu-sm z-[1] p-2 shadow-lg bg-base-100 rounded-box w-52 gap-1">
                        <li>
                            <a href="{{ route('profile.show') }}" class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profilim
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('filament.admin.resources.orders.index') }}"
                                class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                Siparişlerim
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                Favorilerim
                            </a>
                        </li>
                        <div class="divider my-1"></div>
                        <li>
                            <form method="POST" action="{{ route('filament.admin.auth.logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 text-error">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Çıkış Yap
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @else
                <a href="{{ route('filament.admin.auth.login') }}" class="btn btn-ghost btn-sm">Giriş Yap</a>
                <a href="{{ route('filament.admin.auth.register') }}" class="btn btn-primary btn-sm">Kayıt Ol</a>
                @endauth
            </div>
        </nav>
    </div>
</header>