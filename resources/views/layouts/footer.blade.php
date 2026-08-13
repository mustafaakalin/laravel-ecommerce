<footer class="relative mt-16 backdrop-blur-sm mb-8 p-8 rounded-md bg-gradient-to-t from-primary/10 to-secondary/10">
    {{-- Newsletter Section --}}
    @php

        $footerCategories = App\Models\Category::where('is_active', true)
            ->withCount('products')
            ->activeProductsCount()
            ->take(5)
            ->get();

    @endphp


@php
$siteSetting = App\Models\SiteSetting::first();
@endphp
    <div class="absolute -top-20 left-1/2 transform -translate-x-1/2 w-full max-w-4xl px-8 pb-16 mb-16 mx-auto">
        <!-- Newsletter Section -->
        <div class="backdrop-blur bg-primary/50 rounded-2xl p-6 sm:p-8 shadow-xl mb-8">
            <div class="flex flex-wrap sm:flex-row gap-6 items-center justify-between">
                <div class="text-primary-content items-center  my-2">
                    <h3 class="text-xl sm:text-2xl font-bold mb-2">
                        <i class="fas fa-envelope-open-text mr-2"></i>
                        Bültenimize Abone Olun
                    </h3>
                    <p class="opacity-90">En son ürünlerimiz ve tekliflerimizle güncel kalın</p>
                </div>
                @if($siteSetting->social_facebook)
                <a href="{{ $siteSetting->social_facebook }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-blue-600 hover:text-white transition-all duration-300 items-center justify-center flex" 
                   data-tip="Facebook">
                    <i class="fab fa-facebook-f text-xl"></i>
                </a>
                @endif
            
                @if($siteSetting->social_instagram)
                <a href="{{ $siteSetting->social_instagram }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-pink-600 hover:text-white transition-all duration-300  items-center justify-center flex" 
                   data-tip="Instagram">
                    <i class="fab fa-instagram text-xl"></i>
                </a>
                @endif
            
                @if($siteSetting->social_youtube)
                <a href="{{ $siteSetting->social_youtube }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-red-600 hover:text-white transition-all duration-300  items-center justify-center flex" 
                   data-tip="YouTube">
                    <i class="fab fa-youtube text-xl"></i>
                </a>
                @endif
            
                @if($siteSetting->social_x)
                <a href="{{ $siteSetting->social_x }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-black hover:text-white transition-all duration-300  items-center justify-center flex" 
                   data-tip="X">
                    <i class="fab fa-x-twitter text-xl"></i>
                </a>
                @endif
            
                @if($siteSetting->social_tiktok)
                <a href="{{ $siteSetting->social_tiktok }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-black hover:text-white transition-all duration-300  items-center justify-center flex" 
                   data-tip="TikTok">
                    <i class="fab fa-tiktok text-xl"></i>
                </a>
                @endif
            
                @if($siteSetting->social_linkedin)
                <a href="{{ $siteSetting->social_linkedin }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-blue-700 hover:text-white transition-all duration-300  items-center justify-center flex" 
                   data-tip="LinkedIn">
                    <i class="fab fa-linkedin-in text-xl"></i>
                </a>
                @endif
       
            
                @if($siteSetting->social_whatsapp_group)
                <a href="{{ $siteSetting->social_whatsapp_group }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-green-600 hover:text-white transition-all duration-300  items-center justify-center flex" 
                   data-tip="WhatsApp Group">
                    <i class="fab fa-whatsapp text-xl"></i>
                </a>
                @endif
            
                @if($siteSetting->social_whatsapp_channel)
                <a href="{{ $siteSetting->social_whatsapp_channel }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-green-600 hover:text-white transition-all duration-300  items-center justify-center flex" 
                   data-tip="WhatsApp Kanal">
                    <i class="fab fa-whatsapp text-xl"></i>
                </a>
                @endif
            
                @if($siteSetting->social_telegram_group)
                <a href="{{ $siteSetting->social_telegram_group }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-blue-500 hover:text-white transition-all duration-300  items-center justify-center flex" 
                   data-tip="Telegram Group">
                    <i class="fab fa-telegram text-xl"></i>
                </a>
                @endif
            
                @if($siteSetting->social_telegram_channel)
                <a href="{{ $siteSetting->social_telegram_channel }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-blue-500 hover:text-white transition-all duration-300 items-center justify-center flex" 
                   data-tip="Telegram Kanal">
                    <i class="fab fa-telegram text-xl"></i>
                </a>
                @endif
            
                @if($siteSetting->social_facebook_group)
                <a href="{{ $siteSetting->social_facebook_group }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-[#1877F2] hover:text-white transition-all duration-300 items-center justify-center flex" 
                   data-tip="Facebook Group">
                    <i class="fab fa-facebook text-xl"></i>
                </a>
                @endif
            
                @if($siteSetting->social_facebook_page)
                <a href="{{ $siteSetting->social_facebook_page }}" 
                   class="btn btn-ghost tooltip tooltip-info  hover:bg-[#1877F2] hover:text-white  transition-all duration-300 items-center justify-center flex" 
                   data-tip="Facebook Sayfa">
                    <i class="fab fa-facebook text-xl"></i>
                </a>
                @endif
            
                @if($siteSetting->social_reddit_community)
                <a href="{{ $siteSetting->social_reddit_community }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-[#FF4500] hover:text-white transition-all duration-300 items-center justify-center flex" 
                   data-tip="Reddit">
                    <i class="fab fa-reddit text-xl"></i>
                </a>
                @endif
            
                @if($siteSetting->social_instagram_broadcast_channnel)
                <a href="{{ $siteSetting->social_instagram_broadcast_channnel }}" 
                   class="btn btn-ghost tooltip tooltip-info hover:bg-gradient-to-tr from-[#FF5C3B] via-[#C13584] to-[#833AB4] hover:text-white transition-all duration-300 items-center justify-center flex" 
                   data-tip="Instagram Yayın Kanalı">
                    <i class="fab fa-instagram text-xl"></i>
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="container mx-auto pt-24">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            {{-- Categories Column --}}
            <div class="space-y-4">
                <h4 class="text-lg font-bold mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-tags text-primary"></i>
                    Popüler Kategoriler
                </h4>
                <ul class="space-y-3">
                    @foreach ($footerCategories as $category)
                        <li>
                            <a href="{{ route('categories.show', $category->slug) }}"
                                class="hover:text-primary transition-colors flex items-center gap-2 group">
                                <i
                                    class="fa-solid fa-chevron-right text-xs text-base-content/50 group-hover:text-primary transition-colors"></i>
                                <span>{{ $category->name }}</span>
                                <span class="badge badge-sm">{{ $category->active_products_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Quick Links --}}
            <div class="space-y-4">
                <h4 class="text-lg font-bold mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-link text-primary"></i>
                    Hızlı Bağlantılar
                </h4>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('about') }}"
                            class="hover:text-primary transition-colors flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-xs text-base-content/50 group-hover:text-primary transition-colors"></i>
                            Hakkımızda
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}"
                            class="hover:text-primary transition-colors flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-xs text-base-content/50 group-hover:text-primary transition-colors"></i>
                            İletişim
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('faq') }}"
                            class="hover:text-primary transition-colors flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-xs text-base-content/50 group-hover:text-primary transition-colors"></i>
                            SSS
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Legal --}}
            <div class="space-y-4">
                <h4 class="text-lg font-bold mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-scale-balanced text-primary"></i>
                    Yasal
                </h4>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('privacy-policy') }}"
                            class="hover:text-primary transition-colors flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-xs text-base-content/50 group-hover:text-primary transition-colors"></i>
                            Gizlilik Politikası
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('terms') }}"
                            class="hover:text-primary transition-colors flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-xs text-base-content/50 group-hover:text-primary transition-colors"></i>
                            Şartlar ve Koşullar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('shipping-policy') }}"
                            class="hover:text-primary transition-colors flex items-center gap-2 group">
                            <i
                                class="fa-solid fa-chevron-right text-xs text-base-content/50 group-hover:text-primary transition-colors"></i>
                            Nakliye Politikası
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Contact Info --}}
            <div class="space-y-4">
                <h4 class="text-lg font-bold mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-address-card text-primary"></i>
                    İletişim Bilgileri
                </h4>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot mt-1 text-primary"></i>
                        <span>{{ App\Models\SiteSetting::first()->site_address }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-phone text-primary"></i>
                        <a href="tel:{{ App\Models\SiteSetting::first()->site_phone }}"
                            class="hover:text-primary transition-colors">
                            +90 {{ App\Models\SiteSetting::first()->site_phone }}
                        </a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope text-primary"></i>
                        <a href="mailto:{{ App\Models\SiteSetting::first()->site_mail }}"
                            class="hover:text-primary transition-colors">
                            {{ App\Models\SiteSetting::first()->site_mail }}
                        </a>
                    </li>
                </ul>

                {{-- Social Media --}}
                <div class="flex gap-4 mt-6">
                    <a target="blank" href="https://facebook.com/{{ App\Models\SiteSetting::first()->social_facebook }}"
                        class="btn btn-circle btn-sm btn-ghost hover:text-primary">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a target="blank" href="https://x.com/{{ App\Models\SiteSetting::first()->social_x }}"
                        class="btn btn-circle btn-sm btn-ghost hover:text-primary">
                        <i class="fa-brands fa-twitter"></i>
                    </a>
                    <a target="blank"
                        href="https://instagram.com/{{ App\Models\SiteSetting::first()->social_instagram }}"
                        class="btn btn-circle btn-sm btn-ghost hover:text-primary">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a target="blank"
                        href="https://linkedin.com/in/{{ App\Models\SiteSetting::first()->social_linkedin }}"
                        class="btn btn-circle btn-sm btn-ghost hover:text-primary">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                    <a target="blank"
                        href="https://youtube.com/{{ '@' . App\Models\SiteSetting::first()->social_youtube }}"
                        class="btn btn-circle btn-sm btn-ghost hover:text-primary">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a target="blank"
                        href="https://tiktok.com/{{ '@' . App\Models\SiteSetting::first()->social_tiktok }}"
                        class="btn btn-circle btn-sm btn-ghost hover:text-primary">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="border-t border-base-300 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-2xl text-primary"></i>
                    <span class="text-xl font-bold">{{ App\Models\SiteSetting::first()->site_name }}</span>
                </div>

                <div class="text-sm text-base-content/60">
                    © {{ date('Y') }} {{ App\Models\SiteSetting::first()->site_name }}. Tüm hakları saklıdır.
                    ❤️‍🔥🤩🌟
                </div>

                {{-- Payment Methods --}}
                <div class="flex gap-2">
                    <div class="tooltip" data-tip="Visa">
                        <i class="fa-brands fa-cc-visa text-2xl opacity-50 hover:opacity-100 transition-opacity"></i>
                    </div>
                                        <div class="tooltip" data-tip="Mastercard">
                        <i class="fa-brands fa-cc-mastercard text-2xl opacity-50 hover:opacity-100 transition-opacity"></i>
                    </div>
                    <div class="tooltip" data-tip="PayPal">
                        <i class="fa-brands fa-cc-paypal text-2xl opacity-50 hover:opacity-100 transition-opacity"></i>
                    </div>
                    <div class="tooltip" data-tip="Apple Pay">
                        <i class="fa-brands fa-cc-apple-pay text-2xl opacity-50 hover:opacity-100 transition-opacity"></i>
                    </div>
                    <div class="tooltip" data-tip="Troy">
                        <i class="fa-solid fa-credit-card text-2xl opacity-50 hover:opacity-100 transition-opacity"></i>
                    </div>
                    <div class="tooltip" data-tip="Havale/EFT">
                        <i class="fa-solid fa-money-bill text-2xl opacity-50 hover:opacity-100 transition-opacity"></i>
                    </div>
                    <div class="tooltip" data-tip="Google Pay">
                        <i class="fa-brands fa-google-pay text-2xl opacity-50 hover:opacity-100 transition-opacity"></i>
                    </div>
                    <div class="tooltip" data-tip="Online Banking">
                        <i class="fa-solid fa-building-columns text-2xl opacity-50 hover:opacity-100 transition-opacity"></i>
                    </div>
                    <div class="tooltip" data-tip="QR Payment">
                        <i class="fa-solid fa-qrcode text-2xl opacity-50 hover:opacity-100 transition-opacity"></i>
                    </div>
                    <div class="tooltip" data-tip="ETBIS BILGILERI">
                        <a href="{{ $siteSetting->site_etbis_link ?? '' }}">
                            <img src="{{ $siteSetting->site_etbis_qr ?? 'https://images.hepsiburada.net/assets/footer/etbis-icon.webp' }}" alt="" class="h-6 w-6  text-2xl opacity-50 hover:opacity-100 transition-opacity">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
