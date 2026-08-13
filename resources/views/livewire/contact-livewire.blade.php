<div class="min-h-screen">
    <!-- Hero Section -->

    <div class="hero min-h-[40vh] ">
        <div class="hero-content text-center">
            <div class="max-w-3xl">
                <h1 class="text-5xl font-bold mb-8">İletişim</h1>
                <div class="divider"></div>
                <p class="text-lg">Bizimle iletişime geçin</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <!-- Contact Form -->
            <div class="card  shadow-xl">
                <div class="card-body">
                    <h2 class="card-title mb-6">İletişim Formu</h2>
                    
                    <form wire:submit="submit" class="space-y-6">
                        <!-- Name Input -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Ad Soyad</span>
                            </label>
                            <input type="text" 
                                wire:model.live.debounce.500ms="name" 
                                pattern="^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]{3,50}$"
                                minlength="3"
                                maxlength="50"
                                placeholder="Adınız ve Soyadınız"
                                class="input input-bordered @error('name') input-error @enderror" 
                                required />
                            @error('name')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Email Input -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">E-posta</span>
                            </label>
                            <input type="email" 
                                wire:model.live.debounce.500ms="email" 
                                pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                placeholder="ornek@email.com"
                                class="input input-bordered @error('email') input-error @enderror" 
                                required />
                            @error('email')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Phone Input -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Telefon</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">+90</span>
                                <input type="tel" 
                                    wire:model.live.debounce.500ms="phone" 
                                    pattern="^5[0-9]{9}$"
                                    minlength="10"
                                    maxlength="10"
                                    placeholder="5XX XXX XX XX"
                                    class="input input-bordered @error('phone') input-error @enderror pl-12" 
                                    required />
                            </div>
                            @error('phone')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Message Input -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Mesajınız</span>
                            </label>
                            <textarea 
                                wire:model.live.debounce.500ms="message" 
                                minlength="10"
                                maxlength="1000"
                                placeholder="Mesajınızı buraya yazın..."
                                class="textarea textarea-bordered h-32 @error('message') textarea-error @enderror"
                                required></textarea>
                            @error('message')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-full" wire:loading.attr="disabled">
                            <span wire:loading.remove>Gönder</span>
                            <span wire:loading>
                                <span class="loading loading-spinner"></span>
                                Gönderiliyor...
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="space-y-8">
                <!-- Company Info -->
                <div class="card  shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-6">İletişim Bilgileri</h2>
                        
                        <div class="space-y-4">
                            @if($settings->site_phone)
                            <a href="tel:{{ $settings->site_phone }}" class="flex items-center gap-3 hover:text-primary transition-colors">
                                <i class="fa-solid fa-phone w-5 h-5"></i>
                                {{ $settings->site_phone }}
                            </a>
                            @endif

                            @if($settings->site_mail)
                            <a href="mailto:{{ $settings->site_mail }}" class="flex items-center gap-3 hover:text-primary transition-colors">
                                <i class="fa-solid fa-envelope w-5 h-5"></i>
                                {{ $settings->site_mail }}
                            </a>
                            @endif

                            @if($settings->site_address)
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-location-dot w-5 h-5 mt-1"></i>
                                <span>{{ $settings->site_address }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="card  shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-6">Sosyal Medya</h2>
                        <div class="flex flex-wrap gap-4">
                            @if($settings->social_instagram)
                            <a href="https://instagram.com/{{ $settings->social_instagram }}" target="_blank" class="btn btn-circle btn-ghost hover:text-pink-500">
                                <i class="fa-brands fa-instagram fa-lg"></i>
                            </a>
                            @endif

                            @if($settings->social_facebook)
                            <a href="https://facebook.com/{{ $settings->social_facebook }}" target="_blank" class="btn btn-circle btn-ghost hover:text-blue-600">
                                <i class="fa-brands fa-facebook fa-lg"></i>
                            </a>
                            @endif

                            @if($settings->social_youtube)
                            <a href="https://youtube.com/{{ '@' . $settings->social_youtube }}" target="_blank" class="btn btn-circle btn-ghost hover:text-red-600">
                                <i class="fa-brands fa-youtube fa-lg"></i>
                            </a>
                            @endif

                            @if($settings->social_tiktok)
                            <a href="https://tiktok.com/{{ '@' . $settings->social_tiktok }}" target="_blank" class="btn btn-circle btn-ghost hover:text-black">
                                <i class="fa-brands fa-tiktok fa-lg"></i>
                            </a>
                            @endif

                            @if($settings->social_linkedin)
                            <a href="https://linkedin.com/in/{{ $settings->social_linkedin }}" target="_blank" class="btn btn-circle btn-ghost hover:text-blue-700">
                                <i class="fa-brands fa-linkedin fa-lg"></i>
                            </a>
                            @endif

                            @if($settings->social_x)
                            <a href="https://x.com/{{ $settings->social_x }}" target="_blank" class="btn btn-circle btn-ghost hover:text-gray-900">
                                <i class="fa-brands fa-x-twitter fa-lg"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="card  shadow-xl">
                    <div class="card-body p-0">
                        <div class="w-full h-[300px]">
                            <iframe 
                                src="{{ $google_maps_embed ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12684602.000875097!2d35.12932955000001!3d39.08764590000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14b0155c964f2671%3A0x40d9dbd42a625f2a!2zVMO8cmtpeWU!5e0!3m2!1sen!2str!4v1734697109719!5m2!1sen!2str' }}"
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                                class="rounded-lg">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>