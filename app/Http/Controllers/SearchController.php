<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Typesense\Client;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $client = new Client(config('scout.typesense.client-settings'));

        $page = max(1, min((int) $request->query('page', 1), 10000));
        $perPage = max(1, min((int) $request->query('per_page', 24), 100));

        $searchParameters = [
            'q' => $request->query('query', ''),
            'query_by' => 'name,description,tags',
            'sort_by' => $request->query('sort_by', '_text_match:desc,created_at:desc'),
            'per_page' => $perPage,
            'page' => $page,
            'highlight_fields' => 'name,description',
            'highlight_full_fields' => 'name,description',
            'use_cache' => true,
            'cache_ttl' => 60,
        ];

        if ($request->filled('filter_by')) {
            $allowedFields = ['brand_id', 'category_id', 'stock', 'price', 'is_new', 'is_featured'];
            $safeFilters = [];

            foreach (preg_split('/\s+&&\s+/', $request->query('filter_by')) as $part) {
                if (! preg_match('/^([a-z_]+):(=|>=|<=|>|<)([A-Za-z0-9_.-]+)$/', trim($part), $matches)
                    || ! in_array($matches[1], $allowedFields, true)) {
                    return response()->json(['message' => 'Invalid filter'], 422);
                }

                $safeFilters[] = $matches[1] . ':' . $matches[2] . $matches[3];
            }

            $searchParameters['filter_by'] = implode(' && ', $safeFilters);
        }

        $searchResults = $client->collections['products']->documents->search($searchParameters);

        $ids = collect($searchResults['hits'])
            ->pluck('document.id')
            ->map(static fn ($id): int => (int) $id)
            ->values();

        $productsById = Product::query()
            ->with(['images', 'brand', 'category'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $products = $ids
            ->map(static fn (int $id) => $productsById->get($id))
            ->filter()
            ->values();

        $totalHits = (int) ($searchResults['found'] ?? 0);
        $paginator = new LengthAwarePaginator(
            $products,
            $totalHits,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            'data' => $products->map(static fn (Product $product) => [
                'id' => $product->id,
                'component' => view('components.product-card', compact('product'))->render(),
            ])->values(),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_pages' => $paginator->lastPage(),
                'total_results' => $totalHits,
            ],
        ]);
    }
}
