<?php

namespace App\Services;

use Http;
use App\Models\Tag;
use App\Models\Product;

class OpenAiService
{
    public function retrieveTagsForDescription(string $description): array
    {
        $queryOpenAI = $this->sendOpenAIQuery($description);

        return $this->createTags($queryOpenAI);
    }

    private function sendOpenAIQuery(string $description): string
    {
        $query = view('openai.descriptionQuery', [
            'description' => $description,
            ])->render();

        $aiQuery = Http::withToken(config('services.openai.apiKey'))
            ->asJson()
            ->acceptJson()
            ->post(
                'https://api.openai.com/v1/chat/completions',
                [
                    'model' => 'gpt-3.5-turbo-instruct',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $query
                        ]
                    ],
                ]
            );

        // just log the requests... maybe we will want the debug :

        info(str('-')->repeat(100));
        info($aiQuery->json());
        info(str('-')->repeat(100));


        // cleanup is needed from time to time.
        return str_replace('```', '', $aiQuery->json('choices.0.message.content'));
    }


    private function createTags(string $openAIResponse): array
    {
        $tags = [];

        foreach ($this->parseTagsFromResponse($openAIResponse) as $tag) {
            $tags[] = Tag::firstOrCreate(['name' => $tag], ['name' => $tag])->id;
        }

        return $tags;
    }

    private function parseTagsFromResponse(string $openAIResponse): array
    {
        return str($openAIResponse)
        ->chopEnd('.')
        ->explode(',')
        ->toArray();
    }
}
