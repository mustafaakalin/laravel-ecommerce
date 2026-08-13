<?php

namespace App\View\Composers;

use App\Models\Category;
use Illuminate\View\View;

class FooterComposer
{
    public function compose(View $view)
    {
        $footerCategories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(5)
            ->get();

        $view->with('footerCategories', $footerCategories);
    }
}