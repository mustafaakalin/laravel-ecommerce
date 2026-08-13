@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
@livewire('checkout-component')
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
            const addressSelector = document.getElementById('addressSelector');
            if (addressSelector) {
                updateAddressDisplay(addressSelector.options[addressSelector.selectedIndex]);

                addressSelector.addEventListener('change', function() {
                    updateAddressDisplay(this.options[this.selectedIndex]);
                });
            }

            // Apply Coupon Logic
            document.getElementById('applyCoupon').addEventListener('click', function() {
                const couponCode = document.querySelector('input[name="coupon_code"]').value;
                const subtotal = parseFloat(document.getElementById('subtotal').innerText.replace('₺', ''));

                axios.post('{{ route('checkout.apply-coupon') }}', {
                    coupon_code: couponCode
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => {
                        const discount = response.data.discount;
                        const finalPrice = response.data.final_price;

                        document.getElementById('discount').innerText = discount.toFixed(2) + '₺';
                        document.getElementById('total').innerText = finalPrice.toFixed(2) + '₺';
                        document.getElementById('totalinput').value = finalPrice.toFixed(2);

                        const couponMessage = document.getElementById('couponMessage');
                        couponMessage.innerText = 'Coupon applied successfully!';
                        couponMessage.classList.remove('hidden', 'text-error');
                        couponMessage.classList.add('text-success');
                    })
                    .catch(error => {
                        const couponMessage = document.getElementById('couponMessage');
                        couponMessage.innerText = error.response.data.error;
                        couponMessage.classList.remove('hidden', 'text-success');
                        couponMessage.classList.add('text-error');
                    });
            });
        });

        function updateAddressDisplay(selectedOption) {
            // Update hidden inputs
            document.getElementById('address_id').value = selectedOption.value;
            document.getElementById('hidden_address').value = selectedOption.dataset.address;
            document.getElementById('hidden_city').value = selectedOption.dataset.city;
            document.getElementById('hidden_state').value = selectedOption.dataset.state;
            document.getElementById('hidden_zip').value = selectedOption.dataset.zip;
            document.getElementById('hidden_country').value = selectedOption.dataset.country;
            document.getElementById('hidden_phone').value = selectedOption.dataset.phone;

            // Update display elements
            document.getElementById('display_address').innerText = selectedOption.dataset.address;
            document.getElementById('display_city_state').innerText = `${selectedOption.dataset.city}, ${selectedOption.dataset.state}`;
            document.getElementById('display_country_zip').innerText = `${selectedOption.dataset.country}, ${selectedOption.dataset.zip}`;
            document.getElementById('display_phone').innerText = selectedOption.dataset.phone;
        }
</script>
@endpush
@push('styles')


<style>
    .flip-card {
        perspective: 1000px;
        transition: transform 0.3s;
    }

    .flip-card:hover {
        transform: scale(1.05) translateY(-10px);
    }

    .flip-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        text-align: center;
        transition: transform 0.8s;
        transform-style: preserve-3d;
        cursor: pointer;
    }

    .flip-card-inner.is-flipped {
        transform: rotateY(180deg);
    }

    .flip-card-front,
    .flip-card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
    }

    .flip-card-back {
        transform: rotateY(180deg);
    }

    /* Hover effect when card is flipped */
    .flip-card:hover .flip-card-inner.is-flipped {
        transform: rotateY(180deg) scale(1.05) translateY(-10px);
    }
</style>

@endpush
