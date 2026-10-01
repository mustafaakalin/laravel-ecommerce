<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Typesense\Client;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $client = new Client([
            'api_key' => env('TYPESENSE_API_KEY'),
            'nodes' => [
                [
                    'host' => env('TYPESENSE_HOST'),
                    'port' => env('TYPESENSE_PORT'),
                    'protocol' => env('TYPESENSE_PROTOCOL'),
                ],
            ],
            'connection_timeout_seconds' => 2,
        ]);

        $page = max(1, min((int) $request->query('page', 1), 10000));
        $perPage = max(1, min((int) $request->query('per_page', 24), 100));

        $searchParameters = [
            'q' => $request->query('query', ''),
            'query_by' => 'name,description,tags',
            'sort_by' => $request->query('sort_by', '_text_match:desc,created_at:desc'),
            'per_page' => $perPage,
            'page' => $page,
            'highlight_fields' => 'name,description',
            'highlight_full_fields' => 'name,description'
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

        $searchResults = $client->collections['products']
            ->documents
            ->search($searchParameters);

        $products = collect($searchResults['hits'])->map(function ($hit) {
            return Product::find($hit['document']['id']);
        });

        $totalHits = $searchResults['found'];
        $totalPages = ceil($totalHits / $perPage);

        return response()->json([
            'data' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'component' => view('components.product-card', ['product' => $product])->render()
                ];
            }),
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $perPage,
                'total_pages' => $totalPages,
                'total_results' => (int) $totalHits,
            ],
        ]);
    }
}
