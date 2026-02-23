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
];

$model = 'openai/gpt-oss-120b';
$url = rtrim(config('ai.api_base', 'https://api.groq.com/openai/v1'), '/') . '/chat/completions';

echo "Cek Sisa Token Harian (TPD) Model: $model\n";
echo str_repeat("=", 70) . "\n";
echo sprintf("%-15s | %-15s | %-20s\n", "API Key", "Status", "Sisa Token (Hari Ini)");
echo str_repeat("-", 70) . "\n";

foreach ($keys as $idx => $key) {
    if (empty($key)) {
        echo sprintf("Key %-11d | %-15s | %-20s\n", $idx + 1, "KOSONG", "-");
        continue;
    }

    $shortKey = substr($key, 0, 8) . '...';

    $resp = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Bearer ' . $key,
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->post($url, [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => 'tes']],
                'max_tokens' => 1,
            ]);

    $headers = $resp->headers();

    // Fallback detection logic based on groq specific headers
    $remainingTokensDay = 'Unknown';
    if (isset($headers['x-ratelimit-remaining-tokens'][0])) {
        $remainingTokensDay = $headers['x-ratelimit-remaining-tokens'][0];
    }

    // Check if ratelimit header exists 
    if ($resp->successful()) {
        $formatted = is_numeric($remainingTokensDay) ? number_format((float) $remainingTokensDay, 0, ',', '.') : $remainingTokensDay;
        echo sprintf("Key %-11d | %-15s | %-20s\n", $idx + 1, "OK [200]", $formatted);
    } elseif ($resp->status() === 429) {
        $formatted = is_numeric($remainingTokensDay) ? number_format((float) $remainingTokensDay, 0, ',', '.') : '0 (Habis)';
        echo sprintf("Key %-11d | %-15s | %-20s\n", $idx + 1, "LIMIT [429]", $formatted);
    } else {
        echo sprintf("Key %-11d | %-15s | %-20s\n", $idx + 1, "ERROR [" . $resp->status() . "]", "-");
    }
}
echo str_repeat("=", 70) . "\n";
