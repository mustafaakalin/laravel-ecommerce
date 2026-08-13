<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    const MAX_COMMENTS_PER_USER = 10;

    public function index($productId)
    {
        $comments = Comment::with(['user'])
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return CommentResource::collection($comments);
    }

    public function store(Request $request, $productId)
    {
        $request->validate([
            'content' => 'required|string|min:3',
            'rating' => 'required|integer|between:1,5'
        ]);

        $product = Product::findOrFail($productId);

        // Kullanıcının bu ürüne kaç yorum yaptığını kontrol et
        $userCommentCount = Comment::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->count();

        if ($userCommentCount >= self::MAX_COMMENTS_PER_USER) {
            return response()->json([
                'message' => 'You have reached the maximum number of comments (10) for this product'
            ], 422);
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'content' => $request->content,
            'rating' => $request->rating
        ]);

        // Ürünün ortalama puanını güncelle
        $avgRating = Comment::where('product_id', $productId)->avg('rating');
        $product->update(['rating' => round($avgRating, 1)]);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully',
            'data' => new CommentResource($comment->load('user'))
        ], Response::HTTP_OK);

    }

    public function update(Request $request, $productId, $commentId)
    {
        $request->validate([
            'content' => 'required|string|min:3',
            'rating' => 'required|integer|between:1,5'
        ]);

        $comment = Comment::where('id', $commentId)
            ->where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->firstOrFail();

        $comment->update([
            'content' => $request->content,
            'rating' => $request->rating
        ]);

        // Ürünün ortalama puanını güncelle
        $avgRating = Comment::where('product_id', $productId)->avg('rating');
        $product = Product::find($productId);
        $product->update(['rating' => round($avgRating, 1)]);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment Updated successfully',
            'data' => new CommentResource($comment->load('user'))
        ], Response::HTTP_OK);
    }

    public function destroy($productId, $commentId)
    {
        $comment = Comment::where('id', $commentId)
            ->where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->firstOrFail();

        $comment->delete();

        // Ürünün ortalama puanını güncelle
        $avgRating = Comment::where('product_id', $productId)->avg('rating') ?? 0;
        $product = Product::find($productId);
        $product->update(['rating' => round($avgRating, 1)]);

        return response()->json([], 204);
    }

    public function userCommentsInfo($productId)
    {
        $commentCount = Comment::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->count();

        return response()->json([
            'comment_count' => $commentCount,
            'remaining_comments' => self::MAX_COMMENTS_PER_USER - $commentCount,
            'can_comment' => $commentCount < self::MAX_COMMENTS_PER_USER
        ]);
    }
}
