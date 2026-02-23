<?php

return [
    'provider' => env('AI_PROVIDER', 'groq'),
    'api_base' => env('AI_API_BASE', 'https://api.groq.com/openai/v1'),
    'model' => env('AI_MODEL', 'openai/gpt-oss-120b'),
    'temperature' => (float) env('AI_TEMPERATURE', 0.2),
    'timeout' => (int) env('AI_TIMEOUT', 120),

    // Legacy support
    'api_key' => env('AI_API_KEY'),

    // Multi-key support
    'api_key_1' => env('AI_API_KEY_1'),
    'api_key_2' => env('AI_API_KEY_2'),
    'api_key_3' => env('AI_API_KEY_3'),
    'api_key_4' => env('AI_API_KEY_4'),
    'api_key_5' => env('AI_API_KEY_5'),
    'api_key_6' => env('AI_API_KEY_6'),
    'api_key_7' => env('AI_API_KEY_7'),
    'api_key_8' => env('AI_API_KEY_8'),
    'api_key_9' => env('AI_API_KEY_9'),
    'api_key_10' => env('AI_API_KEY_10'),
];
