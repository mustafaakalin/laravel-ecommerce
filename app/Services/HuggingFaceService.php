<?php
// app/Services/HuggingFaceService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use App\Models\Tag;

class HuggingFaceService
{
    public function generateTags(string $description): array
    {
        // Remove HTML tags from the description
        $plainDescription = strip_tags($description);
        Log::info('HuggingFaceService: Generating tags for description.', ['description' => $plainDescription]);

        // Updated prompt to minimize echoing
        $prompt = "Generate a comma-separated list of relevant tags for the following product description. Respond with only the tags, separated by commas.\n\n{$plainDescription}";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('huggingface.api_key'),
                'Content-Type' => 'application/json',
            ])
            ->timeout(120) // Increased timeout to 120 seconds
            ->retry(5, 2000) // Retry up to 5 times with 2 seconds delay
            ->post(config('huggingface.base_uri') . 'models/google/gemma-2-2b-it', [
                'inputs' => $prompt,
            ]);

            Log::info('HuggingFaceService: API Response.', [
                'response_status' => $response->status(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data[0]['generated_text'])) {
                    $generatedText = $data[0]['generated_text'];

                    // Use regex to extract text between '**Tags:**' and the next newline or end of string
                    if (preg_match('/\*\*Tags:\*\*(.*?)(\n|$)/i', $generatedText, $matches)) {
                        $tagsText = $matches[1];
                    } else {
                        // Fallback: assume entire generatedText is the tags
                        $tagsText = $generatedText;
                    }

                    // Split the tags by commas
                    $tags = explode(',', $tagsText);

                    // Trim whitespace, remove unwanted characters, and ensure uniqueness
                    $filteredTags = array_unique(array_filter(array_map(function($tag) {
                        $cleanTag = trim($tag);
                        // Remove any non-alphanumeric characters except spaces and hyphens
                        $cleanTag = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $cleanTag);
                        return !empty($cleanTag) ? $cleanTag : null;
                    }, $tags)));

                    // Log the filtered tags for debugging
                    Log::info('HuggingFaceService: Filtered Tags.', ['filtered_tags' => $filteredTags]);

                    // Create or retrieve tag IDs
                    $tagIds = [];
                    foreach ($filteredTags as $tag) {
                        $tagIds[] = Tag::firstOrCreate(['name' => $tag])->id;
                    }

                    Log::info('HuggingFaceService: Extracted tag IDs.', ['tag_ids' => $tagIds]);
                    return $tagIds;
                } else {
                    Log::warning('HuggingFaceService: generated_text not found in response.', ['response_data' => $data]);
                }
            } else {
                Log::error('HuggingFaceService: API request failed.', [
                    'response_status' => $response->status(),
                    'response_body' => $response->body()
                ]);
            }
        } catch (ConnectException $e) {
            Log::error('HuggingFaceService: Connection error.', ['message' => $e->getMessage()]);
        } catch (RequestException $e) {
            Log::error('HuggingFaceService: Request error.', ['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('HuggingFaceService: Unexpected error.', ['message' => $e->getMessage()]);
        }

        return [];
    }
}