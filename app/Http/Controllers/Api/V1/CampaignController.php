<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Http\Resources\CampaignResource;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('products','products.images','products.comments','products.category','products.campaigns')->paginate(10);
        return CampaignResource::collection($campaigns);
    }

    public function show($slug)
    {
        $campaign = Campaign::with('products','products.images','products.comments','products.category','products.campaigns')->where('slug', $slug)->firstOrFail();
        return new CampaignResource($campaign);
    }
}
