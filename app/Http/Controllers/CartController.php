<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with(['items.product.images'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cartItems = $cart->items;

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->getCurrentPrice() * $item->quantity;
        });

        $shippingCost = 15; // Or calculate based on your shipping logic
        $total = $subtotal + $shippingCost;

        return view('cart.index', compact('cartItems', 'subtotal', 'shippingCost', 'total'));
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

        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Üzgünüz, yeterli stok bulunmamaktadır.'
            ], 422);
        }

        $cartItem = CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'product_id' => $request->product_id
            ],
            [
                'quantity' => $request->quantity
            ]
        );

        return response()->json([
            'message' => 'Ürün sepete eklendi',
            'cart_count' => $cart->getTotalItems()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('cart_id', Auth::user()->cart->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($cartItem->product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Üzgünüz, yeterli stok bulunmamaktadır.'
            ], 422);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'message' => 'Sepet güncellendi',
            'cart_total' => $cartItem->cart->getTotalPrice(),
            'item_total' => $cartItem->getTotalPrice()
        ]);
    }

    public function destroy($id)
    {
        CartItem::where('cart_id', Auth::user()->cart->id)
            ->where('id', $id)
            ->delete();

        return response()->json([
            'message' => 'Ürün sepetten kaldırıldı'
        ]);
    }
}
