<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'icon',
        'image',
        'description',
        'products_count',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }



    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Aktif ürünleri filtrelemek için scope tanımlayın
    public function scopeActiveProductsCount($query)
    {
        // return $query->withCount(['products' => function ($query) {
        //     $query->where('is_active', true);
        // }]);
        return $query->withCount([
            'products as active_products_count' => function ($query) {
                $query->where('is_active', true);
            }
        ]);
    }
    public function scopeProductsCount($query)
    {
        return $query->withCount([
            'products as this_category_products_count' => function ($query) {
                $query->where('is_active', true)
                    ->where('stock', '>', 0);
            },
            'children as child_products_count' => function ($query) {
                $query->withCount([
                    'products as count' => function ($query) {
                        $query->where('is_active', true)
                            ->where('stock', '>', 0);
                    }
                ]);
            }
        ])
            ->addSelect(DB::raw('(this_category_products_count + COALESCE(child_products_count, 0)) as total_products_count'));
    }

    public function getTotalProductsCount(): int
    {
        return $this->products()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->count()
            +
            $this->children()
                ->withCount([
                    'products' => function ($query) {
                        $query->where('is_active', true)
                            ->where('stock', '>', 0);
                    }
                ])
                ->get()
                ->sum('this_category_products_count');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }




    public function getActualProductCount(): int
    {
        try {
            // Get direct products count
            $directProductsCount = $this->products()
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->count();

            // Get child categories products count
            $childrenProductsCount = $this->children()
                ->with([
                    'products' => function ($query) {
                        $query->where('is_active', true)
                            ->where('stock', '>', 0);
                    }
                ])
                ->get()
                ->sum(function ($childCategory) {
                    return $childCategory->products->count();
                });

            return $directProductsCount + $childrenProductsCount;
        } catch (\Exception $e) {
            Log::error('Error calculating actual product count: ' . $e->getMessage());
            return 0;
        }
    }

    public function getProductCountDetails(): array
    {
        try {
            // Direct products statistics
            $directProducts = $this->products()
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->count();

            // Child categories statistics
            $childCategories = $this->children()
                ->with([
                    'products' => function ($query) {
                        $query->where('is_active', true)
                            ->where('stock', '>', 0);
                    }
                ])
                ->get();

            $childrenProducts = $childCategories->sum(function ($childCategory) {
                return $childCategory->products->count();
            });

            return [
                'total_count' => $directProducts + $childrenProducts,
                'direct_products' => $directProducts,
                'children_products' => $childrenProducts,
                'child_categories_count' => $childCategories->count()
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating product count details: ' . $e->getMessage());
            return [
                'total_count' => 0,
                'direct_products' => 0,
                'children_products' => 0,
                'child_categories_count' => 0
            ];
        }
    }




    public function isInActive()
    {
        return $this->is_active = true;
    }


    public function updateProductsCount(): void
    {
        try {
            $totalCount = $this->products()
                ->where(function ($query) {
                    $query->where('is_active', true)
                        ->where('stock', '>', 0);
                })
                ->count();

            // Include products from child categories
            $childrenCount = $this->children()
                ->with('products')
                ->get()
                ->sum(function ($child) {
                    return $child->products()
                        ->where('is_active', true)
                        ->where('stock', '>', 0)
                        ->count();
                });

            $this->update([
                'products_count' => $totalCount + $childrenCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating category products count: ' . $e->getMessage());
        }
    }



    // public function created(Product $product)
    // {
    //     if ($product->category) {
    //         $product->category->updateProductsCount();
    //     }
    // }

    // public function updated(Product $product)
    // {
    //     // If category changed or active status changed
    //     if ($product->isDirty(['category_id', 'is_active', 'stock'])) {
    //         // Update old category count if category changed
    //         if ($product->isDirty('category_id') && $product->getOriginal('category_id')) {
    //             Category::find($product->getOriginal('category_id'))?->updateProductsCount();
    //         }
    //         // Update new category count
    //         if ($product->category) {
    //             $product->category->updateProductsCount();
    //         }
    //     }
    // }

    // public function deleted(Product $product)
    // {
    //     if ($product->category) {
    //         $product->category->updateProductsCount();
    //     }
    // }
    // public function boot()
    // {
    //     Product::observe(ProductObserver::class);
    // }

}
