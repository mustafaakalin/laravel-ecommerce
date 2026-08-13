<?php

namespace App\Http\Controllers\Api\V1;

use Locale;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\OrderItem;
use Iyzipay\Model\Payment;
use Illuminate\Http\Request;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\PaymentChannel;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Iyzipay\Request\CreatePaymentRequest;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return $this->errorResponse('Sepetiniz boş', 400);
        }

        $addresses = Auth::user()->addresses;
        $subtotal = $cart->getTotalPrice();
        $shipping = 0; // Example shipping cost, adjust as necessary
        $total = $subtotal + $shipping;

        return $this->successResponse(compact('cart', 'addresses', 'subtotal', 'shipping', 'total'));
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'address_id' => 'required|exists:addresses,id',
                'card_name' => 'required_if:payment_method,credit_card',
                'card_number' => 'required_if:payment_method,credit_card|numeric',
                'expire_month' => 'required_if:payment_method,credit_card|numeric|between:1,12',
                'expire_year' => 'required_if:payment_method,credit_card|numeric',
                'cvc' => 'required_if:payment_method,credit_card|numeric|digits_between:3,4'
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        }

        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->with('items.product')->first();

        // Retrieve and validate the address
        $address = Address::where('id', $request->input('address_id'))
                          ->where('user_id', $userId)
                          ->first();

        if (!$address) {
            return $this->errorResponse('Invalid address.', 400);
        }

        $totalPrice = $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->quantity) {
                return $this->errorResponse("{$item->product->name} için yeterli stok bulunmamaktadır.", 400);
            }
        }

        $options = new \Iyzipay\Options();
        $options->setApiKey(env('IYZIPAY_API_KEY'));
        $options->setSecretKey(env('IYZIPAY_SECRET_KEY'));
        $options->setBaseUrl(env('IYZIPAY_BASE_URL'));

        $paymentRequest = new CreatePaymentRequest();
        $paymentRequest->setLocale(\Iyzipay\Model\Locale::TR);
        $paymentRequest->setConversationId(uniqid());
        $paymentRequest->setPrice($totalPrice);
        $paymentRequest->setPaidPrice($totalPrice);
        $paymentRequest->setCurrency('TRY');
        $paymentRequest->setPaymentChannel(\Iyzipay\Model\PaymentChannel::WEB);
        $paymentRequest->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);

        $paymentCard = new PaymentCard();
        $paymentCard->setCardHolderName($request->input('card_name'));
        $paymentCard->setCardNumber($request->input('card_number'));
        $paymentCard->setExpireMonth($request->input('expire_month'));
        $paymentCard->setExpireYear($request->input('expire_year'));
        $paymentCard->setCvc($request->input('cvc'));
        $paymentCard->setRegisterCard(0);
        $paymentRequest->setPaymentCard($paymentCard);

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId(uniqid());
        $buyer->setName($request->input('first_name'));
        $buyer->setSurname($request->input('last_name'));
        $buyer->setGsmNumber($request->input('phone') ?? '5555555555');
        $buyer->setEmail($request->input('email'));
        $buyer->setIdentityNumber(Auth::user()->identity_number ?? '11111111111');
        $buyer->setLastLoginDate(now()->format('Y-m-d H:i:s'));
        $buyer->setRegistrationDate(Auth::user()->created_at->format('Y-m-d H:i:s'));
        $buyer->setRegistrationAddress($address->address);
        $buyer->setIp($request->ip());
        $buyer->setCity($address->city);
        $buyer->setCountry($address->country);
        $buyer->setZipCode($address->zip_code);
        $paymentRequest->setBuyer($buyer);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName($request->input('first_name') . ' ' . $request->input('last_name'));
        $billingAddress->setCity($address->city);
        $billingAddress->setCountry($address->country);
        $billingAddress->setAddress($address->address);
        $billingAddress->setZipCode($address->zip_code);
        $paymentRequest->setBillingAddress($billingAddress);

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName($request->input('first_name') . ' ' . $request->input('last_name'));
        $shippingAddress->setCity($address->city);
        $shippingAddress->setCountry($address->country);
        $shippingAddress->setAddress($address->address);
        $shippingAddress->setZipCode($address->zip_code);
        $paymentRequest->setShippingAddress($shippingAddress);

        $basketItems = [];
        foreach ($cart->items as $cartItem) {
            $basketItem = new BasketItem();
            $basketItem->setId('BI_' . $cartItem->product->id);
            $basketItem->setName($cartItem->product->name);
            $basketItem->setCategory1($cartItem->product->category->name);
            $basketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
            $basketItem->setPrice($cartItem->product->price * $cartItem->quantity);
            $basketItems[] = $basketItem;
        }

        $paymentRequest->setBasketItems($basketItems);

        $payment = Payment::create($paymentRequest, $options);

        if ($payment->getStatus() === 'success') {
            try {
                $order = Order::create([
                    'user_id' => $userId,
                    'address_id' => $address->id,
                    'total_price' => $totalPrice,
                    'status' => "pending",
                ]);
            
                Log::info('Order created successfully', [
                    'order_id' => $order->id,
                    'user_id' => $userId,
                    'total_price' => $totalPrice,
                    'address_id' => $address->id
                ]);
            } catch (\Exception $e) {
                Log::error('Order creation failed', [
                    'error' => $e->getMessage(),
                    'user_id' => $userId,
                    'total_price' => $totalPrice,
                    'address_id' => $address->id
                ]);
                
                throw $e;
            }

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            $cart->items()->delete();
            $cart->delete();

            return $this->successResponse($order, 'Order created successfully.');
        } else {
            return $this->errorResponse($payment->getErrorMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    protected function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }


}
