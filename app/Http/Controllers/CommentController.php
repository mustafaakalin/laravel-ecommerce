<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|min:3|max:1000',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5'
        ]);
    
        $comment = Comment::create([
            'user_id' => auth()->id(),
            'product_id' => $validated['product_id'],
            'content' => $validated['content'],
            'rating' => $validated['rating']
        ]);
    
        return back()->with('success', 'Yorumunuz başarıyla eklendi.');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->delete();
        return back()->with('success', 'Yorum başarıyla silindi.');
    }

    // Yorumları Sayfalama ile Getirme
    public function fetchComments(Request $request, $productId)
    {
        // 5 yorum per page
        $comments = Comment::where('product_id', $productId)
                           ->with('user') // İlgili kullanıcıyı da getirelim
                           ->paginate(5);

        // Eğer AJAX ile istek yapılmışsa, JSON formatında geri dönelim
        if ($request->ajax()) {
            return response()->json([
                'comments' => $comments
            ]);
        }

        // Aksi takdirde sayfada devam et
        return view('product.show', compact('comments'));
    }
}
