<?php

namespace App\Http\Controllers;
use App\Models\Category; // Kategori modelinizi kullanıyorsunuz varsayalım

use Illuminate\Http\Request;

class NavbarController extends Controller
{
    public function getNavbarData()
    {

        $categories = Category::whereNull('parent_id')
        ->where('is_active', true)
        ->with(['children' => function($query) {
            $query->where('is_active', true)
                ->withCount('children')
                ->with(['children' => function($q) {
                    $q->where('is_active', true);
                }]);
        }])
        ->latest()
        ->get();
        return $categories;
    }
}
