<?php
return [
    'api_key' => env('HUGGINGFACE_API_KEY', 'your-default-api-key'),
    'base_uri' => 'https://api-inference.huggingface.co/',
    'model' => 'google/gemma-2-2b-it', // Ensure this is the correct model
];