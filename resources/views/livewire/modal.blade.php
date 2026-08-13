<div>
    @if ($show)
    <div x-data="{ show: true }" x-init="
            setTimeout(() => { show = false; @this.closeModal() }, 2000);
            $watch('show', value => { if (!value) @this.closeModal(); });" x-show="show" class="fixed inset-0 z-50">

        <!-- Backdrop with blur -->
        <div class="fixed inset-0 bg-base-300/50 backdrop-blur-sm" @click="show = false">

            <!-- Modal Container -->
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <!-- Modal Box -->
                <div class="modal-box relative w-full max-w-md shadow-2xl" :class="{
                    'bg-gradient-to-br from-base-100 to-base-200': type === 'info',
                    'bg-gradient-to-br from-success/10 to-base-100': type === 'success',
                    'bg-gradient-to-br from-error/10 to-base-100': type === 'error',
                    'bg-gradient-to-br from-warning/10 to-base-100': type === 'warning'
                 }">

                    <!-- Close Button -->
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                        @click="show = false">✕</button>

                    <!-- Modal Content -->
                    <div class="flex flex-col items-center space-y-4 text-center pt-6">
                        <!-- Icons -->
                        @if ($type === 'success')
                        <div class="avatar placeholder">
                            <div class="bg-success/20 text-success rounded-full w-16">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="font-bold text-xl text-success">Başarılı!</h3>
                        @elseif ($type === 'error')
                        <div class="avatar placeholder">
                            <div class="bg-error/20 text-error rounded-full w-16">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="font-bold text-xl text-error">Hata!</h3>
                        @elseif ($type === 'warning')
                        <div class="avatar placeholder">
                            <div class="bg-warning/20 text-warning rounded-full w-16">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="font-bold text-xl text-warning">Uyarı!</h3>
                        @else
                        <div class="avatar placeholder">
                            <div class="bg-info/20 text-info rounded-full w-16">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="font-bold text-xl text-info">Bilgi</h3>
                        @endif

                        <!-- Message -->
                        <p class="py-4 text-base-content/80">{{ $message }}</p>

                        <!-- Action Button -->
                        <div class="modal-action justify-center w-full">
                            <button @click="show = false" class="btn btn-block normal-case" :class="{
                                    'btn-info': type === 'info',
                                    'btn-success': type === 'success',
                                    'btn-error': type === 'error',
                                    'btn-warning': type === 'warning'
                                }">
                                Kapat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Opsiyonel: Kuyrukta bekleyen toast sayısını göster -->
    @if(count($queue) > 0)
        <div class="absolute -right-2 -top-2 bg-primary text-primary-content rounded-full w-5 h-5 flex items-center justify-center text-xs">
            {{ count($queue) }}
        </div>
    @endif
    @endif
    
</div>