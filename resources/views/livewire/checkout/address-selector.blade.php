<div class="card bg-base-100 shadow-xl">
    <div class="card-body">
        <h2 class="card-title text-2xl mb-4">Delivery Address</h2>
        
        @if($addresses->count() > 1)
            <select wire:model.live="selectedAddressId" class="select select-bordered w-full">
                @foreach($addresses as $address)
                    <option value="{{ $address->id }}">
                        {{ $address->title }} - {{ $address->city }}, {{ $address->state }}
                    </option>
                @endforeach
            </select>
        @endif

        <div class="mt-4 bg-base-200 p-4 rounded-lg">
            @php $selectedAddress = $addresses->find($selectedAddressId) @endphp
            @if($selectedAddress)
                <p class="mb-2">{{ $selectedAddress->address }}</p>
                <p class="mb-2">{{ $selectedAddress->city }}, {{ $selectedAddress->state }}</p>
                <p class="mb-2">{{ $selectedAddress->country }}, {{ $selectedAddress->zip_code }}</p>
                <p>{{ $selectedAddress->phone }}</p>
            @endif
        </div>
    </div>
</div>