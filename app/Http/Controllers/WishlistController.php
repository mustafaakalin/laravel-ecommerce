<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class WishlistController extends Controller
{
    public function index()
    {
        $likes = Auth::user()->likes()->with('product.images')->get();
        return view('wishlist.index', compact('likes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $like = Auth::user()->likes()->updateOrCreate([
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'message' => 'Ürün favorilere eklendi'
        ]);
    }

    public function destroy($productId)
    {
        Auth::user()->likes()->where('product_id', $productId)->delete();

        return response()->json([
            'message' => 'Ürün favorilerden kaldırıldı'
        ]);
    }
}
