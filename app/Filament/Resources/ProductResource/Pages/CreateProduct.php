<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Services\HuggingFaceService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $huggingFaceService = new HuggingFaceService();
    //     Log::info('CreateProduct: Generating tags.', ['description' => $data['description']]);
    //     $tags = $huggingFaceService->generateTags($data['description']);
    //     Log::info('CreateProduct: Tags generated.', ['tags' => $tags]);
    //     if (!empty($tags)) {
    //         $data['tags'] = array_merge($data['tags'] ?? [], $tags);
    //     } else {
    //         // Handle the case where no tags were generated
    //         Session::flash('error', 'Tag generation failed. Please try again.');
    //     }

    //     return $data;
    // }
    
}
