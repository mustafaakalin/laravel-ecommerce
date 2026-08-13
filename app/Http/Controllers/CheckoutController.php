<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Iyzipay\Options;
use App\Models\Order;
use App\Models\Coupon;
use GuzzleHttp\Client;
use Iyzipay\Model\Buyer;
use App\Models\OrderItem;
use Iyzipay\Model\Locale;
use Iyzipay\Model\Address;
use Iyzipay\Model\Payment;
use Illuminate\Http\Request;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\PaymentChannel;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Iyzipay\Request\CreatePaymentRequest;

class CheckoutController extends Controller
{


    private function sendTelegramMessage($message)
    {
        $telegramBotToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        $client = new Client();

        try {
            $client->post("https://api.telegram.org/bot{$telegramBotToken}/sendMessage", [
                'form_params' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML', // Optional: For formatting
                ],
            ]);
        } catch (\Exception $e) {
            // Handle exceptions if needed
            \Log::error("Telegram API error: " . $e->getMessage());
        }
    }


    public function index()
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return redirect()->route('products.index')
                ->with('error', 'Sepetiniz boş');
        }

        $addresses = Auth::user()->addresses;
        $subtotal = $cart->getTotalPrice();
        $shipping = 00.00; // Example shipping cost, adjust as necessary
        $total = $subtotal + $shipping;

        return view('checkout.index', compact('cart', 'addresses', 'subtotal', 'shipping', 'total'));
    }



    public function store(Request $request)
    {
        // Validasyon
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|regex:/^.+@.+$/i',
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'zip_code' => 'required|string|max:20',
                'country' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'card_name' => 'required|string|max:60',
                'card_number' => 'required|numeric',
                'expire_month' => 'numeric|between:1,12',
                'expire_year' => 'numeric',
                'cvc' => 'numeric|digits_between:3,4',
                'coupon_code' => 'nullable|string|max:20',
                'totalinput' => 'nullable'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // Kullanıcının sepetini al
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->with('items.product')->first();

        if (!$cart) {
            return redirect()->back()->with('error', 'Sepetiniz boş.');
        }

        // Sepetteki toplam fiyatı hesapla
        $totalPrice = $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        // Kupon kodunu uygula
        $couponCode = $request->input('coupon_code');
        $discount = 0;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid()) {
                $discount = $coupon->applyDiscount($totalPrice);
                $totalPrice -= $discount;
            } else {
                return redirect()->back()->with('error', 'Geçersiz kupon kodu.');
            }
        }

        // Stok kontrolü
        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->quantity) {
                session()->flash('error', "{$item->product->name} için yeterli stok bulunmamaktadır.");
                return back();
            }
        }

        // Iyzico options ve payment request setup
        $options = new Options();
        $options->setApiKey(env('IYZIPAY_API_KEY'));
        $options->setSecretKey(env('IYZIPAY_SECRET_KEY'));
        $options->setBaseUrl(env('IYZIPAY_BASE_URL'));

        $paymentRequest = new CreatePaymentRequest();
        $paymentRequest->setLocale(Locale::TR);
        $paymentRequest->setConversationId(uniqid());
        $paymentRequest->setPrice($totalPrice);
        if ($request->input('totalinput')) {
            $paymentRequest->setPaidPrice($request->input('totalinput'));
        } else {
            $paymentRequest->setPaidPrice($totalPrice);
        }

        $paymentRequest->setCurrency('TRY');
        $paymentRequest->setPaymentChannel(PaymentChannel::WEB);
        $paymentRequest->setPaymentGroup(PaymentGroup::PRODUCT);

        // Kart bilgileri
        $paymentCard = new PaymentCard();
        $paymentCard->setCardHolderName($request->input('card_name'));
        $paymentCard->setCardNumber($request->input('card_number'));
        $paymentCard->setExpireMonth($request->input('expire_month'));
        $paymentCard->setExpireYear($request->input('expire_year'));
        $paymentCard->setCvc($request->input('cvc'));
        $paymentCard->setRegisterCard(0);
        $paymentRequest->setPaymentCard($paymentCard);

        // Buyer Information
        $buyer = new Buyer();
        $buyer->setId(uniqid());
        $buyer->setName($request->input('first_name'));
        $buyer->setSurname($request->input('last_name'));
        $buyer->setGsmNumber($request->input('phone') ?? '5555555555');
        $buyer->setEmail($request->input('email'));
        $buyer->setIdentityNumber(Auth::user()->identity_number ?? '11111111111');
        $buyer->setLastLoginDate(now()->format('Y-m-d H:i:s'));
        $buyer->setRegistrationDate(Auth::user()->created_at->format('Y-m-d H:i:s'));
        $buyer->setRegistrationAddress($request->input('address'));
        $buyer->setIp($request->ip());
        $buyer->setCity($request->input('city'));
        $buyer->setCountry($request->input('country'));
        $buyer->setZipCode($request->input('zip_code'));
        $paymentRequest->setBuyer($buyer);

        // Billing Address
        $billingAddress = new Address();
        $billingAddress->setContactName($request->input('first_name') . ' ' . $request->input('last_name'));
        $billingAddress->setCity($request->input('city'));
        $billingAddress->setCountry($request->input('country'));
        $billingAddress->setAddress($request->input('address'));
        $billingAddress->setZipCode($request->input('zip_code'));
        $paymentRequest->setBillingAddress($billingAddress);

        // Shipping Address
        $shippingAddress = new Address();
        $shippingAddress->setContactName($request->input('first_name') . ' ' . $request->input('last_name'));
        $shippingAddress->setCity($request->input('city'));
        $shippingAddress->setCountry($request->input('country'));
        $shippingAddress->setAddress($request->input('address'));
        $shippingAddress->setZipCode($request->input('zip_code'));
        $paymentRequest->setShippingAddress($shippingAddress);

        // // Sepetteki her ürün için sepet oluştur
        // $basketItems = [];
        // foreach ($cart->items as $cartItem) {
        //     $basketItem = new BasketItem();
        //     $basketItem->setId('BI_' . $cartItem->product->id);
        //     $basketItem->setName($cartItem->product->name);
        //     $basketItem->setCategory1($cartItem->product->category->name);
        //     $basketItem->setItemType(BasketItemType::PHYSICAL);
        //     $basketItem->setPrice($cartItem->product->price * $cartItem->quantity);
        //     $basketItems[] = $basketItem;
        // }

        // Sepetteki ilk ürün için sepet oluştur
        $basketItems = [];
        if ($cart->items->isNotEmpty()) {
            $cartItem = $cart->items->first();
            $basketItem = new BasketItem();
            $basketItem->setId('BI_' . $cartItem->product->id);
            $basketItem->setName($cartItem->product->name);
            $basketItem->setCategory1($cartItem->product->category->name);
            $basketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
            $basketItem->setPrice($cartItem->product->price * $cartItem->quantity);
            $basketItems[] = $basketItem;
        }

        $paymentRequest->setBasketItems($basketItems);

        // Perform payment
        $payment = Payment::create($paymentRequest, $options);

        if ($payment->getStatus() === 'success') {
            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'total_price' => $totalPrice,
                'status' => 'pending',  // Sipariş durumu
                'discount' => $discount, // Kupon indirimi
            ]);

            // Create order items and update stock
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                // Update stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Clear cart
            $cart->items()->delete();
            $cart->delete();

            // Sipariş onay e-postasını gönder
            Mail::to($request->input('email'))->send(new OrderConfirmationMail($order));

            // Telegram'a bildirim gönder
            $telegramMessage = config('app.name') . "!\n";
            $telegramMessage .= "Yeni sipariş alındı!\n";
            $telegramMessage .= "Sipariş ID: " . $order->id . "\n";
            $telegramMessage .= "Toplam Fiyat: " . $totalPrice . " ₺\n";
            $telegramMessage .= "Kullanıcı ID: " . $userId . "\n";
            $telegramMessage .= "Durum: Ödendi.\n";
            $this->sendTelegramMessage($telegramMessage); // Telegram bildirimi gönder

            return redirect()->route('home')->with('success', 'Siparişiniz başarıyla oluşturuldu.');
        } else {
            // Ödeme hatası
            $errorMessage = $payment->getErrorMessage();
            return redirect()->back()->with('error', $errorMessage); // Başarısız durumda yönlendirme
        }
    }

    public function applyCoupon(Request $request)
    {
        $couponCode = $request->input('coupon_code');
        $coupon = Coupon::where('code', $couponCode)->first();

        if (!$coupon || !$coupon->isValid()) {
            return response()->json(['error' => 'Invalid coupon code'], 422);
        }

        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->with('items.product')->first();

        $totalPrice = $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $discount = $coupon->applyDiscount($totalPrice);
        $finalPrice = $totalPrice - $discount;

        return response()->json([
            'discount' => $discount,
            'final_price' => $finalPrice,
        ]);
    }
}
