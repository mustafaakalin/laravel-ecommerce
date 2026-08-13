<div x-data="{ 
    show: @entangle('show'),
    queue: @entangle('queue'),
    timer: null,
    init() {
        $watch('show', value => {
            if (value) {
                this.timer = setTimeout(() => {
                    this.show = false;
                    // Toast kapandıktan sonra kuyruktaki diğer toast'ı göster
                    setTimeout(() => {
                        @this.processQueue();
                    }, 300); // transition süresi kadar bekle
                }, 3000);
            }
        });
    }
}" 
x-show="show" 
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0 transform translate-y-2"
x-transition:enter-end="opacity-100 transform translate-y-0"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100 transform translate-y-0"
x-transition:leave-end="opacity-0 transform translate-y-2"
class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50">

<div class="alert shadow-lg {{ $type === 'success' ? 'alert-success' : ($type === 'error' ? 'alert-error' : ($type === 'warning' ? 'alert-warning' : 'alert-info')) }}">
    <div class="flex items-center gap-2">
        @switch($type)
            @case('success')
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                @break
            @case('error')
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                @break
            @case('warning')
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                @break
            @default
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
        @endswitch
        <span>{{ $message }}</span>
    </div>
</div>

<!-- Opsiyonel: Kuyrukta bekleyen toast sayısını göster -->
@if(count($queue) > 0)
    <div class="absolute -right-2 -top-2 bg-primary text-primary-content rounded-full w-5 h-5 flex items-center justify-center text-xs">
        {{ count($queue) }}
    </div>
@endif
</div>