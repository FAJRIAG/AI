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

echo "Cek Limit Groq Model: $model\n";
echo str_repeat("=", 85) . "\n";
echo sprintf("%-10s | %-12s | %-25s | %-25s\n", "LLM", "Status", "Sisa Token per Menit", "Sisa Request per Hari");
echo str_repeat("-", 85) . "\n";

foreach ($keys as $idx => $key) {
    if (empty($key)) {
        echo sprintf("llm %-6d | %-12s | %-25s | %-25s\n", $idx + 1, "KOSONG", "-", "-");
        continue;
    }

    $shortKey = substr($key, 0, 8) . '...';

    $resp = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Bearer ' . $key,
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->post($url, [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => 'limit check']],
                'max_tokens' => 1,
            ]);

    $headers = $resp->headers();

    // Groq track "limit-tokens" as TPM (Tokens Per Minute = 8000)
    $remainingTPM = $headers['x-ratelimit-remaining-tokens'][0] ?? 'Unknown';
    $limitTPM = $headers['x-ratelimit-limit-tokens'][0] ?? '8000';

    // Groq track "limit-requests" as RPD (Requests Per Day = 1000)
    $remainingRPD = $headers['x-ratelimit-remaining-requests'][0] ?? 'Unknown';
    $limitRPD = $headers['x-ratelimit-limit-requests'][0] ?? '1000';

    if ($resp->successful()) {
        $strTPM = is_numeric($remainingTPM) ? number_format((float) $remainingTPM, 0, ',', '.') . " / " . number_format((float) $limitTPM, 0, ',', '.') : $remainingTPM;
        $strRPD = is_numeric($remainingRPD) ? number_format((float) $remainingRPD, 0, ',', '.') . " / " . number_format((float) $limitRPD, 0, ',', '.') : $remainingRPD;

        echo sprintf("llm %-6d | %-12s | %-25s | %-25s\n", $idx + 1, "OK [200]", $strTPM, $strRPD);
    } elseif ($resp->status() === 429) {
        echo sprintf("llm %-6d | %-12s | %-25s | %-25s\n", $idx + 1, "LIMIT [429]", "0 (Habis)", "0 (Habis)");
    } else {
        echo sprintf("llm %-6d | %-12s | %-25s | %-25s\n", $idx + 1, "ERR [" . $resp->status() . "]", "-", "-");
    }
}
echo str_repeat("=", 85) . "\n";
