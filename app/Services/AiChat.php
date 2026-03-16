<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChat
{
    /**
     * Stream jawaban model sebagai potongan token.
     * $messages = [
     *   ['role'=>'system','content'=>'...'],
     *   ['role'=>'user','content'=>'...'],
     *   ['role'=>'assistant','content'=>'...'],
     *   ...
     * ]
     *
     * $onToken menerima string token setiap kali ada delta baru.
     */
    public function stream(array $messages, \Closure $onToken): void
    {
        $provider = strtolower(config('ai.provider', 'groq'));
        // Saat ini kamu pakai OpenAI — panggil Responses API:
        if ($provider === 'groq' || $provider === 'openai' || $provider === 'jrigpt') {
            $this->streamOpenAIResponses($messages, $onToken);
            return;
        }

        // Fallback: tetap pakai OpenAI Responses
        $this->streamOpenAIResponses($messages, $onToken);
    }

    /**
     * OpenAI Responses API (SSE streaming).
     * Endpoint: POST https://api.groq.com/openai/v1/chat/completions
     * Payload: { model, messages|input, stream: true }
     */
    private function streamOpenAIResponses(array $messages, \Closure $onToken): void
    {
        // Prevent PHP execution timeout for long generations
        @set_time_limit(0);
        @ini_set('max_execution_time', 0);

        $url = rtrim(config('ai.api_base', 'https://api.groq.com/openai/v1'), '/') . '/chat/completions';
        $model = config('ai.model', 'openai/gpt-oss-120b');

        // Payload memakai gaya "messages" (chat-like)
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => (float) config('ai.temperature', 0.4),
            'top_p' => 0.5,
            'frequency_penalty' => 0.05,
            'presence_penalty' => 0.0,
            'stream' => true,
        ];

        $keyManager = new AiKeyManager();
        $maxRetries = 3; // Reduced from 10 to prevent long blocking
        $attempt = 0;
        $resp = null;

        try {
            while ($attempt < $maxRetries) {
                $apiKey = $keyManager->getCurrentKey();

                if (!$apiKey) {
                    $onToken("\n\n(⚠️ Tidak ada API Key yang tersedia)");
                    return;
                }

                $resp = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                    ->withOptions([
                        'stream' => true,
                        'timeout' => 300, 
                    ])
                    ->post($url, $payload);

                if ($resp->successful()) {
                    break;
                }

                $errorStatus = $resp->status();
                $errorBody = $resp->body();
                Log::warning("AI request failed with status $errorStatus using key index " . ($attempt + 1) . ". Rotating key and retrying... Response: " . mb_strimwidth($errorBody, 0, 500));

                // Handle specific API status codes from JriGPT Documentation
                if ($errorStatus === 402) {
                    $onToken("\n\n(⚠️ Saldo tidak mencukupi. Silakan lakukan top up terlebih dahulu di Dashboard AI.)");
                    return;
                }

                if ($errorStatus === 403) {
                    $onToken("\n\n(⚠️ Akses Ditolak. Payload terlalu panjang atau melebihi batas pesan.)");
                    return;
                }

                // Handle server timeout (504) or overload (503) explicitly
                if ($errorStatus === 504 || $errorStatus === 503) {
                    $onToken("\n\n(⚠️ Server AI sedang sibuk atau mengalami timeout (Error $errorStatus). Silakan coba lagi nanti.)");
                    return;
                }

                // Handle specific API errors in JSON
                $errObj = json_decode($errorBody, true);
                if (isset($errObj['detail'])) {
                    $onToken("\n\n(⚠️ " . $errObj['detail'] . ")");
                    return;
                }

                $keyManager->rotateKey();
                $attempt++;

                if ($attempt >= $maxRetries) {
                    if ($errorStatus === 429) {
                        $onToken("\n\n(⚠️ Limit token/request sudah habis (429))\n");
                    } elseif ($errorStatus === 400) {
                        $onToken("\n\n(⚠️ Request tidak valid. Periksa format pesan (400))");
                    } else {
                        $onToken("\n\n(⚠️ Gagal menghubungi server AI setelah beberapa kali mencoba (Error $errorStatus))");
                    }
                    return;
                }
            }

            // Jika respons bukan streaming (application/json)
            if (str_contains($resp->header('Content-Type'), 'application/json')) {
                $obj = $resp->json();
                
                // Check if it's an error although status is 200
                if (isset($obj['error'])) {
                    $msg = $obj['error']['message'] ?? (is_string($obj['error']) ? $obj['error'] : 'Unknown API Error');
                    $onToken("\n\n(⚠️ $msg)");
                    return;
                }
                
                if (isset($obj['detail'])) {
                    $onToken("\n\n(⚠️ " . $obj['detail'] . ")");
                    return;
                }

                $content = $obj['choices'][0]['message']['content'] ?? '';
                if ($content !== '') {
                    $onToken($content);
                } else {
                    Log::error("AI Empty Response Body: " . json_encode($obj));
                    $onToken("\n\n(⚠️ API mengembalikan respons kosong atau error)");
                }
                return;
            }

            $body = $resp->toPsrResponse()->getBody();
            $buffer = '';

            while (!$body->eof()) {
                $chunk = $body->read(8192);
                if ($chunk === '') {
                    usleep(10_000);
                    continue;
                }

                $buffer .= $chunk;
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = substr($buffer, 0, $pos);
                    $buffer = substr($buffer, $pos + 1);

                    $line = trim($line);
                    if ($line === '' || str_starts_with($line, ':'))
                        continue;

                    if (!str_starts_with($line, 'data: '))
                        continue;

                    $jsonStr = substr($line, 6);
                    if ($jsonStr === '[DONE]') {
                        break 2;
                    }

                    $obj = json_decode($jsonStr, true);
                    if (!is_array($obj))
                        continue;

                    // Standard OpenAI/Groq streaming format: choices[0].delta.content
                    $delta = $obj['choices'][0]['delta']['content'] ?? '';
                    if ($delta !== '') {
                        $onToken($delta);
                    }

                    if (isset($obj['error'])) {
                        $msg = $obj['error']['message'] ?? 'Unknown error';
                        $onToken("\n\n(⚠️ $msg)");
                        break 2;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("AI Chat Exception: " . $e->getMessage());
            $onToken("\n\n(⚠️ Terjadi kesalahan koneksi: " . $e->getMessage() . ")");
        }
    }
}

