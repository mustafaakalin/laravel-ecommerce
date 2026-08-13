<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Services\HuggingFaceService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     $huggingFaceService = new HuggingFaceService();
    //     if (isset($data['description'])) {
    //         Log::info('EditProduct: Generating tags.', ['description' => $data['description']]);
    //         $tags = $huggingFaceService->generateTags($data['description']);
    //         Log::info('EditProduct: Tags generated.', ['tags' => $tags]);
    //         if (!empty($tags)) {
    //             $data['tags'] = array_merge($data['tags'] ?? [], $tags);
    //         } else {
    //             // Handle the case where no tags were generated
    //             Session::flash('error', 'Tag generation failed. Please try again.');
    //         }
    //     }

    //     return $data;
    // }
}
