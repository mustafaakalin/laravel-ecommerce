<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{
    public function index()
    {
        try {
            $campaigns = Campaign::with(['products' => function($query) {
                $query->with(['images', 'category']);
            }])
            ->withCount('products') // Add this line
            ->where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->paginate(6);

            return view('campaigns.index', compact('campaigns'));
        } catch (\Exception $e) {
            return response()->view('errors.500', ['error' => $e->getMessage()], 500);
        }
    }

    public function show(string $slug)
    {
        try {
            $campaign = Campaign::with(['products' => function($query) {
                $query->with(['images', 'category', 'brand'])
                      ->where('is_active', true)
                      ->where('stock', '>', 0);
            }])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

            $products = $campaign->products()->paginate(12);

            return view('campaigns.show', compact('campaign', 'products'));
        } catch (\Exception $e) {
            Log::error('Campaign show error: ' . $e->getMessage());
            return redirect()->route('campaigns.index')
                           ->with('error', 'Campaign not found or no longer active.');
        }
    }
}