<!-- filepath: /home/mustafa/Documents/Projects/php/laravel/ecommerce1/resources/views/livewire/checkout/payment-form.blade.php -->
<div x-data="{
    cardNumber: @entangle('cardNumber'),
    cardName: @entangle('cardName'),
    expiryMonth: @entangle('expiryMonth'),
    expiryYear: @entangle('expiryYear'),
    cvc: @entangle('cvc'),
    isFlipped: false,
    get formattedCardNumber() {
        let value = this.cardNumber.replace(/\D/g, '');
        let formatted = '';
        for (let i = 0; i < value.length && i < 16; i++) {
            if (i > 0 && i % 4 === 0) {
                formatted += ' ';
            }
            formatted += value[i];
        }
        return formatted || '•••• •••• •••• ••••';
    },
    get formattedExpiryDate() {
        let value = this.expiryMonth + this.expiryYear;
        value = value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2, 4);
        }
        return value || 'MM/YY';
    },
    get formattedCVC() {
        let value = this.cvc.replace(/\D/g, '').slice(0, 3);
        return value || '•••';
    },
}" x-init="
    $watch('expiryYear', (value) => {
        if (value.length === 2) {
            if (parseInt(value) < 22) {
                value = '20' + value;
            } else {
                value = '20' + value;
            }
        }
    });
" class="card bg-base-100 shadow-xl">

    <div class="card-body">
        <h2 class="card-title text-2xl mb-4">Payment Information</h2>

        <!-- Animated Credit Card -->
        <div class="w-full p-4 sm:p-6 md:p-8 flex items-center justify-center">
            <!-- Credit Card Container -->
            <div class="flip-card w-full max-w-[280px] sm:max-w-[320px] md:max-w-[384px] h-44 sm:h-48 md:h-56 relative cursor-pointer"
                @click="isFlipped = !isFlipped">

                <!-- Credit Card Inner -->
                <div class="flip-card-inner relative w-full h-full" :class="{ 'is-flipped': isFlipped }">

                    <!-- Front of the Card -->
                    <div
                        class="flip-card-front absolute w-full h-full rounded-xl p-4 sm:p-5 md:p-6 bg-gradient-to-br from-[#000046] to-[#1CB5E0]">
                        <!-- Card Header -->
                        <div class="flex justify-between items-start">
                            <div class="w-12 sm:w-14 md:w-16 h-8 sm:h-10 md:h-12">
                                <div
                                    class="w-8 sm:w-10 md:w-12 h-6 sm:h-8 md:h-10 rounded bg-gradient-to-br from-yellow-300 to-yellow-400">
                                </div>
                            </div>
                            <span class="text-xs sm:text-sm text-white/90 font-medium">CREDIT CARD</span>
                        </div>

                        <!-- Card Number -->
                        <div class="mt-4 sm:mt-6 md:mt-8">
                            <div class="text-lg sm:text-xl md:text-2xl text-white tracking-wider font-mono">
                                <span x-text="formattedCardNumber"></span>
                            </div>
                        </div>

                        <!-- Card Details -->
                        <div class="mt-4 sm:mt-6 md:mt-8 flex justify-between items-end">
                            <div>
                                <div class="text-[10px] sm:text-xs text-white/70">Card Holder</div>
                                <div class="text-sm sm:text-base text-white font-mono uppercase mt-0.5 sm:mt-1">
                                    <span x-text="cardName.toUpperCase()"></span>
                                </div>
                            </div>
                            <div>
                                <div class="text-[10px] sm:text-xs text-white/70">Expires</div>
                                <div class="text-sm sm:text-base text-white font-mono mt-0.5 sm:mt-1">
                                    <span x-text="formattedExpiryDate"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Back of the Card -->
                    <div
                        class="flip-card-back absolute w-full h-full rounded-xl bg-gradient-to-br from-gray-700 to-gray-800">
                        <!-- Magnetic Strip -->
                        <div class="w-full h-8 sm:h-10 md:h-12 bg-black mt-6 sm:mt-7 md:mt-8"></div>

                        <!-- CVV Section -->
                        <div class="px-4 sm:px-5 md:px-6 mt-6 sm:mt-7 md:mt-8">
                            <div class="h-8 sm:h-10 md:h-12 bg-white/90 rounded flex items-center justify-end pr-4">
                                <div class="font-mono text-black">
                                    <span x-text="formattedCVC"></span>
                                </div>
                            </div>

                            <!-- Bank Details -->
                            <div class="flex justify-between items-center mt-4 sm:mt-5 md:mt-6">
                                <div class="text-white/80 text-xs sm:text-sm">Powered by AKALIN TECH</div>
                                <div class="text-white font-semibold text-base sm:text-lg">BANK.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Card Holder Name</span>
                </label>
                <input type="text" wire:model.live="cardName" class="input input-bordered" placeholder="John Doe" />
                @error('cardName') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Card Number</span>
                </label>
                <input type="text" wire:model.live="cardNumber" class="input input-bordered"
                    placeholder="1234 5678 9012 3456" />
                @error('cardNumber') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Expiry Date</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" wire:model.live="expiryMonth" class="input input-bordered w-20"
                            placeholder="MM" />
                        <span class="self-center">/</span>
                        <input type="text" wire:model.live="expiryYear" class="input input-bordered w-20"
                            placeholder="YY" />
                    </div>
                    @error('expiryMonth') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    @error('expiryYear') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">CVC</span>
                    </label>
                    <input type="text" wire:model.live="cvc" class="input input-bordered w-24" placeholder="123"
                        @focus="isFlipped = true" @blur="isFlipped = false" />
                    @error('cvc') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>
</div>