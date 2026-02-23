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
        if ($provider === 'groq' || $provider === 'openai') {
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
        $maxRetries = 10;
        $attempt = 0;
        $resp = null;

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
                    'timeout' => 0,
                ])
                ->post($url, $payload);

            if ($resp->successful()) {
                break;
            }

            Log::warning("AI request failed with status " . $resp->status() . " using key index " . ($attempt + 1) . ". Rotating key and retrying... Response: " . $resp->body());
            $keyManager->rotateKey();
            $attempt++;

            if ($attempt >= $maxRetries) {
                if ($resp->status() === 429) {
                    $onToken("\n\n(⚠️ 2jt token sudah habis)\n");
                }
                return;
            }
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
    }
}
