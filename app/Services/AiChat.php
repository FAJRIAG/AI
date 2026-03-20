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
    public function stream(array $messages, \Closure $onToken, string $mood = 'calm'): void
    {
        // Hubungkan mood ke instruksi nada di system prompt
        $toneInstruction = \App\Services\SentimentService::getToneInstruction($mood);
        if (!empty($toneInstruction)) {
            // Cari pesan system yang ada atau tambahkan di awal
            $foundSystem = false;
            foreach ($messages as &$msg) {
                if ($msg['role'] === 'system') {
                    $msg['content'] .= "\n\n" . $toneInstruction;
                    $foundSystem = true;
                    break;
                }
            }
            if (!$foundSystem) {
                array_unshift($messages, ['role' => 'system', 'content' => $toneInstruction]);
            }
        }

        // TAMBAHKAN INSTRUKSI WEB AGENT KE SYSTEM PROMPT
        $agenticPrompt = "\n\nKEMAMPUAN WEB AGENT:
1. Gunakan `search_web` untuk mencari informasi umum atau menemukan URL yang relevan.
2. Gunakan `browse_url` untuk mengunjungi URL spesifik guna membaca isi lengkap halaman tersebut. Ini sangat berguna jika informasi yang Anda butuhkan (seperti harga detail, stok, atau isi berita lengkap) tidak ada di ringkasan hasil pencarian.
3. Anda bisa melakukan beberapa kali pencarian atau kunjungan halaman secara berurutan untuk menyelesaikan tugas yang kompleks.";

        foreach ($messages as &$msg) {
            if ($msg['role'] === 'system') {
                $msg['content'] .= $agenticPrompt;
                break;
            }
        }

        $provider = strtolower(config('ai.provider', 'groq'));
        // Saat ini kamu pakai OpenAI — panggil Responses API:
        if ($provider === 'groq' || $provider === 'openai' || $provider === 'jrigpt') {
            $this->streamOpenAIResponses($messages, $onToken);
            return;
        }

        // Fallback: tetap pakai OpenAI Responses
        $lastStatus = '';
        $this->streamOpenAIResponses($messages, $onToken, $lastStatus);
    }

    /**
     * OpenAI Responses API (SSE streaming).
     * Endpoint: POST https://api.groq.com/openai/v1/chat/completions
     * Payload: { model, messages|input, stream: true }
     */
    private function streamOpenAIResponses(array $messages, \Closure $onToken, string &$lastStatus = ''): void
    {
        // Prevent PHP execution timeout for long generations
        @set_time_limit(0);
        @ini_set('max_execution_time', 0);

        $url = rtrim(config('ai.api_base', 'https://api.groq.com/openai/v1'), '/') . '/chat/completions';
        $model = config('ai.model', 'openai/gpt-oss-120b');
        
        // Deteksi apakah ada input gambar di dalam messages
        $hasImage = false;
        foreach ($messages as $msg) {
            if (is_array($msg['content'])) {
                foreach ($msg['content'] as $part) {
                    if (isset($part['type']) && $part['type'] === 'image_url') {
                        $hasImage = true;
                        break 2;
                    }
                }
            }
        }

        // Jika terdeteksi gambar, PAKSA alihkan ke model Vision, 
        // karena model text-only tidak bisa membaca array gambar.
        if ($hasImage) {
            $model = config('ai.vision_model', 'llama-3.2-90b-vision-preview'); 
        }

        // Payload memakai gaya "messages" (chat-like)
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => (float) config('ai.temperature', 0.4),
            'top_p' => 0.5,
            'frequency_penalty' => 0.05,
            'presence_penalty' => 0.0,
            'stream' => true,
            'tools' => [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'search_web',
                        'description' => 'Cari informasi terkini dari internet. Gunakan fungsi ini untuk mencari hal-hal baru, harga saham terkini, cuaca, atau informasi aktual yang tidak ada di memori Anda.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => [
                                    'type' => 'string',
                                    'description' => 'Kata kunci untuk dicari di Google/DuckDuckGo, contoh: "Harga Emas Hari Ini"'
                                ]
                            ],
                            'required' => ['query']
                        ]
                    ]
                ],
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'browse_url',
                        'description' => 'Kunjungi URL spesifik untuk membaca isi lengkap halamannya dalam format Markdown. Gunakan ini setelah `search_web` jika Anda perlu detail lebih mendalam dari suatu situs.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'url' => [
                                    'type' => 'string',
                                    'description' => 'URL lengkap situs yang ingin dikunjungi (termasuk https://)'
                                ]
                            ],
                            'required' => ['url']
                        ]
                    ]
                ]
            ]
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

                // Handle server timeout (504) or overload (503)
                if ($errorStatus === 504 || $errorStatus === 503) {
                    // Jangan langsung menyerah, rotasi key dan coba lagi
                    Log::warning("AI Server busy ($errorStatus). Rotating key and retrying...");
                    usleep(500000); // Tunggu 0.5s
                } else {
                    // Handle specific API errors in JSON
                    $errObj = json_decode($errorBody, true);
                    if (isset($errObj['detail'])) {
                        $onToken("\n\n(⚠️ " . $errObj['detail'] . ")");
                        return;
                    }
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
            $toolCallsBuffer = [];
            $fullContent = '';
            
            $potentialToolBuffer = '';
            $isBuffering = false;

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

                    $delta = $obj['choices'][0]['delta'] ?? [];

                    // Standard OpenAI/Groq streaming format: choices[0].delta.content
                    if (isset($delta['content']) && $delta['content'] !== '') {
                        $contentPiece = $delta['content'];
                        $fullContent .= $contentPiece;
                        
                        // Start buffering if we see "search_web" or an opening brace which might be a JSON tool call
                        // Start buffering if we see a tool name or an opening brace
                        if (!$isBuffering && (strpos($contentPiece, 'search_web') !== false || strpos($contentPiece, 'browse_url') !== false || strpos($contentPiece, '{') !== false)) {
                            $isBuffering = true;
                        }

                        if ($isBuffering) {
                            $potentialToolBuffer .= $contentPiece;
                            // Safety: if buffer gets too large, it's probably not a tool call, stop buffering
                            if (strlen($potentialToolBuffer) > 2000) {
                                $onToken($potentialToolBuffer);
                                $potentialToolBuffer = '';
                                $isBuffering = false;
                            }
                        } else {
                            $onToken($contentPiece);
                        }
                    }

                    // Penangkapan Tool Calls (Standard format)
                    if (isset($delta['tool_calls'])) {
                        $isBuffering = true; // Still buffering tool call parts
                        foreach ($delta['tool_calls'] as $tc) {
                            $idx = $tc['index'];
                            if (!isset($toolCallsBuffer[$idx])) {
                                $toolCallsBuffer[$idx] = [
                                    'id' => $tc['id'] ?? '',
                                    'type' => 'function',
                                    'function' => [
                                        'name' => $tc['function']['name'] ?? '',
                                        'arguments' => ''
                                    ]
                                ];
                            }
                            if (isset($tc['function']['arguments'])) {
                                $toolCallsBuffer[$idx]['function']['arguments'] .= $tc['function']['arguments'];
                            }
                        }
                    }

                    if (isset($obj['error'])) {
                        $msg = $obj['error']['message'] ?? 'Unknown error';
                        $onToken("\n\n(⚠️ $msg)");
                        break 2;
                    }
                }
            } // end while

            // FALLBACK FOR TEXT-BASED TOOL CALLS (OpenClaw / LiteLLM custom proxy format)
            $isFallbackTool = false;
            if (empty($toolCallsBuffer) && preg_match('/(search_web|browse_url).*?(\{.*?\})/is', $fullContent, $matches)) {
                // Ensure the extracted JSON is valid
                $toolJson = $matches[2];
                $toolName = $matches[1];
                if (json_decode($toolJson, true)) {
                    $isFallbackTool = true;
                    $toolCallsBuffer[] = [
                        'id' => 'call_' . substr(md5(uniqid()), 0, 8),
                        'type' => 'function',
                        'function' => [
                            'name' => $toolName,
                            'arguments' => $toolJson
                        ]
                    ];
                }
            }

            // Decide what to do with the buffered text
            if (!empty($toolCallsBuffer)) {
                // It was a tool call! Discard potentialToolBuffer (preventing flicker)
                $potentialToolBuffer = ''; 
                
                // Berikan feedback visual yang tepat berdasarkan tool pertama
                $firstTool = $toolCallsBuffer[0]['function']['name'] ?? 'tool';
                $statusMsg = ($firstTool === 'search_web') 
                    ? "🔍 *(Sedang mencari informasi di internet...)* ⏳" 
                    : "🌐 *(Sedang mengunjungi website...)* ⏳";

                // Hanya kirim jika status berubah atau belum pernah kirim
                if ($lastStatus !== $statusMsg) {
                    $onToken("\n\n[HIDE_TOOL_CALL]\n$statusMsg\n\n");
                    $lastStatus = $statusMsg;
                    
                    // PAKSA FLUSH agar user langsung melihat status "Sedang..."
                    if (function_exists('ob_flush') && ob_get_level() > 0) @ob_flush();
                    @flush();
                }
                $messages[] = [
                    'role' => 'assistant',
                    'content' => $fullContent ?: '🔍 *(Menjalankan pencarian...)*', // Hindari error "Message content cannot be empty"
                    'tool_calls' => array_values($toolCallsBuffer)
                ];

                foreach ($toolCallsBuffer as $tc) {
                    if ($tc['function']['name'] === 'search_web') {
                        $args = json_decode($tc['function']['arguments'], true);
                        $query = $args['query'] ?? '';
                        $result = \App\Services\WebSearchEngine::search($query);
                        
                        $messages[] = [
                            'role' => 'tool',
                            'tool_call_id' => $tc['id'],
                            'content' => $result
                        ];
                    } elseif ($tc['function']['name'] === 'browse_url') {
                        $args = json_decode($tc['function']['arguments'], true);
                        $url = $args['url'] ?? '';
                        $result = \App\Services\BrowserService::browse($url);
                        
                        Log::info("Tool Result (" . $tc['function']['name'] . "): " . mb_strimwidth($result, 0, 500));
                        
                        $messages[] = [
                            'role' => 'tool',
                            'tool_call_id' => $tc['id'],
                            'content' => $result
                        ];
                    }
                }

                Log::info("Recursive stream starting. Messages count: " . count($messages));
                // Teruskan array messages baru yang berisi referensi tool result kembali ke AI
                $this->streamOpenAIResponses($messages, $onToken, $lastStatus);
            } else {
                // Not a tool call after all, release the buffer to the user
                if ($potentialToolBuffer !== '') {
                    $onToken($potentialToolBuffer);
                    $potentialToolBuffer = '';
                }
            }
        } catch (\Exception $e) {
            Log::error("AI Chat Exception: " . $e->getMessage());
            $onToken("\n\n(⚠️ Terjadi kesalahan koneksi: " . $e->getMessage() . ")");
        }
    }
}

