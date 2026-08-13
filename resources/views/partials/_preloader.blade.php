
<div id="preloader" class="fixed inset-0 z-[9999] bg-base-100 transition-all duration-300 ease-in-out">
    <div class="absolute inset-0 bg-gradient-to-r from-primary/10 to-transparent opacity-50"></div>
    
    <div class="relative flex h-screen items-center justify-center">
        <div class="flex flex-col items-center space-y-6">
            <!-- Logo Container -->
            <div class="relative">
                <div class="absolute inset-0 animate-spin-slow">
                    <div class="h-32 w-32 rounded-full bg-gradient-to-r from-primary/20 to-transparent"></div>
                </div>
                
                <div class="relative h-32 w-32 overflow-hidden rounded-full bg-base-100 p-4">
                    <img src="{{ $siteLogo }}" alt="Logo" class="h-full w-full object-contain animate-float">
                </div>
            </div>

            <!-- Site Name -->
            <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary animate-slide-up">
                {{ $siteName }}
            </h2>

            <!-- Loading Dots -->
            <div class="flex space-x-2">
                @foreach(range(1,3) as $i)
                    <div class="h-2 w-2 rounded-full bg-primary animate-pulse-scale" style="animation-delay: {{ ($i-1) * 100 }}ms"></div>
                @endforeach
            </div>
        </div>
    </div>
</div>