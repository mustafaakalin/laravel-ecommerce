<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;

class SearchProducts extends Component
{
    public $search = '';
    public $isOpen = false;

    public function updatedSearch()
    {
        if (strlen($this->search) > 0) {
            $this->isOpen = true;
        } else {
            $this->isOpen = false;
        }
    }




    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return collect(); // Arama terimi 2 karakterden kısa ise boş koleksiyon döndür
        }

        // Typesense'den ham sonuçları al
        $results = Product::search($this->search)
            ->take(5)
            ->raw(); // Ham sonuç JSON olarak alınır

        // Sonuçları işler ve highlight'lı veriyi dahil eder
        return collect($results['hits'] ?? [])->map(function ($hit) {
            $document = $hit['document']; // Document verisini al
            $highlight = $hit['highlight'] ?? []; // Highlight verisini al

            // Highlight verisini yerleştir
            $highlighted = [
                'name' => $highlight['name']['snippet'] ?? $document['name'], // Highlight edilmiş isim
                'description' => $highlight['description']['snippet'] ?? Str::limit(strip_tags($document['description']), 50), // Highlight edilmiş açıklama
                'tags' => $highlight['tags']['snippet'] ?? $document['tags'], // Highlight edilmiş isim
            ];

            return [
                'id' => $document['id'],
                'name' => $highlighted['name'],
                'description' => $highlighted['description'],
                'price' => $document['price'],
                'slug' => $document['slug'],
                'tags' => $highlighted['tags'] ?? [], // Tags bilgisi
                'images' => $document['images'] ?? [], // Images bilgisi
            ];
        });
    }




    public function render()
    {
        return view('livewire.search-products');
    }
}
