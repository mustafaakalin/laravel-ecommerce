<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Product;
use App\Http\Resources\LikeResource;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */



    public function index()
    {
        $likes = Like::with(['product.images', 'product.category'])
            ->where('user_id', Auth::id())
            ->get();

        return LikeResource::collection($likes);
    }

    public function store($productId)
    {
        $product = Product::findOrFail($productId);

        $like = Like::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $productId
        ]);

        return new LikeResource($like->load(['product.images', 'product.category']));
    }

    public function destroy($productId)
    {
        $like = Like::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->firstOrFail();

        $like->delete();

        return response()->json([], 204);
    }

    public function check($productId)
    {
        $exists = Like::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'is_liked' => $exists
        ]);
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
}
