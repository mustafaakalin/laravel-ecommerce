<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Cart as CartModel;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShipmentDiscount;
use App\Models\SiteSetting;
use App\Models\Soldout;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\Attributes\On;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use GuzzleHttp\Client;
use Iyzipay\Options;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\Address as IyziAddress;
use Iyzipay\Model\Payment;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Request\CreatePaymentRequest;
use App\Livewire\Cart as CartComponent;
use App\Livewire\CartCounter;
use Iyzipay\Model\Address as IyzicoAddress;

class CheckoutComponent extends Component
{
    // Properties
    public $cart;
    public $couponCode = '';
    public $couponDiscount = 0;
    public $couponDiscountType = 'percentage' || 'fixed';
    public $couponDiscountValue = 0;
    public $discount = 0;
    public $totalPrice;
    public $paymentMethod = 'iyzico';
    public $useraddresses;
    public $selectedAddress;
    public $card_name;
    public $card_number;
    public $expire_month;
    public $expire_year;
    public $cvc;

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

    public function mount()
    {
        $user = Auth::user();
        $this->useraddresses = auth()->user()->addresses;
        $this->selectedAddress = $this->useraddresses->where('is_default', true)->first()?->id;

        $this->cart = $user->cart ?? CartModel::create(['user_id' => $user->id]);
        $this->totalPrice = $this->cart ? $this->cart->calculateTotalPrice() ?? 0 : 0;

        if ($this->paymentMethod === 'stripe') {
            $this->createStripePaymentIntent();
        }
    }

    #[On('updateCart')]
    public function updateCart()
    {
        $this->cart = Auth::user()->cart ?? CartModel::create(['user_id' => auth()->id()]);
        $this->totalPrice = $this->cart ? $this->cart->calculateTotalPrice() ?? 0 : 0;

        // Fix: Use correct component aliases
        $this->dispatch('cartUpdated')->to(CartCounter::class, CartComponent::class);
    }

    public function updatedPaymentMethod($value)
    {
        if ($value === 'stripe') {
            $this->createStripePaymentIntent();
        }
    }

