<?php

namespace App\Livewire;

use App\Models\{Brand, Category, Product};
use App\Support\CacheKeys;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Typesense\Client;

class ProductListing extends Component
{
    public string $search = '';
    public string $sort = 'newest';
    public int $page = 1;

    protected int $perPage = 12;

    public array $filters = [
        'price_min' => null,
        'price_max' => null,
        'categories' => [],
        'brands' => [],
        'only_active' => false,
        'only_in_stock' => false,
    ];

    public $categories;
    public $brands;

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

    public function mount(): void
    {
        $this->categories = Cache::flexible(
            CacheKeys::catalog('categories'),
            [300, 1800],
            static fn () => Category::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
        );

        $this->brands = Cache::flexible(
            CacheKeys::catalog('brands'),
            [300, 1800],
            static fn () => Brand::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    public function updated($field): void
    {
        if (in_array($field, ['search', 'sort', 'filters'], true)) {
            $this->resetPage();
            $this->dispatch('filters-updated');
        }
    }

    public function updatedFilters($value, $key): void
    {
        if ($value === '' && str_contains($key, 'price')) {
            data_set($this->filters, $key, null);
        }

        $this->resetPage();
        $this->dispatch('filters-updated');
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

    public function render()
    {
        return view('livewire.product-listing', [
            'products' => $this->searchProducts(),
            'categories' => $this->categories,
            'brands' => $this->brands,
        ]);
    }

    private function searchProducts()
    {
        $result = (new Client(config('scout.typesense.client-settings')))
            ->collections['products']
            ->documents
            ->search([
                'q' => $this->search,
                'query_by' => 'name,description,tags',
                'filter_by' => $this->buildTypesenseFilters(),
                'sort_by' => $this->buildTypesenseSort(),
                'per_page' => $this->perPage,
                'page' => $this->page,
                'use_cache' => true,
                'cache_ttl' => 60,
            ]);

        $ids = collect($result['hits'])
            ->pluck('document.id')
            ->map(static fn ($id): int => (int) $id)
            ->values();

        $productsById = Product::query()
            ->with(['images', 'brand', 'category'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        return $ids
            ->map(static fn (int $id) => $productsById->get($id))
            ->filter()
            ->values();
    }

    private function buildTypesenseFilters(): string
    {
        $filters = [];

        $categories = $this->positiveIntegers($this->filters['categories'] ?? []);
        if ($categories) {
            $filters[] = 'category_id:=[' . implode(',', $categories) . ']';
        }

        $brands = $this->positiveIntegers($this->filters['brands'] ?? []);
        if ($brands) {
            $filters[] = 'brand_id:=[' . implode(',', $brands) . ']';
        }

        $priceMin = $this->filters['price_min'];
        if (is_numeric($priceMin) && (float) $priceMin >= 0) {
            $filters[] = 'price:>=' . number_format((float) $priceMin, 2, '.', '');
        }

        $priceMax = $this->filters['price_max'];
        if (is_numeric($priceMax) && (float) $priceMax >= 0) {
            $filters[] = 'price:<=' . number_format((float) $priceMax, 2, '.', '');
        }

        if ($this->filters['only_active']) {
            $filters[] = 'is_active:=true';
        }

        if ($this->filters['only_in_stock']) {
            $filters[] = 'stock:>0';
        }

        return implode(' && ', $filters);
    }

    private function positiveIntegers(array $values): array
    {
        return array_values(array_filter(
            array_map('intval', $values),
            static fn (int $value): bool => $value > 0
        ));
    }

    private function buildTypesenseSort(): string
    {
        return match ($this->sort) {
            'price_asc' => 'price:asc',
            'price_desc' => 'price:desc',
            default => 'created_at:desc',
        };
    }
}
