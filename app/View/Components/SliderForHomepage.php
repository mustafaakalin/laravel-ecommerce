<?php

namespace App\View\Components;

use Closure;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Campaign;
use App\Models\Category;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Models\SliderForHomepage as ModelsSliderForHomepage;

class SliderForHomepage extends Component
{
    public $VarSliderForHomepageData;
    public $products;
    public $brands;
    public $categories;
    public $campaigns;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->VarSliderForHomepageData = ModelsSliderForHomepage::where('status', 1)
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    
        // Debug statement
        // dd($this->VarSliderForHomepageData); // Uncomment to check data
    
        $this->products = Product::where('is_active', true)->get();
        $this->brands = Brand::where('is_active', true)->get();
        $this->categories = Category::where('is_active', true)->get();
        $this->campaigns = Campaign::where('is_active', true)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.slider-for-homepage', [
            'VarSliderForHomepageData' => $this->VarSliderForHomepageData,
            'products' => $this->products,
            'brands' => $this->brands,
            'categories' => $this->categories,
            'campaigns' => $this->campaigns
        ]);
    }
}
