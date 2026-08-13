<div class="container mx-auto py-8 px-4">
    @if(Auth::check() && Auth::user()->addresses->isEmpty())
        <div class="min-h-[60vh] flex flex-col items-center justify-center space-y-6">
            <div class="card w-96 bg-base-100 shadow-xl">
                <div class="card-body items-center text-center">
                    <h2 class="card-title text-2xl mb-4">No Address Found!</h2>
                    <p class="text-gray-600 mb-6">Please add a delivery address to continue checkout.</p>
                    <a href="{{ route('addresses.create') }}" class="btn btn-primary btn-wide">
                        Add New Address
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="flex-grow space-y-6">
                <h1 class="text-3xl font-bold mb-8 text-center lg:text-left">Checkout</h1>
                <livewire:checkout.address-selector />
                <livewire:checkout.payment-form />
            </div>
            <div class="lg:w-1/3">
                <livewire:checkout.order-summary 
                    :subtotal="$subtotal"
                    :shipping="$shipping"
                    :discount="$discount"
                    :total="$total"
                />
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('orderSuccess', (data) => {
                // Show success message and redirect
                window.location.href = `/orders/${data.orderId}`;
            });

            Livewire.on('checkoutError', (data) => {
                // Show error message
                alert(data.message);
            });
        });
    </script>
</div>