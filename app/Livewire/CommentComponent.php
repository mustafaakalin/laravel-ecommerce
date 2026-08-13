<?php

namespace App\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Livewire\Attributes\Rule;

class CommentComponent extends Component
{
    public $product;
    
    #[Rule('required|min:10|max:1000')]
    public $content = '';
    
    #[Rule('required|integer|min:1|max:5')]
    public $rating = 5;

    public function mount($product)
    {
        $this->product = $product;
    }

    public function addComment()
    {
        $this->validate();

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'product_id' => $this->product->id,
            'content' => $this->content,
            'rating' => $this->rating
        ]);

        $this->reset('content', 'rating');

        $this->dispatch('comment-added');
        $this->dispatch('showToast', message: 'Yorumunuz başarıyla eklendi!', type: 'succes');

    }

    public function deleteComment($commentId)
    {
        $comment = Comment::find($commentId);

        if ($comment && $comment->user_id === auth()->id()) {
            $comment->delete();
            $this->dispatch('comment-deleted');
            $this->dispatch('showToast', message: 'Yorum başarıyla silindi!', type: 'succes');
    
        }
    }

    public function render()
    {
        return view('livewire.comment-component', [
            'comments' => $this->product->comments()->with('user')->latest()->get()
        ]);
    }
}