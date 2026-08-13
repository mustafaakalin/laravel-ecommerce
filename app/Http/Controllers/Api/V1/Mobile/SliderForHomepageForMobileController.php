<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SliderForHomepage;
use App\Http\Resources\Mobile\SliderForHomepageForMobileResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class SliderForHomepageForMobileController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $sliders = Cache::remember('homepage_sliders', 3600, function () {
            return SliderForHomepage::where('status', 1)
                ->orderBy('position', 'asc')
                ->orderBy('updated_at', 'desc')
                ->get();
        });

        return SliderForHomepageForMobileResource::collection($sliders);
    }
}
