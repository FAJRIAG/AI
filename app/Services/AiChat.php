<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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
        $provider = strtolower(env('AI_PROVIDER', 'groq'));
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
        $url = env('AI_API_BASE', 'https://api.groq.com/openai/v1') . '/chat/completions';
        $model = env('AI_MODEL', 'llama-3.3-70b-versatile');

        // Payload memakai gaya "messages" (chat-like)
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => (float) env('AI_TEMPERATURE', 0.2),
            'stream' => true,
        ];

        $resp = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])
            ->withOptions([
                'stream' => true,
                'timeout' => 0,     // jangan time out saat streaming
            ])
            ->post($url, $payload);

        // Baca SSE line-by-line
        $body = $resp->toPsrResponse()->getBody();

        while (!$body->eof()) {
            $chunk = $body->read(8192);
            if ($chunk === '') {
                usleep(10_000);
                continue;
            }

            // Server mengirim beberapa baris; kita proses tiap baris
            foreach (preg_split("/\r\n|\n|\r/", $chunk) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, ':'))
                    continue;

                // OpenAI Responses streaming mengirim "data: {json}" / "[DONE]"
                if (!str_starts_with($line, 'data: '))
                    continue;
                $json = substr($line, 6);

                if ($json === '[DONE]') {
                    break 2; // selesai
                }

                $obj = json_decode($json, true);
                if (!is_array($obj))
                    continue;

                // Event delta teks:
                // type: "response.output_text.delta" -> field "delta"
                $type = $obj['type'] ?? null;

                if ($type === 'response.output_text.delta') {
                    $delta = $obj['delta'] ?? '';
                    if ($delta !== '') {
                        $onToken($delta);
                    }
                } elseif ($type === 'response.error') {
                    $msg = $obj['error']['message'] ?? 'OpenAI error';
                    $onToken("\n\n(⚠️ $msg)");
                } elseif ($type === 'response.completed') {
                    break 2;
                }
                // (Event lain seperti tool-calls bisa ditangani sesuai kebutuhan ke depan.)
            }
        }
    }
}
