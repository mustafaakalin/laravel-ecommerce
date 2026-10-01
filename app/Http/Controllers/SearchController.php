<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
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
            $parts = preg_split('/\s+&&\s+/', $request->query('filter_by'));
            $safeFilters = [];

            foreach ($parts as $part) {
                if (!preg_match('/^([a-z_]+):(=|>=|<=|>|<)([A-Za-z0-9_.-]+)$/', trim($part), $m)
                    || !in_array($m[1], $allowedFields, true)) {
                    return response()->json(['message' => 'Invalid filter'], 422);
                }

                $safeFilters[] = $m[1] . ':' . $m[2] . $m[3];
            }

            if ($safeFilters) {
                $searchParameters['filter_by'] = implode(' && ', $safeFilters);
            }
        }

        $searchResults = $client->collections['products']->documents->search($searchParameters);

        // Fetch all matching models in one query (plus eager-loaded relations)
        // instead of one Product::find() query per Typesense hit.
        $ids = collect($searchResults['hits'])
            ->pluck('document.id')
            ->map(static fn ($id) => (int) $id)
            ->values();

        $productsById = Product::query()
            ->with(['images', 'brand', 'category'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        // Preserve Typesense ranking/order.
        $products = $ids
            ->map(static fn (int $id) => $productsById->get($id))
            ->filter()
            ->values();

        $totalHits = (int) $searchResults['found'];

        return response()->json([
            'data' => $products->map(static function (array $product) {
                return [
                    'id' => $product->id,
                    'component' => view('components.product-card', [
                        'product' => $product,
                    ])->render(),
                ];
            }),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_pages' => (int) ceil($totalHits / $perPage),
                'total_results' => $totalHits,
            ],
        ]);
    }
}
