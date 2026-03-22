<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChat
{
    /**
     * @param \App\Models\Project|null $project
     */
    public function stream(array $messages, \Closure $onToken, string $mood = 'calm', $project = null): void
    {
        $provider = env('AI_PROVIDER', 'jrigpt');
        $model = env('AI_MODEL', 'jrigpt');

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
        $agenticPrompt = <<<EOT


KEMAMPUAN WEB AGENT:
1. Gunakan `search_web` untuk mencari informasi umum atau menemukan URL yang relevan.
3. Gunakan `get_links` untuk melihat daftar halaman lain di sebuah situs. Ini membantu Anda "menjelajahi" menu navigasi atau mencari halaman detail.
4. Gunakan `deep_research` untuk mencari informasi mendalam dari dokumen-dokumen internal atau kodingan yang ada di Workspace/Project saat ini. Ini adalah "Infinite Memory" Anda untuk project ini.
5. Anda adalah Web Agent Mandiri: Jika Anda tidak menemukan jawaban di halaman pertama, gunakan `get_links` untuk mencari halaman lain (seperti "Pricing", "About", atau "Details") dan kunjungi halaman tersebut.
6. PENTING: Jangan menjelaskan apa yang akan Anda lakukan. Langsung panggil tool yang diperlukan. Jangan memberikan kata pengantar seperti "Saya akan mencari..." atau "Mari kita coba...". Jika Anda butuh memanggil tool, panggil saja secara langsung.
EOT;

        foreach ($messages as &$msg) {
            if ($msg['role'] === 'system') {
                $msg['content'] .= $agenticPrompt;
                break;
            }
        }

        if ($provider === 'groq' || $provider === 'openai' || $provider === 'jrigpt') {
            $this->streamOpenAIResponses($messages, $onToken, $project);
            return;
        }

        // Fallback: tetap pakai OpenAI Responses
        $lastStatus = '';
        $this->streamOpenAIResponses($messages, $onToken, $project, $lastStatus);
    }

    private function streamOpenAIResponses(array $messages, \Closure $onToken, $project = null, $lastStatus = '', int $depth = 0): void
    {
        // Safety: Limit recursion depth to prevent infinite loops
        if ($depth > 10) {
            $onToken("\n\n(⚠️ Masalah: Terlalu banyak langkah pencarian. Coba buat pertanyaan yang lebih spesifik.)");
            return;
        }

        @set_time_limit(0);
        @ini_set('max_execution_time', 0);

        $url = rtrim(config('ai.api_base', 'https://api.groq.com/openai/v1'), '/') . '/chat/completions';
        $model = config('ai.model', 'openai/gpt-oss-120b');
        
        // Deteksi gambar
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
        if ($hasImage) $model = config('ai.vision_model', 'llama-3.2-90b-vision-preview');

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
                ],
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'deep_research',
                        'description' => 'Cari informasi teknis, algoritma, atau isi dokumen dari Workspace Knowledge Base. Gunakan ini jika pengguna menanyakan sesuatu tentang kodingan atau dokumen yang sudah di-index di proyek ini.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => [
                                    'type' => 'string',
                                    'description' => 'Kueri semantik untuk mencari di database pengetahuan, contoh: "bagaimana sistem auth bekerja?"'
                                ]
                            ],
                            'required' => ['query']
                        ]
                    ]
                ],
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'get_links',
                        'description' => 'Ambil semua daftar link yang ada di sebuah URL. Gunakan ini jika Anda ingin melihat menu navigasi atau halaman lain di sebuah website.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'url' => [
                                    'type' => 'string',
                                    'description' => 'URL lengkap website yang ingin dilihat daftar link-nya.'
                                ]
                            ],
                            'required' => ['url']
                        ]
                    ]
                ]
            ]
        ];

        $keyManager = new AiKeyManager();
        $maxRetries = 3;
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

                if ($errorStatus === 402) {
                    $onToken("\n\n(⚠️ Saldo tidak mencukupi. Silakan lakukan top up terlebih dahulu di Dashboard AI.)");
                    return;
                }

                if ($errorStatus === 403) {
                    $onToken("\n\n(⚠️ Akses Ditolak. Payload terlalu panjang atau melebihi batas pesan.)");
                    return;
                }

                if ($errorStatus === 504 || $errorStatus === 503) {
                    usleep(500000); 
                } else {
                    $errObj = json_decode($errorBody, true);
                    if (isset($errObj['detail'])) {
                        $onToken("\n\n(⚠️ " . $errObj['detail'] . ")");
                        return;
                    }
                }

                $keyManager->rotateKey();
                $attempt++;

                if ($attempt >= $maxRetries) {
                    $onToken("\n\n(⚠️ Gagal menghubungi server AI setelah beberapa kali mencoba (Error $errorStatus))");
                    return;
                }
            }

            if (str_contains($resp->header('Content-Type'), 'application/json')) {
                $obj = $resp->json();
                if (isset($obj['error'])) {
                    $msg = $obj['error']['message'] ?? (is_string($obj['error']) ? $obj['error'] : 'Unknown API Error');
                    $onToken("\n\n(⚠️ $msg)");
                    return;
                }
                $content = $obj['choices'][0]['message']['content'] ?? '';
                if ($content !== '') {
                    $onToken($content);
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
                    if ($line === '' || str_starts_with($line, ':')) continue;
                    if (!str_starts_with($line, 'data: ')) continue;

                    $jsonStr = substr($line, 6);
                    if ($jsonStr === '[DONE]') break 2;

                    $obj = json_decode($jsonStr, true);
                    if (!is_array($obj)) continue;

                    $delta = $obj['choices'][0]['delta'] ?? [];

                    if (isset($delta['content']) && $delta['content'] !== '') {
                        $contentPiece = $delta['content'];
                        $fullContent .= $contentPiece;
                        
                        // Start buffering if we see even a PART of a tool name or an opening brace
                        if (!$isBuffering) {
                            $checkText = strtolower($contentPiece);
                            if (strpos($checkText, 'search') !== false || 
                                strpos($checkText, 'browse') !== false || 
                                strpos($checkText, 'deep') !== false || 
                                strpos($checkText, 'get_') !== false || 
                                strpos($checkText, '{') !== false) {
                                $isBuffering = true;
                            }
                        }

                        if ($isBuffering) {
                            $potentialToolBuffer .= $contentPiece;
                            // If it looks like JSON starts, definitely buffer until end or failure
                            if (strlen($potentialToolBuffer) > 3000) {
                                $onToken($potentialToolBuffer);
                                $potentialToolBuffer = '';
                                $isBuffering = false;
                            }
                        } else {
                            $onToken($contentPiece);
                        }
                    }


                    if (isset($delta['tool_calls'])) {
                        $isBuffering = true; 
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
                }
            } 

            // FALLBACK FOR TEXT-BASED TOOL CALLS (Multiple support)
            if (empty($toolCallsBuffer)) {
                if (preg_match_all('/(search_web|browse_url|deep_research|get_links).*?(\{.*?\})/is', $fullContent, $allMatches, PREG_SET_ORDER)) {
                    foreach ($allMatches as $matches) {
                        $toolName = trim($matches[1]);
                        $toolJson = trim($matches[2]);
                        if (json_decode($toolJson, true)) {
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
                }
            }

            if (!empty($toolCallsBuffer)) {
                $messages[] = [
                    'role' => 'assistant',
                    'content' => $fullContent ?: '🔍 *(Menjalankan pencarian...)*', 
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
                        $messages[] = [
                            'role' => 'tool',
                            'tool_call_id' => $tc['id'],
                            'content' => $result
                        ];
                    } elseif ($tc['function']['name'] === 'deep_research' && $project) {
                        $args = json_decode($tc['function']['arguments'], true);
                        $query = $args['query'] ?? '';
                        $knowService = resolve(\App\Services\ProjectKnowledgeService::class);
                        $result = $knowService->search($project, $query);
                        $messages[] = [
                            'role' => 'tool',
                            'tool_call_id' => $tc['id'],
                            'content' => $result
                        ];
                    } elseif ($tc['function']['name'] === 'get_links') {
                        $args = json_decode($tc['function']['arguments'], true);
                        $url = $args['url'] ?? '';
                        $result = \App\Services\BrowserService::getLinks($url);
                        $messages[] = [
                            'role' => 'tool',
                            'tool_call_id' => $tc['id'],
                            'content' => $result
                        ];
                    }
                }

                Log::info("Recursive stream starting. Depth: $depth. Messages count: " . count($messages));
                $this->streamOpenAIResponses($messages, $onToken, $project, $lastStatus, $depth + 1);
            } else {
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
