<!DOCTYPE html>
<html>
<head>
    <title>Sipariş Onayı</title>
</head>
<body>
    <h1>Merhaba, {{ $order->user->name }}!</h1>
    <p>Teşekkür ederiz, siparişiniz alınmıştır. İşte sipariş detaylarınız:</p>

    <h2>Sipariş No: {{ $order->id }}</h2>
    <p>Toplam Tutar: {{ $order->total_price }} TL</p>
    <p>Durum: {{ $order->status }}</p>

    <h3>Ürünler:</h3>
    <ul>
        @foreach($order->items as $item)
            <li>{{ $item->product->name }} (x{{ $item->quantity }}) - {{ $item->price }} TL</li>
        @endforeach
    </ul>

    <p>Herhangi bir sorunuz varsa bizimle iletişime geçmekten çekinmeyin.</p>

    <p>İyi günler dileriz!</p>
</body>
</html>