    protected function createStripePaymentIntent()
    {
        try {
            // Ensure we have a valid total amount
            if (!$this->totalPrice || $this->totalPrice <= 0) {
                throw new \Exception('Invalid order amount');
            }

            $amount = (int) (($this->totalPrice - $this->discount) * 100); // Convert to cents and ensure integer

            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'user_id' => auth()->id(),
                    'cart_id' => $this->cart->id
                ]
            ]);

            // Log successful intent creation
            Log::info('Stripe payment intent created:', [
                'amount' => $amount,
                'currency' => 'usd',
                'client_secret' => $paymentIntent->client_secret
            ]);

            $this->dispatch('stripePaymentIntent', clientSecret: $paymentIntent->client_secret);
        } catch (\Exception $e) {
            Log::error('Stripe payment intent creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'amount' => $this->totalPrice ?? 'null',
                'discount' => $this->discount ?? 0
            ]);
            $this->dispatch('showToast', message: 'Payment initialization failed: ' . $e->getMessage(), type: 'error');
        }
    }


    public function applyCoupon()
    {
        try {
            if (empty($this->couponCode)) {
                throw new \Exception('Lütfen bir kupon kodu girin.');
            }

            $coupon = Coupon::where('code', $this->couponCode)->first();
            $this->couponDiscountValue = $coupon->value;
            $this->couponDiscountType = $coupon->type;

            if (!$coupon) {
                throw new \Exception('Kupon bulunamadı.');
            }

            if (!$coupon->isValid()) {
                throw new \Exception('Bu kupon artık geçerli değil.');
            }

            // Son fiyatların toplamını al (ürün ve kampanya indirimleri uygulanmış hali)
            $subtotal = $this->cart->items->sum(function ($item) {
                return $item->getTotalPrice();
            });

            if ($subtotal <= 0) {
                throw new \Exception('Sepetinizde ürün bulunmuyor.');
            }

            // Kupon değerlerini kontrol et ve logla
            Log::info('Coupon details:', [
                'coupon_id' => $coupon->id,
                'coupon_code' => $coupon->code,
                'discount_type' => $coupon->type,
                'discount_value' => $coupon->value,
                'subtotal' => $subtotal
            ]);

            // Kupon indirimini hesapla
            if ($coupon->type === 'fixed') {
                // Sabit indirim için
                $discountAmount = (float) $coupon->value;
                $this->discount = min($discountAmount, $subtotal);

                Log::info('Fixed coupon calculation:', [
                    'discount_value' => $discountAmount,
                    'subtotal' => $subtotal,
                    'applied_discount' => $this->discount
                ]);
            } else {
                // Yüzdelik indirim için
                $percentage = (float) $coupon->value;
                if ($percentage <= 0 || $percentage > 100) {
                    throw new \Exception('Geçersiz kupon yüzdesi.');
                }

                $this->discount = round(($subtotal * $percentage) / 100, 2);

                Log::info('Percentage coupon calculation:', [
                    'percentage' => $percentage,
                    'subtotal' => $subtotal,
                    'applied_discount' => $this->discount
                ]);
            }

            // Değerleri güncelle
            $this->couponDiscount = $this->discount;
            $this->cart->update([
                'coupon_id' => $coupon->id,
                // 'coupon_discount' => $this->discount
            ]);

            // Final fiyatı güncelle
            $this->totalPrice = max(0, $subtotal - $this->discount);

            // Başarılı mesajı gönder
            $this->dispatch(
                'showToast',
                message: 'Kupon başarıyla uygulandı: -'
                . number_format($this->discount, 2) . ' ₺ ('
                . ($this->couponDiscountType === 'percentage'
                    ? $this->couponDiscountValue . '%'
                    : number_format($this->couponDiscountValue, 2) . '₺')
                . ')',
                type: 'success'
            );

        } catch (\Exception $e) {
            // Hata durumunda değerleri sıfırla
            $this->couponCode = '';
            $this->discount = 0;
            $this->couponDiscount = 0;

            Log::error('Coupon application failed:', [
                'error' => $e->getMessage(),
                'coupon_code' => $this->couponCode
            ]);

            $this->dispatch('showToast', message: $e->getMessage(), type: 'error');
        }
    }

    public function placeOrder()
    {
        if ($this->paymentMethod === 'stripe') {
            try {
                if (!$this->totalPrice || $this->totalPrice <= 0) {
                    throw new \Exception('Invalid order amount');
                }

                Stripe::setApiKey(config('services.stripe.secret'));

                // Create payment intent only with amount and metadata
                $paymentIntent = PaymentIntent::create([
                    'amount' => (int) (($this->totalPrice - $this->discount) * 100),
                    'currency' => 'usd',
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                    'metadata' => [
                        'user_id' => auth()->id(),
                        'cart_id' => $this->cart->id
                    ]
                ]);

                // Return the client secret to the frontend
                $this->dispatch(
                    'stripePaymentIntent',
                    clientSecret: $paymentIntent->client_secret
                );

            } catch (\Exception $e) {
                Log::error('Stripe payment intent creation failed:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $this->dispatch(
                    'showToast',
                    message: 'Payment initialization failed: ' . $e->getMessage(),
                    type: 'error'
                );
            }
            return;
        }

        // ...existing iyzico payment code...
        if ($this->paymentMethod === 'iyzico') {
            // Process payment with iyzico
            // ...

            try {
                // Initialize iyzico options
                $options = new Options();
                $options->setApiKey(env('IYZIPAY_API_KEY'));
                $options->setSecretKey(env('IYZIPAY_SECRET_KEY'));
                $options->setBaseUrl(env('IYZIPAY_BASE_URL'));

                // Calculate total price before any discounts
                $originalTotal = $this->cart->items->sum(fn($item) => $item->getOriginalPrice() * $item->quantity);

                // Create payment request
                $request = new CreatePaymentRequest();
                $request->setLocale('tr');
                $request->setConversationId(uniqid());

                // Set original price (before discounts)
                $request->setPrice($originalTotal);

                // Calculate final price after all discounts
                $finalPrice = $this->cart->items->sum(function ($item) {
                    return $item->getDiscountedPrice() * $item->quantity;
                });

                // Apply coupon discount if exists
                $finalPrice = max(0, $finalPrice - $this->discount);
                $shipmentPrice = \App\Models\SiteSetting::first()->site_shipment_price;
                if ($shipmentPrice) {
                    // Shipment discount
                    if ($finalPrice >= \App\Models\ShipmentDiscount::first()->price) {
                        $shipmentPrice = 0;
                    }
                    $finalPrice = max(0, $finalPrice + $shipmentPrice);
                }
                // Set the final price to be paid
                $request->setPaidPrice($finalPrice);

                // Set payment card details
                $paymentCard = new PaymentCard();
                $paymentCard->setCardHolderName($this->card_name);
                $paymentCard->setCardNumber($this->card_number);
                $paymentCard->setExpireMonth($this->expire_month);
                $paymentCard->setExpireYear($this->expire_year);
                $paymentCard->setCvc($this->cvc);
                $paymentCard->setRegisterCard(0);
                $request->setPaymentCard($paymentCard);

                // Set buyer details
                $buyer = new Buyer();
                $buyer->setId(auth()->id());
                $buyer->setName(auth()->user()->name);
                $buyer->setSurname(auth()->user()->surname ?? 'Not Set');
                $buyer->setEmail(auth()->user()->email);
                $buyer->setIdentityNumber(auth()->user()->identity_number ?? '11111111111');
                $buyer->setRegistrationAddress($this->useraddresses->find($this->selectedAddress)->address);
                $buyer->setCity($this->useraddresses->find($this->selectedAddress)->city);
                $buyer->setCountry($this->useraddresses->find($this->selectedAddress)->country ?? 'Turkey');
                $request->setBuyer($buyer);


                // Shipping address kısmını güncelleyin
                $shippingAddress = new IyzicoAddress();  // Iyzico'nun Address sınıfını kullanın
                $shippingAddress->setContactName(auth()->user()->name);
                $shippingAddress->setCity($this->useraddresses->find($this->selectedAddress)->city);
                $shippingAddress->setCountry($this->useraddresses->find($this->selectedAddress)->country ?? 'Turkey');
                $shippingAddress->setAddress($this->useraddresses->find($this->selectedAddress)->address);
                $request->setShippingAddress($shippingAddress);

                // Billing address kısmını güncelleyin
                $billingAddress = new IyzicoAddress();  // Iyzico'nun Address sınıfını kullanın
                $billingAddress->setContactName(auth()->user()->name);
                $billingAddress->setCity($this->useraddresses->find($this->selectedAddress)->city);
                $billingAddress->setCountry($this->useraddresses->find($this->selectedAddress)->country ?? 'Turkey');
                $billingAddress->setAddress($this->useraddresses->find($this->selectedAddress)->address);
                $request->setBillingAddress($billingAddress);

                // Create basket items with proper pricing
                $basketItems = [];
                foreach ($this->cart->items as $item) {
                    $basketItem = new BasketItem();
                    $basketItem->setId($item->product->id);
                    $basketItem->setName($item->product->name);
                    $basketItem->setCategory1($item->product->category?->name ?? 'General');
                    $basketItem->setItemType('PHYSICAL');

                    // Set original price of item
                    $basketItem->setPrice($item->getOriginalPrice() * $item->quantity);

                    // Get discount info for logging
                    $discountInfo = $item->getDiscountInfo();
                    if (!empty($discountInfo)) {
                        $itemDiscountDetails = [];
                        if (isset($discountInfo['product'])) {
                            $itemDiscountDetails[] = "Product discount: {$discountInfo['product']['value']}%";
                        }
                        if (isset($discountInfo['campaign'])) {
                            $itemDiscountDetails[] = "Campaign: {$discountInfo['campaign']['name']} - " .
                                ($discountInfo['campaign']['type'] === 'percentage' ?
                                    "{$discountInfo['campaign']['value']}%" :
                                    "{$discountInfo['campaign']['value']}₺");
                        }

                        // Log discount details
                        Log::info("Item {$item->product->name} discounts:", [
                            'original_price' => $item->getOriginalPrice(),
                            'final_price' => $item->getDiscountedPrice(),
                            'discounts' => $itemDiscountDetails
                        ]);
                    }

                    $basketItems[] = $basketItem;
                }
                $request->setBasketItems($basketItems);

                // Debug log payment details
                Log::info('Payment Request:', [
                    'original_total' => $originalTotal,
                    'discounted_total' => $finalPrice,
                    'coupon_discount' => $this->discount,
                    'final_paid_price' => $finalPrice,
                    'status' => 'Initiating payment'
                ]);

                // Make payment request
                $payment = Payment::create($request, $options);




                // Debug log response
                Log::info('Payment Response:', [
                    'status' => $payment->getStatus(),
                    'errorCode' => $payment->getErrorCode(),
                    'errorMessage' => $payment->getErrorMessage(),
                    'paymentId' => $payment->getPaymentId()
                ]);




                if ($payment->getStatus() === 'success') {
                    // Continue with order creation
                    // ...existing order creation code...

                    try {
                        DB::beginTransaction();
                        $this->dispatch('showToast', message: 'iyzico success Order placed successfully.', type: 'success');

                        // Create order logic
                        $user = Auth::user();


                        // Her ürünün Son Fiyat'larının toplamı (ürün ve kampanya indirimleri dahil)
                        $finalTotal = $this->cart->items->sum(function ($item) {
                            return $item->getTotalPrice();
                        });

                        // Kargo ücreti kontrolü
                        $shipmentPrice = SiteSetting::first()->site_shipment_price ?? 0;
                        $shipmentDiscountPrice = ShipmentDiscount::first()->price ?? 0;

                        // Kupon indirimi (wire:model ile senkronize)
                        if ($this->couponDiscount > 0) {
                            // Kupon indirimi Nihai Toplamdan büyük olamaz
                            $couponDiscount = min($this->couponDiscount, $finalTotal);
                            $finalTotal = max(0, $finalTotal - $couponDiscount);
                        }

                        // En son kargo ücreti eklenir
                        if ($finalTotal < $shipmentDiscountPrice) {
                            $finalTotal += $shipmentPrice;
                        }


                        $order = new Order();
                        $order->user_id = $user->id;
                        $order->address_id = $this->useraddresses->find($this->selectedAddress)->id;
                        $order->total_price = round($finalTotal, 2);
                        $order->status = 'paid';
                        $order->payment_method = 'iyzico';
                        $order->payment_id = $payment->getPaymentId();
                        $order->save();

                        foreach ($this->cart->items as $item) {
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
                        $this->cart->items()->delete();
                        $this->cart->delete();

                        DB::commit();


                        // Update UI components
                        $this->dispatch('updateCart');

                        // Set success message
                        session()->flash('success', 'session flash Order placed successfully.');
                        $this->dispatch('showToast', message: 'dispatch Order placed successfully.', type: 'success');


                        // Sipariş onay e-postasını gönder
                        Mail::to(auth()->user()->email)->send(new OrderConfirmationMail($order));

                        // Telegram'a bildirim gönder
                        $telegramMessage = config('app.name') . "!\n";
                        $telegramMessage .= "Yeni sipariş alındı!\n";
                        $telegramMessage .= "Sipariş ID: " . $order->id . "\n";
                        $telegramMessage .= "Toplam Fiyat: " . $this->totalPrice - $this->discount . " ₺\n";
                        $telegramMessage .= "Kullanıcı ID: " . auth()->user()->id . "\n";
                        $telegramMessage .= "Durum: Ödendi.\n";
                        $this->sendTelegramMessage($telegramMessage); // Telegram bildirimi gönder




                        // Redirect with proper response
                        return $this->redirect(route('orders.success', ['order' => $order->id]), navigate: true);

                        // return redirect()->route('home');
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->dispatch('showToast', message: 'Error processing order: ' . $e->getMessage(), type: 'error');
                    }
                } else {
                    // if cart is empty
                    if ($this->cart->items->isEmpty()) {
                        $this->dispatch('showToast', message: 'Cart is empty.', type: 'error');

                        return redirect()->route('home')->with('error', 'Your cart is empty, you have to shop first.');
                    }

                    $errorMessage = $payment->getErrorMessage() ?? 'Unknown error occurred';
                    $this->dispatch('showToast', message: 'Payment failed: ' . $errorMessage, type: 'error');
                    return;
                }
            } catch (\Exception $e) {
                Log::error('Payment Exception:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                $this->dispatch('showToast', message: 'Payment processing error: ' . $e->getMessage(), type: 'error');
                return;
            }
        } elseif ($this->paymentMethod === 'paypal') {
            // Process payment with PayPal
            // ...
        }
    }

    #[On('stripe-payment-success')]
    public function handleStripeSuccess($paymentIntent)
    {
        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => auth()->id(),
                'address_id' => $this->selectedAddress,
                'total_price' => $this->totalPrice - $this->discount,
                'status' => 'paid',
                'payment_id' => $paymentIntent['id'],
                'payment_method' => 'stripe'
            ]);

            foreach ($this->cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->getCurrentPrice()
                ]);
            }

            $this->cart->items()->delete();
            $this->cart->delete();

            DB::commit();

            $this->dispatch('updateCart')->to(CartCounter::class);
            return $this->redirect(route('orders.success', ['order' => $order->id]), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed:', [
                'error' => $e->getMessage(),
                'payment_intent' => $paymentIntent
            ]);
            $this->dispatch('showToast', message: 'Order processing failed', type: 'error');
        }
    }

    #[On('process-payment')]
    public function processPayment()
    {
        if ($this->paymentMethod === 'stripe') {
            // Payment confirmation is handled client-side and through webhooks
            return;
        }
        // ...existing code for other payment methods...
    }

    public function render()
    {
        return view('livewire.checkout-component');
    }
}