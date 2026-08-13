<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Typesense\Client;

class ProductFilter extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';
    
    #[Url]
    public $category = '';
    
    #[Url]
    public $minPrice = '';
    
    #[Url]
    public $maxPrice = '';
    
    #[Url]
    public $sort = 'newest';

    public $isFilterModalOpen = false;

    public function mount()
    {
        $this->minPrice = request('min_price', '');
        $this->maxPrice = request('max_price', '');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function toggleFilterModal()
    {
        $this->isFilterModalOpen = !$this->isFilterModalOpen;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'category', 'minPrice', 'maxPrice', 'sort']);
        $this->resetPage();
    }

    public function getProducts()
    {
        $client = new Client([
            'api_key' => config('scout.typesense.client-settings.api_key'),
            'nodes' => [
                [
                    'host' => config('scout.typesense.client-settings.nodes.0.host'),
                    'port' => config('scout.typesense.client-settings.nodes.0.port'),
                    'protocol' => config('scout.typesense.client-settings.nodes.0.protocol'),
                ],
            ],
            'connection_timeout_seconds' => config('scout.typesense.client-settings.connection_timeout_seconds'),
        ]);

        $query = [
            'q' => $this->search,
            'query_by' => 'name,description,search_keywords',
            'per_page' => 12,
            // 'page' => $this->page,
        ];

        // Kategori filtresi
        if ($this->category) {
            $query['filter_by'] = "category_id:={$this->category}";
        }

        // Fiyat filtresi
        if ($this->minPrice !== '' || $this->maxPrice !== '') {
            $priceFilter = [];
            if ($this->minPrice !== '') {
                $priceFilter[] = "price:>={$this->minPrice}";
            }
            if ($this->maxPrice !== '') {
                $priceFilter[] = "price:<={$this->maxPrice}";
            }
            if (!empty($priceFilter)) {
                $query['filter_by'] = ($query['filter_by'] ?? '') . ' && ' . implode(' && ', $priceFilter);
            }
        }

        // Sıralama
        switch ($this->sort) {
            case 'price_asc':
                $query['sort_by'] = 'price:asc';
                break;
            case 'price_desc':
                $query['sort_by'] = 'price:desc';
                break;
            case 'newest':
                $query['sort_by'] = 'created_at:desc';
                break;
            case 'rating':
                $query['sort_by'] = 'rating:desc';
                break;
        }

        if (!empty($query['filter_by'])) {
            $query['filter_by'] = trim($query['filter_by'], ' && ');
        } else {
            unset($query['filter_by']);
        }

        if (!isset($query['sort_by'])) {
            unset($query['sort_by']);
        }

        $results = $client->collections['products_index']->documents->search($query);

        return collect($results['hits'] ?? [])->map(function ($hit) {
            $document = $hit['document'];
            $highlight = $hit['highlight'] ?? [];

            $highlighted = [
                'name' => $highlight['name']['snippet'] ?? $document['name'],
                'description' => $highlight['description']['snippet'] ?? $document['description'],
                'tags' => $highlight['tags']['snippet'] ?? $document['tags'],
            ];

            return [
                'id' => $document['id'],
                'name' => $highlighted['name'],
                'description' => $highlighted['description'],
                'price' => $document['price'],
                'slug' => $document['slug'],
                'tags' => $highlighted['tags'] ?? [],
                'images' => $document['images'] ?? [],
            ];
        });
    }

    public function render()
    {
        $products = $this->getProducts();
        return view('livewire.product-filter', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->get(),
        ]);
    }
}