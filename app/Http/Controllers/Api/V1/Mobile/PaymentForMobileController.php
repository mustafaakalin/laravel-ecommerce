<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\PaymentForMobileResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Soldout;
use App\Models\Coupon;
use App\Models\SiteSetting;
use App\Models\ShipmentDiscount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Iyzipay\Options;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\Address;
use Iyzipay\Model\Payment;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Request\CreatePaymentRequest;
use GuzzleHttp\Client;

class PaymentForMobileController extends Controller
{
    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:iyzico,stripe,paypal',
            'card_name' => 'required_if:payment_method,iyzico',
            'card_number' => 'required_if:payment_method,iyzico',
            'expire_month' => 'required_if:payment_method,iyzico',
            'expire_year' => 'required_if:payment_method,iyzico',
            'cvc' => 'required_if:payment_method,iyzico',
            'address_id' => 'required|exists:addresses,id',
        ]);

        $paymentMethod = $request->input('payment_method');
        $cart = Cart::where('user_id', Auth::id())->firstOrFail();
        $totalPrice = $cart->calculateTotalPrice();
        $discount = 0; // Implement coupon logic here if needed

        try {
            DB::beginTransaction();

            if ($paymentMethod === 'iyzico') {
                $paymentResponse = $this->processIyzicoPayment($request, $cart, $totalPrice, $discount);
            } elseif ($paymentMethod === 'stripe') {
                $paymentResponse = $this->processStripePayment($request, $cart, $totalPrice, $discount);
            } elseif ($paymentMethod === 'paypal') {
                $paymentResponse = $this->processPaypalPayment($request, $cart, $totalPrice, $discount);
            } else {
                throw new \Exception('Invalid payment method');
            }

            DB::commit();

            return new PaymentForMobileResource($paymentResponse);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment failed: ' . $e->getMessage(), [
                'payment_method' => $paymentMethod,
                'user_id' => Auth::id(),
                'cart_id' => $cart->id ?? null,
            ]);

            return response()->json([
                'error' => 'Payment failed',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function processIyzicoPayment(Request $request, Cart $cart, $totalPrice, $discount)
    {
        try {
            // Initialize iyzico options
            $options = new Options();
            $options->setApiKey(env('IYZIPAY_API_KEY'));
            $options->setSecretKey(env('IYZIPAY_SECRET_KEY'));
            $options->setBaseUrl(env('IYZIPAY_BASE_URL'));

            // Calculate final price with all discounts
            $finalPrice = 0;
            $basketItems = [];

            foreach ($cart->items as $item) {
                // 1. Start with original price
                $basePrice = $item->getOriginalPrice();
                $currentPrice = $basePrice;

                // 2. Apply product's own discount if exists
                if ($item->product->discount > 0) {
                    $productDiscountAmount = $basePrice * ($item->product->discount / 100);
                    $currentPrice = $basePrice - $productDiscountAmount;

                    Log::info("Product discount applied:", [
                        'product_id' => $item->product->id,
                        'original_price' => $basePrice,
                        'discount_percentage' => $item->product->discount,
                        'price_after_discount' => $currentPrice
                    ]);
                }

                // 3. Apply campaign discount if product is in campaign
                if ($campaign = $item->campaign()) {
                    if ($campaign->discount_type === 'percentage') {
                        $campaignDiscount = $currentPrice * ($campaign->discount_value / 100);
                        $currentPrice -= $campaignDiscount;
                    } else {
                        $campaignDiscount = min($campaign->discount_value, $currentPrice);
                        $currentPrice -= $campaignDiscount;
                    }

                    Log::info("Campaign discount applied:", [
                        'product_id' => $item->product->id,
                        'campaign_id' => $campaign->id,
                        'price_before_campaign' => $currentPrice + $campaignDiscount,
                        'price_after_campaign' => $currentPrice
                    ]);
                }





                // Multiply by quantity
                $finalPrice += $currentPrice * $item->quantity;

                // Create basket item
                $basketItem = new BasketItem();
                $basketItem->setId($item->product->id);
                $basketItem->setName($item->product->name);
                $basketItem->setCategory1($item->product->category?->name ?? 'General');
                $basketItem->setItemType('PHYSICAL');
                $basketItem->setPrice($basePrice * $item->quantity); // Original price for iyzico
                $basketItems[] = $basketItem;
            }

            // 4. Apply coupon discount if exists
            // if ($cart->coupon && $cart->coupon->isValid()) {
            //     $couponDiscount = $cart->coupon->discount_type === 'fixed'
            //         ? min($cart->coupon->discount_value, $finalPrice)
            //         : $finalPrice * ($cart->coupon->discount_value / 100);

            //     $finalPrice = max(0, $finalPrice - $couponDiscount);

            //     Log::info("Coupon discount applied:", [
            //         'coupon_code' => $cart->coupon->code,
            //         'discount_amount' => $couponDiscount,
            //         'price_after_coupon' => $finalPrice
            //     ]);
            // }

            // 4. Kupon kontrolü ve indirimi
            if ($cart->coupon_id) {
                try {
                    $coupon = Coupon::findOrFail($cart->coupon_id);

                    // Detaylı kupon geçerlilik kontrolleri
                    if (!$coupon->is_active) {
                        throw new \Exception('Kupon artık aktif değil.');
                    }

                    if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
                        throw new \Exception('Kupon henüz aktif değil.');
                    }

                    if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                        throw new \Exception('Kupon süresi dolmuş.');
                    }

                    if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                        throw new \Exception('Kupon kullanım limiti dolmuş.');
                    }

                    // Minimum sepet tutarı kontrolü
                    if ($coupon->minimum_amount && $finalPrice < $coupon->minimum_amount) {
                        throw new \Exception("Kupon için minimum sepet tutarı: {$coupon->minimum_amount} TL");
                    }

                    // Kupon indirimini uygula
                    $couponDiscount = 0;
                    if ($coupon->type === 'fixed') {
                        $couponDiscount = min($coupon->value, $finalPrice);
                    } else { // percentage
                        $couponDiscount = ($finalPrice * $coupon->value) / 100;
                    }

                    // Final fiyattan kupon indirimini düş
                    $finalPrice = max(0, $finalPrice - $couponDiscount);

                    Log::info("Coupon discount applied:", [
                        'cart_id' => $cart->id,
                        'coupon_id' => $coupon->id,
                        'coupon_code' => $coupon->code,
                        'discount_type' => $coupon->type,
                        'discount_value' => $coupon->value,
                        'discount_amount' => $couponDiscount,
                        'price_before_coupon' => $finalPrice + $couponDiscount,
                        'price_after_coupon' => $finalPrice
                    ]);

                    // Kupon kullanım sayısını artır
                    $coupon->increment('used_count');

                } catch (\Exception $e) {
                    Log::error('Coupon application failed:', [
                        'cart_id' => $cart->id,
                        'coupon_id' => $cart->coupon_id,
                        'error' => $e->getMessage()
                    ]);
                    // Kupon hatası durumunda işlemi durdurmadan devam et
                    // Sadece loglama yap ve kullanıcıya bildir
                }
            }

            // 5. Apply shipping cost
            $shipmentDiscountPrice = ShipmentDiscount::first()->price ?? 0;
            $shipmentPrice = SiteSetting::first()->site_shipment_price ?? 0;

            if ($finalPrice < $shipmentDiscountPrice) {
                $finalPrice += $shipmentPrice;
                Log::info("Shipping cost added:", [
                    'shipping_cost' => $shipmentPrice,
                    'final_total' => $finalPrice
                ]);
            }

            // Create payment request
            $createPaymentRequest = new CreatePaymentRequest();
            $createPaymentRequest->setLocale('tr');
            $createPaymentRequest->setConversationId(uniqid());
            $createPaymentRequest->setPrice($cart->items->sum(fn($item) => $item->getOriginalPrice() * $item->quantity));
            $createPaymentRequest->setPaidPrice(round($finalPrice, 2));
            $createPaymentRequest->setBasketItems($basketItems);

            // Set other payment details...
            // ... (remaining code for buyer, shipping address, etc.)


            // Set payment card details
            $paymentCard = new PaymentCard();
            $paymentCard->setCardHolderName($request->input('card_name'));
            $paymentCard->setCardNumber($request->input('card_number'));
            $paymentCard->setExpireMonth($request->input('expire_month'));
            $paymentCard->setExpireYear($request->input('expire_year'));
            $paymentCard->setCvc($request->input('cvc'));
            $paymentCard->setRegisterCard(0);
            $createPaymentRequest->setPaymentCard($paymentCard);

            // Set buyer details
            $buyer = new Buyer();
            $buyer->setId(Auth::id());
            $buyer->setName(Auth::user()->name);
            $buyer->setSurname(Auth::user()->surname ?? 'Not Set');
            $buyer->setEmail(Auth::user()->email);
            $buyer->setIdentityNumber(Auth::user()->identity_number ?? '11111111111');
            $buyer->setRegistrationAddress(Auth::user()->addresses()->find($request->input('address_id'))->address);
            $buyer->setCity(Auth::user()->addresses()->find($request->input('address_id'))->city);
            $buyer->setCountry(Auth::user()->addresses()->find($request->input('address_id'))->country ?? 'Turkey');
            $createPaymentRequest->setBuyer($buyer);

            // Set shipping address
            $shippingAddress = new Address();
            $shippingAddress->setContactName(Auth::user()->name);
            $shippingAddress->setCity(Auth::user()->addresses()->find($request->input('address_id'))->city);
            $shippingAddress->setCountry(Auth::user()->addresses()->find($request->input('address_id'))->country ?? 'Turkey');
            $shippingAddress->setAddress(Auth::user()->addresses()->find($request->input('address_id'))->address);
            $createPaymentRequest->setShippingAddress($shippingAddress);

            // Set billing address
            $billingAddress = new Address();
            $billingAddress->setContactName(Auth::user()->name);
            $billingAddress->setCity(Auth::user()->addresses()->find($request->input('address_id'))->city);
            $billingAddress->setCountry(Auth::user()->addresses()->find($request->input('address_id'))->country ?? 'Turkey');
            $billingAddress->setAddress(Auth::user()->addresses()->find($request->input('address_id'))->address);
            $createPaymentRequest->setBillingAddress($billingAddress);



            // Make payment request
            $payment = Payment::create($createPaymentRequest, $options);

            if ($payment->getStatus() === 'success') {
                // Create order with final calculated price
                $order = $this->createOrder($cart, $finalPrice, 0, $request->input('address_id'), 'iyzico', $payment->getPaymentId());

                return [
                    'status' => 'success',
                    'message' => 'Payment successful',
                    'order_id' => $order->id,
                    'final_price' => $finalPrice
                ];
            } else {
                throw new \Exception('Iyzico payment failed: ' . $payment->getErrorMessage());
            }

        } catch (\Exception $e) {
            Log::error('Payment processing error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Payment processing error: ' . $e->getMessage());
        }
    }

    private function processStripePayment(Request $request, Cart $cart, $totalPrice, $discount)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $amount = (int) (($totalPrice - $discount) * 100); // Convert to cents and ensure integer

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'user_id' => auth()->id(),
                    'cart_id' => $cart->id
                ]
            ]);

            // Create order
            $order = $this->createOrder($cart, $totalPrice, $discount, $request->input('address_id'), 'stripe', $paymentIntent->id);

            return [
                'status' => 'success',
                'message' => 'Stripe payment intent created',
                'client_secret' => $paymentIntent->client_secret,
                'order_id' => $order->id,
            ];
        } catch (\Exception $e) {
            throw new \Exception('Stripe payment intent creation failed: ' . $e->getMessage());
        }
    }

    private function processPaypalPayment(Request $request, Cart $cart, $totalPrice, $discount)
    {
        // Implement PayPal payment logic here
        // This is a placeholder
        throw new \Exception('PayPal payment is not implemented yet');
    }

    private function createOrder(Cart $cart, $totalPrice, $discount, $addressId, $paymentMethod, $paymentId)
    {
        $user = Auth::user();

        $order = new Order();
        $order->user_id = $user->id;
        $order->address_id = $addressId;
        $order->total_price = $totalPrice - $discount;
        $order->status = 'paid';
        $order->payment_method = $paymentMethod;
        $order->payment_id = $paymentId;
        $order->save();

        foreach ($cart->items as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item->product->id;
            $orderItem->quantity = $item->quantity;
            $orderItem->price = $item->product->getCurrentPrice();
            $orderItem->save();

            // Mark product as sold
            $soldoutItem = new Soldout();
            $soldoutItem->user_id = $user->id;
            $soldoutItem->product_id = $item->product->id;
            $soldoutItem->order_id = $order->id;
            $soldoutItem->is_sold = true;
            $soldoutItem->save();
        }

        // Clear cart
        $cart->items()->delete();
        $cart->delete();

        // Update UI components
        // $this->dispatch('updateCart');

        // Sipariş onay e-postasını gönder
        Mail::to(Auth::user()->email)->send(new OrderConfirmationMail($order));

        // Telegram'a bildirim gönder
        $telegramMessage = config('app.name') . "!\n";
        $telegramMessage .= "Yeni sipariş alındı!\n";
        $telegramMessage .= "Sipariş ID: " . $order->id . "\n";
        $telegramMessage .= "Toplam Fiyat: " . $totalPrice - $discount . " ₺\n";
        $telegramMessage .= "Kullanıcı ID: " . Auth::user()->id . "\n";
        $telegramMessage .= "Durum: Ödendi.\n";
        $this->sendTelegramMessage($telegramMessage); // Telegram bildirimi gönder

        return $order;
    }

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
            Log::error("Telegram API error: " . $e->getMessage());
        }
    }
}
