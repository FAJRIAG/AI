<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$keys = [
    config('ai.api_key_1'),
    config('ai.api_key_2'),
    config('ai.api_key_3'),
    config('ai.api_key_4'),
    config('ai.api_key_5'),
    config('ai.api_key_6'),
    config('ai.api_key_7'),
    config('ai.api_key_8'),
    config('ai.api_key_9'),
    config('ai.api_key_10'),
    config('ai.api_key_11'),
    config('ai.api_key_12'),
];

$model = 'openai/gpt-oss-120b';
$url = rtrim(config('ai.api_base', 'https://api.groq.com/openai/v1'), '/') . '/chat/completions';

echo "Testing model: $model\n";
echo "=================================================\n";

foreach ($keys as $idx => $key) {
    if (empty($key)) {
        echo "Key " . ($idx + 1) . ": KOSONG\n";
        continue;
    }

    $shortKey = substr($key, 0, 8) . '...' . substr($key, -4);

    $resp = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Bearer ' . $key,
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->post($url, [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => 'halo']],
                'max_tokens' => 10,
            ]);

    if ($resp->successful()) {
        echo "Key " . ($idx + 1) . " ($shortKey) : OK [200]\n";
    } elseif ($resp->status() === 429) {
        $data = $resp->json();
        $msg = $data['error']['message'] ?? 'Rate limit exceeded';
        echo "Key " . ($idx + 1) . " ($shortKey) : RATE LIMIT [429] - " . (str_contains($msg, 'Tokens per Day') ? 'TPD limit habis' : 'Limit lain') . "\n";
    } else {
        echo "Key " . ($idx + 1) . " ($shortKey) : ERROR [" . $resp->status() . "]\n";
    }
}
echo "=================================================\n";
