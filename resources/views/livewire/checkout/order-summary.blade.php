<div class="card bg-base-100 shadow-xl sticky top-4">
    <div class="card-body">
        <h2 class="card-title text-2xl mb-6">Order Summary</h2>
        
        <div class="space-y-4">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span>{{ number_format($subtotal, 2) }}₺</span>
            </div>
            
            @if($discount > 0)
            <div class="flex justify-between text-success">
                <span>Discount</span>
                <span>-{{ number_format($discount, 2) }}₺</span>
            </div>
            @endif
            
            <div class="flex justify-between">
                <span class="text-gray-600">Shipping</span>
                <span>{{ number_format($shipping, 2) }}₺</span>
            </div>
            
            <div class="divider my-2"></div>
            
            <div class="flex justify-between text-lg font-bold">
                <span>Total</span>
                <span>{{ number_format($total, 2) }}₺</span>
            </div>

            <button wire:click="processCheckout" class="btn btn-primary w-full">
                Complete Order
            </button>
        </div>
    </div>
</div>