<div class="space-y-4">
    {{-- Order Info --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <span class="font-medium">Müşteri:</span> {{ $order->user->name }}
        </div>
        <div>
            <span class="font-medium">Tarih:</span> {{ $order->created_at->format('d/m/Y H:i') }}
        </div>
        <div>
            <span class="font-medium">Durum:</span>
            <span @class([ 'px-2 py-1 rounded-full text-xs' , 'border-2 border-current'=> $order->status === 'pending',
                'border-2 border-current bg-warning-100' => $order->status === 'shipping',
                'border-2 border-current bg-success-100' => $order->status === 'completed',
                ])>
                {{ match($order->status) {
                'pending' => 'Bekliyor',
                'shipping' => 'Kargoda',
                'completed' => 'Tamamlandı',
                default => ucfirst($order->status)
                } }}
            </span>
        </div>
        <div>
            <span class="font-medium">Adress:</span>
            @php
            $address = $order->user->addresses->find($order->address_id);
            @endphp

            {{-- Display address details --}}
            @if($address)
            <div class="space-y-2 border-2 border-current bg-warning-100 rounded-lg m-2 p-2">
                <div class="font-medium">{{ $address->title }}</div>
                <div>{{ $address->first_name }} {{ $address->last_name }}</div>
                <div>{{ $address->phone }}</div>
                <div>{{ $address->address }}</div>
                <div>{{ $address->city }}, {{ $address->state }} {{ $address->zip_code }}</div>
                <div>{{ $address->country }}</div>
            </div>
            @else
            <div class="text-gray-500">Adres bulunamadı</div>
            @endif
        </div>
    </div>

    {{-- Order Items --}}
    <div class="border rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-base-50">
                <tr>
                    <th class="px-4 py-2 text-left">Ürün</th>
                    <th class="px-4 py-2 text-right">Adet</th>
                    <th class="px-4 py-2 text-right">Birim Fiyat</th>
                    <th class="px-4 py-2 text-right">Toplam</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($order->items as $item)
                <tr>
                    <td class="px-4 py-2">{{ $item->product->name }}</td>
                    <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                    <td class="px-4 py-2 text-right">{{ $item->price, 'TRY' }}</td>
                    <td class="px-4 py-2 text-right">{{ $item->quantity * $item->price, 'TRY' }}</td>
                </tr>
                @endforeach
                <tr class="bg-base-50 font-medium">
                    <td colspan="3" class="px-4 py-2 text-right">Toplam:</td>
                    <td class="px-4 py-2 text-right">{{ $order->total_price, 'TRY' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Shipment Info --}}
    @if($order->shipment)
    <div class="border rounded-xl p-4 space-y-2">
        <h3 class="font-medium">Kargo Bilgileri</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <span class="font-medium">Kargo Firması:</span> {{ $order->shipment->carrier }}
            </div>
            <div>
                <span class="font-medium">Takip No:</span> {{ $order->shipment->tracking_number }}
            </div>
            <div>
                <span class="font-medium">Kargoya Verilme:</span> {{ $order->shipment->shipped_at->format('d/m/Y H:i')
                }}
            </div>
        </div>
    </div>
    @endif
</div>