<header class="sticky top-0 z-20 bg-base-100/80 backdrop-blur-lg shadow-sm">
    <nav class="navbar container mx-auto">
        <!-- Mobile Menu & Brand -->
        <div class="navbar-start">
            <div class="dropdown lg:hidden">
                <label tabindex="0" class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-bars text-lg"></i>
                </label>
                <ul class="dropdown-content menu menu-sm mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a href="/"><i class="fa-solid fa-home"></i>Anasayfa</a></li>
                    <li><a href="/products"><i class="fa-solid fa-box"></i>Ürünler</a></li>
                    <li><a href="/about"><i class="fa-solid fa-info-circle"></i>Hakkımızda</a></li>
                    <li><a href="/contact"><i class="fa-solid fa-envelope"></i>İletişim</a></li>
                    <li><a href="/faq"><i class="fa-solid fa-question-circle"></i>SSS</a></li>
                </ul>
            </div>
            <a href="{{ route('home') }}" class="btn btn-ghost text-xl normal-case">
                <span class="hidden md:inline">{{ \App\Models\SiteSetting::first()->site_name }}</span>
                <span class="md:hidden">{{ Str::limit(\App\Models\SiteSetting::first()->site_name, 10) }}</span>
            </a>
        </div>

        <!-- Desktop Menu -->
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal gap-2">
                <li><a href="/"><i class="fa-solid fa-home"></i>Anasayfa</a></li>
                <li><a href="/products"><i class="fa-solid fa-box"></i>Ürünler</a></li>
                <li class="dropdown dropdown-hover">
                    <a tabindex="0">
                        <i class="fa-solid fa-tags"></i>
                        Kategoriler
                    </a>
                    <ul class="dropdown-content menu shadow bg-base-100 rounded-box w-52 max-h-[70vh] overflow-y-auto ">
                        <li>
                            @foreach ($categories as $category)
                                <details close>
                                    <summary>
                                        {{ $category->name }}
                                        @if ($category->children_count > 0)
                                            <span class="badge badge-sm">{{ $category->children_count }}</span>
                                        @endif
                                    </summary>
                                    <ul>
                                        @foreach ($category->children as $child)
                                            @if($child->children->count() > 0)
                                            <li>
                                                <details>
                                                    <summary>
                                                        {{ $child->name }}
                                                        <span class="badge badge-sm">{{ $child->children_count }}</span>
                                                    </summary>
                                                    <ul>
                                                        @foreach($child->children as $grandChild)
                                                            <li>
                                                                <a href="{{ route('products.index', ['category' => $grandChild->slug]) }}">
                                                                    {{ $grandChild->name }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </details>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ route('products.index', ['category' => $child->slug]) }}">
                                                    {{ $child->name }}
                                                </a>
                                            </li>
                                        @endif
                                        @endforeach
                                    </ul>
                                </details>
                            @endforeach
                        </li>
                    </ul>
                </li>
                <li class="dropdown dropdown-hover">
                    <a tabindex="0"><i class="fa-solid fa-bars"></i>Menü</a>
                    <ul class="dropdown-content menu shadow bg-base-100 rounded-box w-52">
                        <li><a href="/about"><i class="fa-solid fa-info-circle"></i>Hakkımızda</a></li>
                        <li><a href="/contact"><i class="fa-solid fa-envelope"></i>İletişim</a></li>
                        <li><a href="/faq"><i class="fa-solid fa-question-circle"></i>SSS</a></li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- Right Side Menu -->
        <div class="navbar-end gap-0.5 md:gap-1">
            <!-- Theme Switcher -->
            <div class="dropdown dropdown-end dropdown-hover">
                <label tabindex="0" class="btn btn-ghost btn-circle btn-sm md:btn-md">
                    <i class="fa-solid fa-palette text-sm md:text-base"></i>
                </label>
                <div
                    class="dropdown-content bg-base-200 rounded-box w-48 md:w-72 h-[70vh] overflow-y-auto p-4 -mr-2 md:mr-0">
                    <div class="flex flex-col gap-4">
                        <h2 class="font-bold text-lg mb-2">Temalar</h2>

                        <!-- Light Themes -->
                        <div class="grid grid-cols-2 gap-2">
                            @foreach (['light', 'cupcake', 'bumblebee', 'emerald', 'corporate'] as $theme)
                                <label class="theme-item flex-1">
                                    <input type="radio" name="theme-dropdown" value="{{ $theme }}"
                                        x-model="theme" @change="localStorage.setItem('theme', $event.target.value)"
                                        class="theme-controller hidden">
                                    <div class="cursor-pointer rounded-lg overflow-hidden border-2 hover:border-primary"
                                        :class="theme === '{{ $theme }}' ? 'border-primary' : 'border-transparent'">
                                        <div class="w-full h-8 preview-theme" data-theme="{{ $theme }}">
                                            <div class="w-full h-full bg-base-100 grid grid-cols-5">
                                                <div class="bg-base-200"></div>
                                                <div class="bg-base-300"></div>
                                                <div class="bg-primary"></div>
                                                <div class="bg-secondary"></div>
                                                <div class="bg-accent"></div>
                                            </div>
                                        </div>
                                        <div class="theme-name px-2 py-1 text-xs capitalize bg-base-100">
                                            {{ $theme }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <!-- Dark Themes -->
                        <h3 class="font-medium text-sm text-base-content/80 mt-4">Koyu Temalar</h3>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach (['dark', 'synthwave', 'cyberpunk', 'halloween', 'forest', 'black', 'dracula', 'night', 'coffee'] as $theme)
                                <label class="theme-item flex-1">
                                    <input type="radio" name="theme-dropdown" value="{{ $theme }}"
                                        class="theme-controller hidden"
                                        {{ session('theme') === $theme ? 'checked' : '' }}>
                                    <div
                                        class="cursor-pointer rounded-lg overflow-hidden border-2 hover:border-primary 
                                         {{ session('theme') === $theme ? 'border-primary' : 'border-transparent' }}">
                                        <div class="w-full h-8 preview-theme" data-theme="{{ $theme }}">
                                            <div class="w-full h-full bg-base-100 grid grid-cols-5">
                                                <div class="bg-base-200"></div>
                                                <div class="bg-base-300"></div>
                                                <div class="bg-primary"></div>
                                                <div class="bg-secondary"></div>
                                                <div class="bg-accent"></div>
                                            </div>
                                        </div>
                                        <div class="theme-name px-2 py-1 text-xs capitalize bg-base-100">
                                            {{ $theme }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <!-- Colorful Themes -->
                        <h3 class="font-medium text-sm text-base-content/80 mt-4">Renkli Temalar</h3>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach (['retro', 'valentine', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'luxury', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'winter', 'dim', 'nord', 'sunset', 'caramellatte', 'abyss', 'silk'] as $theme)
                                <label class="theme-item flex-1">
                                    <input type="radio" name="theme-dropdown" value="{{ $theme }}"
                                        class="theme-controller hidden"
                                        {{ session('theme') === $theme ? 'checked' : '' }}>
                                    <div
                                        class="cursor-pointer rounded-lg overflow-hidden border-2 hover:border-primary 
                                         {{ session('theme') === $theme ? 'border-primary' : 'border-transparent' }}">
                                        <div class="w-full h-8 preview-theme" data-theme="{{ $theme }}">
                                            <div class="w-full h-full bg-base-100 grid grid-cols-5">
                                                <div class="bg-base-200"></div>
                                                <div class="bg-base-300"></div>
                                                <div class="bg-primary"></div>
                                                <div class="bg-secondary"></div>
                                                <div class="bg-accent"></div>
                                            </div>
                                        </div>
                                        <div class="theme-name px-2 py-1 text-xs capitalize bg-base-100">
                                            {{ $theme }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Arama -->
            <div x-data="{ open: false }" class="dropdown dropdown-end tooltip tooltip-bottom  rounded-full"
                data-tip="Ara">
                <!-- Arama Butonu (Mobil) -->
                <label tabindex="0" class="btn btn-ghost btn-circle btn-sm md:btn-md" @click="open = !open">
                    <i
                        class="fa-solid fa-search text-sm md:text-base bg-gradient-to-r from-primary/10 to-secondary/10 rounded-full"></i>
                </label>

                <!-- Dropdown İçeriği -->
                <div x-show="open" @click.outside="open = false" tabindex="0"
                    class="dropdown-content z-[1] w-48 lg:w-60 p-2 shadow-lg  backdrop-blur-md rounded-box max-h-screen mt-2 -mr-2 md:mr-0 bg-gradient-to-r from-primary/10 to-secondary/10 ">
                    <livewire:search-products />
                </div>
            </div>


            @auth
                <!-- Auth User Menu -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle btn-sm md:btn-md avatar online">
                        <div class="w-8 md:w-10 rounded-full ring ring-primary ring-offset-2">
                            @if (isset(Auth::user()->avatar))
                                <img src="/storage/{{ Auth::user()->avatar }}" alt="avatar" />
                            @else
                                <img src="{{ asset('/default_user_avatar.jpg') }}" alt="avatar" />
                            @endif
                        </div>
                    </label>
                    <ul class="menu dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        @if (auth()->user()->hasRole('admin'))
                            <li><a href="{{ route('filament.admin.pages.dashboard') }}"><i
                                        class="fa-solid fa-gauge"></i>Genel Bakış</a></li>
                            <li><a href="{{ route('filament.admin.resources.orders.index') }}"><i
                                        class="fa-solid fa-shopping-bag"></i>Siparişlerim</a></li>
                        @endif
                        <li><a href="{{ route('profile.show') }}"><i class="fa-solid fa-user"></i>Profilim</a></li>
                        <li><a href="{{ route('wishlist.index') }}"><i class="fa-solid fa-heart"></i>Favorilerim</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-error w-full text-left">
                                    <i class="fa-solid fa-sign-out-alt"></i>Çıkış Yap
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <!-- Guest Menu -->
                <div class="flex items-center gap-0.5 md:gap-1">
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-sign-in-alt text-sm md:text-base"></i>
                        <span class="hidden sm:inline">Giriş</span>
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-user-plus text-sm md:text-base"></i>
                        <span class="hidden sm:inline">Kayıt</span>
                    </a>
                </div>
            @endauth
        </div>
    </nav>
</header>
