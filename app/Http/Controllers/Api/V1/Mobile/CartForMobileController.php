<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Mobile\CartForMobileResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CartForMobileController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    //     // Apply specific permission middleware to each method
    //     $this->middleware([\Illuminate\Auth\Middleware\Authorize::using('permission:cart:view')]);
    //     $this->middleware([\Illuminate\Auth\Middleware\Authorize::using('permission:cart:add')]);
    //     $this->middleware([\Illuminate\Auth\Middleware\Authorize::using('permission:cart:update')]);
    //     $this->middleware([\Illuminate\Auth\Middleware\Authorize::using('permission:cart:remove')]);
    //     $this->middleware([\Illuminate\Auth\Middleware\Authorize::using('permission:product:purchase')]);
    // }

    // public static function middleware(): array
    // {
    //     return [
    //         // examples with aliases, pipe-separated names, guards, etc:
    //         'role_or_permission:manager|edit articles',
    //         new Middleware('role:author', only: ['index']),
    //         new Middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('manager'), except: ['show']),
    //         new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('delete records,api'), only: ['destroy']),
    //     ];
    // }


    public function index(): JsonResponse
    {
        try {
            // No need for additional permission check since middleware already handles it
            $cart = Cart::with(['items.product.media'])
                ->where('user_id', Auth::id())
                ->firstOrCreate(['user_id' => Auth::id()]);

            return response()->json(new CartForMobileResource($cart));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not retrieve cart',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {

            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1|max:99'
            ]);

            $product = Product::with(['campaigns'])->findOrFail($validated['product_id']);

            // Check product availability
            if (!$product->is_active) {
                return response()->json([
                    'error' => 'Product unavailable',
                    'message' => 'Product is not active'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check stock
            if ($product->stock < $validated['quantity']) {
                return response()->json([
                    'error' => 'Insufficient stock',
                    'message' => "Only {$product->stock} items available"
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check campaign validity if exists
            $activeCampaign = $product->campaigns()
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if ($activeCampaign) {
                if (!$activeCampaign->isActive()) {
                    return response()->json([
                        'error' => 'Campaign expired',
                        'message' => 'The campaign for this product has expired'
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            // Check existing item and update quantity
            $existingItem = $cart->items()->where('product_id', $validated['product_id'])->first();
            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $validated['quantity'];
                if ($newQuantity > $product->stock) {
                    return response()->json([
                        'error' => 'Stock limit exceeded',
                        'message' => "Cannot add more items. Stock limit: {$product->stock}"
                    ], Response::HTTP_BAD_REQUEST);
                }
                $existingItem->update(['quantity' => $newQuantity]);
            } else {
                $cart->items()->create($validated);
            }

            return response()->json(new CartForMobileResource($cart->fresh(['items.product.media', 'coupon'])));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not add item to cart',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(['code' => 'required|string']);
    
            $coupon = Coupon::where('code', $validated['code'])
                ->where('is_active', true)
                ->first();
    
            if (!$coupon) {
                return response()->json([
                    'error' => 'Invalid coupon',
                    'message' => 'This coupon does not exist'
                ], Response::HTTP_BAD_REQUEST);
            }
    
            if (!$coupon->isValid()) {
                return response()->json([
                    'error' => 'Invalid coupon',
                    'message' => 'This coupon has expired or reached its usage limit'
                ], Response::HTTP_BAD_REQUEST);
            }
    
            $cart = Cart::where('user_id', Auth::id())->firstOrFail();
    
            // Check if cart meets minimum amount requirement if exists
            if ($coupon->minimum_amount && $cart->getTotalPrice() < $coupon->minimum_amount) {
                return response()->json([
                    'error' => 'Minimum amount not met',
                    'message' => "Cart total must be at least {$coupon->minimum_amount} to use this coupon"
                ], Response::HTTP_BAD_REQUEST);
            }
    
            $cart->update(['coupon_id' => $coupon->id]);
    
            return response()->json(new CartForMobileResource($cart->fresh(['items.product.media', 'coupon'])));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not apply coupon',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $productId): JsonResponse
    {
        try {
            // if (!Auth::user()->can('cart:update')) {
            //     return response()->json([
            //         'error' => 'Permission denied',
            //         'message' => 'You do not have permission to update cart items'
            //     ], Response::HTTP_FORBIDDEN);
            // }

            $validated = $request->validate([
                'quantity' => 'required|integer|min:1|max:99'
            ]);

            $cart = Cart::where('user_id', Auth::id())->firstOrFail();
            $cartItem = $cart->items()->where('product_id', $productId)->firstOrFail();
            $product = Product::with(['campaigns'])->findOrFail($productId);

            // Check product availability
            if (!$product->is_active) {
                return response()->json([
                    'error' => 'Product unavailable',
                    'message' => 'Product is not active'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check stock availability
            if ($product->stock < $validated['quantity']) {
                return response()->json([
                    'error' => 'Insufficient stock',
                    'message' => "Only {$product->stock} items available"
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check campaign validity if exists
            // if ($product->activeCampaign && !$product->activeCampaign->isActive()) {
            //     return response()->json([
            //         'error' => 'Campaign expired',
            //         'message' => 'The campaign for this product has expired'
            //     ], Response::HTTP_BAD_REQUEST);
            // }


            // Check campaign validity if exists
            $activeCampaign = $product->campaigns()
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if ($activeCampaign) {
                if (!$activeCampaign->isActive()) {
                    return response()->json([
                        'error' => 'Campaign expired',
                        'message' => 'The campaign for this product has expired'
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            $cartItem->update(['quantity' => $validated['quantity']]);

            // If cart is empty after update, remove it
            if ($cart->items()->sum('quantity') === 0) {
                $cart->delete();
                return response()->json([
                    'message' => 'Cart is now empty'
                ], Response::HTTP_OK);
            }

            return response()->json(new CartForMobileResource($cart->fresh(['items.product.media', 'coupon'])));

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Not found',
                'message' => 'Cart item not found'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not update cart item',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($productId): JsonResponse
    {
        try {

            $cart = Cart::where('user_id', Auth::id())->firstOrFail();

            // Remove the item
            $deleted = $cart->items()->where('product_id', $productId)->delete();

            if (!$deleted) {
                return response()->json([
                    'error' => 'Not found',
                    'message' => 'Item not found in cart'
                ], Response::HTTP_NOT_FOUND);
            }

            // If cart is empty after removal, delete the cart
            if ($cart->items()->count() === 0) {
                $cart->delete();
                return response()->json([
                    'message' => 'Cart is now empty'
                ], Response::HTTP_OK);
            }

            return response()->json(new CartForMobileResource($cart->fresh(['items.product.media', 'coupon'])));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not remove item from cart',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Other necessary methods (update, destroy, etc.)
}
