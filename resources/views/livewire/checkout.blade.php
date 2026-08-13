<div class="container mx-auto py-8  px-4">
    @if(Auth::check() && $addresses->isEmpty())
    <div class="min-h-[60vh] flex flex-col items-center justify-center space-y-6">
        <div class="card w-96 bg-base-100 shadow-xl">
            <div class="card-body items-center text-center">
                <h2 class="card-title text-2xl mb-4">No Address Found!</h2>
                <p class="text-gray-600 mb-6">Please add a delivery address to continue checkout.</p>
                <a href="{{ route('filament.admin.resources.addresses.index') }}" class="btn btn-primary btn-wide">
                    Add New Address
                </a>
            </div>
        </div>
    </div>
    @else
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-grow">
            <h1 class="text-3xl font-bold mb-8 text-center lg:text-left">Checkout</h1>
            <form action="" wire:submit.prevent="submit" method="POST" class="space-y-8" id="checkoutForm">
                @csrf
                <!-- Address Selection Section - Only show if multiple addresses -->
                @if($addresses->count() > 1)
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Select Delivery Address</h2>
                        <select class="select select-bordered w-full" id="addressSelector"  wire:model="selectedAddress" wire:change="updateAddress($event.target.value)">
                            @foreach($addresses as $address)
                            <option value="{{ $address->id }}" data-address-name="{{ $address->title }}"
                                data-address="{{ $address->address }}" data-city="{{ $address->city }}"
                                data-state="{{ $address->state }}" data-zip="{{ $address->zip_code }}"
                                data-country="{{ $address->country }}" data-phone="{{ $address->phone }}" {{ $address->
                                is_default ? 'selected' : '' }}>
                                {{ $address->title }} - {{ $address->city }}, {{ $address->state }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @else
                @php
                $singleAddress = $addresses->first();
                @endphp
                @endif


                <!-- Hidden Address Fields -->
                <input type="hidden" name="address_id" id="address_id" value="{{ $selectedAddress }}">

                <input type="hidden" name="address" id="hidden_address" value="{{ $address }}">
                <input type="hidden" name="city" id="hidden_city" value="{{ $city }}">
                <input type="hidden" name="state" id="hidden_state" value="{{ $state }}">
                <input type="hidden" name="zip_code" id="hidden_zip" value="{{ $zip_code }}">
                <input type="hidden" name="country" id="hidden_country" value="{{ $country }}">
                <input type="hidden" name="phone" id="hidden_phone" value="{{ $phone }}">


                <!-- Personal Information Section -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Personal Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">First Name</span>
                                </label>
                                <input type="text" name="first_name" class="input input-bordered"
                                    value="{{ Auth::user()->name }}" readonly>
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Last Name</span>
                                </label>
                                <input type="text" name="last_name" class="input input-bordered"
                                    value="{{ Auth::user()->surname }}" readonly>
                            </div>
                            <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                        </div>
                    </div>
                </div>


                <!-- Selected Address Display -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Delivery Address</h2>
                        <div class="bg-base-200 p-4 rounded-lg">
                            <p class="mb-2">{{ $city }}, {{ $state }}</p>
                            <p class="mb-2">{{ $country }}, {{ $zip_code }}</p>
                            <p>{{ $phone }}</p>
                        </div>
                    </div>
                </div>


                <!-- Payment Section -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Payment Information</h2>
                        
                        <!-- Payment Method Selection -->
                        <div class="mb-6">
                            <div class="flex gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="paymentMethod" value="stripe" class="radio radio-primary">
                                    <span class="ml-2">Credit Card (Stripe)</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="paymentMethod" value="iyzico" class="radio radio-primary">
                                    <span class="ml-2">Iyzico</span>
                                </label>
                            </div>
                        </div>

                        @if($paymentMethod === 'stripe')
                        <!-- Stripe Elements Container -->
                        <div id="stripe-payment-element" class="mb-4"></div>
                        <div id="payment-message" class="text-error hidden"></div>
                        @else
                        <!-- Existing Iyzico Payment Form -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Name on Card</span>
                                </label>
                                <input type="text" id="cardName" name="card_name" wire:model="card_name" class="input input-bordered" required
                                    maxlength="50" wire:model="card_name" value="{{ Auth::user()->name }}"  maxlength="30">
                                <span class="text-error text-sm mt-1 hidden" id="cardNameError"></span>
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Card Number</span>
                                </label>
                                <input type="text" id="cardNumber" name="card_number" class="input input-bordered"
                                    required maxlength="19" placeholder="1234 5678 9012 3456" wire:model="card_number" >
                                <span class="text-error text-sm mt-1 hidden" id="cardNumberError"></span>
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Expiry Date</span>
                                </label>
                                <input type="text" id="cardExpiry" class="input input-bordered" placeholder="MM/YY"
                                    required maxlength="5" wire:model="card_expiry" >
                                <span class="text-error text-sm mt-1 hidden" id="cardExpiryError"></span>
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">CVC</span>
                                </label>
                                <input type="text" id="cardCVC" name="cvc" class="input input-bordered" required
                                    maxlength="3" placeholder="123"  wire:model="cvc" >
                                <span class="text-error text-sm mt-1 hidden" id="cardCVCError"></span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Coupon Section -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Coupon Code</h2>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <input type="text" name="coupon_code" class="input input-bordered flex-grow"
                                placeholder="Enter coupon code">
                            <button type="button" class="btn btn-primary" id="applyCoupon"  wire:click="applyCoupon">Apply Coupon</button>
                        </div>
                        <div id="couponMessage" class="mt-2 text-error hidden"></div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Order Summary - Sticky -->
        <div class="lg:w-1/3">
            <div class="card bg-base-100 shadow-xl sticky top-4">
                <div class="card-body">
                    <h2 class="card-title text-2xl mb-6">Order Summary</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span id="subtotal" class="font-medium">{{ number_format($subtotal, 2) }}₺</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Discount</span>
                            <span id="discount" class="font-medium text-success">0.00₺</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium">{{ number_format($shipping, 2) }}₺</span>
                        </div>
                        <div class="divider"></div>
                        <div class="flex justify-between">
                            <span class="text-lg font-bold">Total</span>
                            <div>
                                <input type="hidden" id="totalinput" name="totalinput" value="{{ $total }}"  wire:model="totalinput">
                                <span id="total" class="text-lg font-bold">{{ number_format($total, 2) }}₺</span>
                            </div>
                        </div>
                        <button type="submit" form="checkoutForm" class="btn btn-primary btn-block"
                            wire:click="submit">
                            Complete Order
                        </button>
                    </div>
                </div>
            </div>
        </div>


    </div>
    @endif
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    let stripe;
    let elements;
    let paymentElement;
    let form = document.getElementById('checkoutForm');
    let submitButton = form.querySelector('button[type="submit"]');
    let processing = false;

    document.addEventListener('livewire:initialized', () => {
        stripe = Stripe('{{ config('services.stripe.key') }}');
        
        // Initialize Stripe when payment method changes to stripe
        Livewire.on('initStripe', async () => {
            submitButton.disabled = true;
            console.log('Initializing Stripe...');
            await @this.createStripePaymentIntent();
        });

        // Set up elements when we receive the payment intent
        Livewire.on('stripePaymentIntent', async ({clientSecret}) => {
            console.log('Received client secret, setting up elements...');
            await setupStripeElements(clientSecret);
        });
    });

    async function setupStripeElements(clientSecret) {
        try {
            if (paymentElement) {
                paymentElement.destroy();
            }

            elements = stripe.elements({
                clientSecret,
                appearance: {
                    theme: 'stripe',
                    variables: {
                        colorPrimary: '#570DF8',
                    }
                }
            });

            paymentElement = elements.create('payment');
            await paymentElement.mount('#stripe-payment-element');
            submitButton.disabled = false;
            
            console.log('Stripe elements mounted successfully');
        } catch (error) {
            console.error('Error setting up Stripe elements:', error);
            document.getElementById('payment-message').textContent = 'Error setting up payment form';
        }
    }

    if (form) {
        form.addEventListener('submit', async (e) => {
            if (@this.get('paymentMethod') !== 'stripe') return;

            e.preventDefault();
            
            if (processing) return;
            processing = true;
            submitButton.disabled = true;

            const messageDiv = document.getElementById('payment-message');
            messageDiv.textContent = 'Processing payment...';
            messageDiv.classList.remove('hidden');

            try {
                const {error, paymentIntent} = await stripe.confirmPayment({
                    elements,
                    redirect: 'if_required',
                    confirmParams: {
                        return_url: '{{ route('payment.success') }}',
                        payment_method_data: {
                            billing_details: {
                                name: '{{ Auth::user()->name }}',
                                email: '{{ Auth::user()->email }}'
                            }
                        }
                    }
                });

                if (error) {
                    console.error('Payment error:', error);
                    messageDiv.textContent = error.message;
                    processing = false;
                    submitButton.disabled = false;
                } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                    console.log('Payment successful:', paymentIntent);
                    messageDiv.textContent = 'Payment successful!';
                    await @this.dispatch('stripe-payment-success', {paymentIntent});
                }
            } catch (error) {
                console.error('Payment error:', error);
                messageDiv.textContent = 'An unexpected error occurred';
                processing = false;
                submitButton.disabled = false;
            }
        });
    }
</script>
@endpush