<?php

namespace App\Livewire;

use App\Models\{Brand, Category, Product};
use Livewire\Component;
use Livewire\WithPagination;
use Typesense\Client;

class ProductListing extends Component
{
    use WithPagination;

    // Search and sorting properties
    public string $search = '';
    public string $sort = 'newest';
    public int $page = 1;
    protected int $perPage = 12;

    // Filter properties
    public array $filters = [
        'price_min' => null,
        'price_max' => null,
        'categories' => [],
        'brands' => [],
        'only_active' => false,
        'only_in_stock' => false,
    ];

    // Collection properties
    public $categories;
    public $brands;

    // URL query parameters configuration
    protected $queryString = [
        'search' => ['except' => ''],
        'sort' => ['except' => 'newest'],
        'filters' => ['except' => [
            'price_min' => null,
            'price_max' => null,
            'categories' => [],
            'brands' => [],
            'only_active' => false,
            'only_in_stock' => false,
        ]],
        'page' => ['except' => 1],
    ];

    /**
     * Initialize component data
     */
    public function mount(): void
    {
        $this->loadCategories();
        $this->loadBrands();
    }

    /**
     * Reset pagination on filter/search/sort updates
     */
    public function updated($field): void
    {
        if (in_array($field, ['search', 'sort', 'filters'])) {
            $this->resetPage();
            $this->dispatch('filters-updated');
        }
    }



    public function resetFilters(): void
    {
        $this->filters = [
            'price_min' => null,
            'price_max' => null,
            'categories' => [],
            'brands' => [],
            'only_active' => false,
            'only_in_stock' => false,
        ];
        
        $this->resetPage();
        $this->dispatch('filters-updated');
    }


    /**
     * Reset pagination on filter updates
     */
    public function updatedFilters($value, $key): void
    {
        // Değer kontrolü
        if ($value === '' && str_contains($key, 'price')) {
            data_set($this->filters, $key, null);
        }
        
        // Sayfa resetleme ve güncelleme
        $this->resetPage();
        $this->dispatch('filters-updated');
    }

    /**
     * Render the component
     */
    public function render()
    {
        $products = $this->searchProducts();

        return view('livewire.product-listing', [
            'products' => $products,
            'categories' => $this->categories,
            'brands' => $this->brands,
        ]);
    }

    /**
     * Load active categories
     */
    private function loadCategories(): void
    {
        $this->categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Load active brands
     */
    private function loadBrands(): void
    {
        $this->brands = Brand::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Search products using Typesense
     */
    private function searchProducts()
    {
        $client = new Client(config('scout.typesense.client-settings'));
        
        $searchParameters = [
            'q' => $this->search,
            'query_by' => 'name,description,tags',
            'filter_by' => $this->buildTypesenseFilters(),
            'sort_by' => $this->buildTypesenseSort(),
            'per_page' => $this->perPage,
            'page' => $this->page,
        ];

        $result = $client->collections['products']->documents->search($searchParameters);

        return $this->mapSearchResults($result['hits']);
    }

    /**
     * Map Typesense search results to Product models
     */
    private function mapSearchResults(array $hits)
    {
        return collect($hits)->map(function ($hit) {
            return Product::find($hit['document']['id']);
        });
    }

    /**
     * Build Typesense filter string
     */

     private function buildTypesenseFilters(): string
     {
         $filters = [];
         
         // Kategoriler
         if (!empty($this->filters['categories'])) {
             $categories = array_filter($this->filters['categories']); // Boş değerleri temizle
             if (!empty($categories)) {
                 $filters[] = 'category_id:=[' . implode(',', $categories) . ']';
             }
         }
         
         // Markalar
         if (!empty($this->filters['brands'])) {
             $brands = array_filter($this->filters['brands']); // Boş değerleri temizle
             if (!empty($brands)) {
                 $filters[] = 'brand_id:=[' . implode(',', $brands) . ']';
             }
         }
         
         // Fiyat filtreleri
         if (!empty($this->filters['price_min'])) {
             $filters[] = 'price:>=' . floatval($this->filters['price_min']);
         }
         
         if (!empty($this->filters['price_max'])) {
             $filters[] = 'price:<=' . floatval($this->filters['price_max']);
         }
         
         // Durum filtreleri
         if ($this->filters['only_active']) {
             $filters[] = 'is_active:=true';
         }
         
         if ($this->filters['only_in_stock']) {
             $filters[] = 'stock:>0';
         }
         
         return empty($filters) ? '' : implode(' && ', $filters);
     }

    


    /**
     * Get category filter
     */
    private function getCategoryFilter(): ?string
    {
        if (empty($this->filters['categories'])) {
            return null;
        }

        return 'category_id:=[' . implode(',', $this->filters['categories']) . ']';
    }

    /**
     * Get brand filter
     */
    private function getBrandFilter(): ?string
    {
        if (empty($this->filters['brands'])) {
            return null;
        }

        return 'brand_id:=[' . implode(',', $this->filters['brands']) . ']';
    }

    /**
     * Get price range filter
     */
    private function getPriceFilter(): string
    {
        $priceFilters = [];

        if ($this->filters['price_min']) {
            $priceFilters[] = 'price:>=' . $this->filters['price_min'];
        }

        if ($this->filters['price_max']) {
            $priceFilters[] = 'price:<=' . $this->filters['price_max'];
        }

        return implode(' && ', $priceFilters);
    }

    /**
     * Get status-related filters
     */
    private function getStatusFilters(): string
    {
        $statusFilters = [];

        if ($this->filters['only_active']) {
            $statusFilters[] = 'is_active:=true';
        }

        if ($this->filters['only_in_stock']) {
            $statusFilters[] = 'stock:>0';
        }

        return implode(' && ', $statusFilters);
    }

    /**
     * Build Typesense sort string
     */
    private function buildTypesenseSort(): string
    {
        return match ($this->sort) {
            'price_asc' => 'price:asc',
            'price_desc' => 'price:desc',
            default => 'created_at:desc',
        };
    }
}