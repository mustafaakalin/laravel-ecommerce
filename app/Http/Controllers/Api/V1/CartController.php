<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', Auth::id())
            ->firstOrCreate(['user_id' => Auth::id()]);

        return new CartResource($cart);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $product = Product::findOrFail($request->product_id);

        $cartItem = $cart->items()->updateOrCreate(
            [
                'product_id' => $request->product_id,
            ],
            [
                'quantity' => $request->quantity
            ]
        );

        return new CartResource($cart->load('items.product'));
    }

    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::where('user_id', Auth::id())->firstOrFail();
        
        $cartItem = $cart->items()->where('product_id', $productId)->firstOrFail();
        
        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return new CartResource($cart->load('items.product'));
    }

    public function destroy($productId)
    {
        $cart = Cart::where('user_id', Auth::id())->firstOrFail();
        
        $cart->items()->where('product_id', $productId)->delete();

        if ($cart->items()->count() === 0) {
            $cart->delete();
            return response()->json([], 204);
        }

        return new CartResource($cart->load('items.product'));
    }
}