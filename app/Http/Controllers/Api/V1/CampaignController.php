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
        $campaigns = Campaign::query()->paginate(10);
        return CampaignResource::collection($campaigns);
    }

    public function show($slug)
    {
        $campaign = Campaign::query()->where('slug', $slug)->firstOrFail();
        return new CampaignResource($campaign);
    }
}
