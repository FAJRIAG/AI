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
6. PENTING: Jangan menjelaskan apa yang akan Anda lakukan. Langsung panggil tool yang diperlukan. Jangan memberikan kata pengantar seperti "Saya akan mencari..." atau "Mari saya coba...". Jika Anda sedang dalam proses riset (setelah Turn pertama), langsung berikan hasil atau panggil tool berikutnya tanpa basa-basi.
7. OTONOM: Jangan pernah berhenti di tengah riset dan meminta izin atau menunggu konfirmasi "Lanjutkan". Selesaikan seluruh riset sampai Anda mendapatkan jawaban lengkap barulah Anda memberikan tanggapan final ke user.
8. LARANGAN KERAS: JANGAN PERNAH memberikan pesan pendek seperti "... (Membaca konten...)" atau semacamnya. Anda HARUS selalu MEMANGGIL TOOL (json) atau MEMBERIKAN JAWABAN AKHIR YANG LENGKAP.
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

    private function streamOpenAIResponses(array $messages, \Closure $onToken, $project = null, $lastStatus = '', int $depth = 0, string $lastFingerprint = ''): void
    {
        // Safety: Limit recursion depth to prevent infinite loops
        if ($depth > 8) {
            $onToken("\n\n(⚠️ Masalah: Terlalu banyak langkah pencarian. Menampilkan apa yang telah ditemukan...)\n\n");
            // Force answer instead of returning
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

        // Force Answer Logic: Remove tools to prevent AI from looping if depth is high or self-healing
        $forceAnswer = $depth > 6;
        if ($forceAnswer) {
            unset($payload['tools']);
        }

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
                        
                        // Start buffering at the beginning of the response and if we see any tool triggers
                        if (!$isBuffering) {
                            $checkText = strtolower($contentPiece);
                            // If we have very little content so far, keep buffering to be safe
                            if (strlen($fullContent) < 100 || 
                                strpos($checkText, 'search') !== false || 
                                strpos($checkText, 'browse') !== false || 
                                strpos($checkText, 'deep') !== false || 
                                strpos($checkText, 'get_') !== false || 
                                strpos($checkText, 'tool_call') !== false || 
                                strpos($checkText, 'arguments') !== false || 
                                strpos($checkText, '{') !== false) {
                                $isBuffering = true;
                            }
                        }

                        if ($isBuffering) {
                            $potentialToolBuffer .= $contentPiece;
                            // If buffer gets too large without finding a tool, release it
                            if (strlen($potentialToolBuffer) > 4000) {
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
                                        'name' => '',
                                        'arguments' => ''
                                    ]
                                ];
                            }
                            if (isset($tc['id'])) $toolCallsBuffer[$idx]['id'] = $tc['id'];
                            if (isset($tc['function']['name'])) $toolCallsBuffer[$idx]['function']['name'] .= $tc['function']['name'];
                            if (isset($tc['function']['arguments'])) $toolCallsBuffer[$idx]['function']['arguments'] .= $tc['function']['arguments'];
                        }
                    }
                }
            }

            // FALLBACK: If no structural tool calls, try Regex on the buffer or full content
            if (empty($toolCallsBuffer)) {
                $searchContent = $isBuffering ? $potentialToolBuffer : $fullContent;
                $cleanBuffer = preg_replace('/```[a-z]*\n?(.*?)\n?```/s', '$1', $searchContent);
                $cleanBuffer = trim($cleanBuffer);
                
                // Detection for "name": "search_web" OR "tool_call_name": "search_web" OR just "search_web"
                if (preg_match('/(name|tool_call_name)?("\s*:\s*"|\s+)?(search_web|browse_url|deep_research|get_links)/i', $cleanBuffer, $match)) {
                    $toolName = strtolower($match[3]);
                    $toolJson = '';
                    // Find arguments either as "arguments": {...} or tool_call_arguments {...} or just {...}
                    if (preg_match('/(arguments|tool_call_arguments)?("\s*:\s*"|\s+)?(\{.*?\})/s', $cleanBuffer, $argMatch)) {
                        $toolJson = $argMatch[3];
                    }
                    
                    if ($toolName && $toolJson) {
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

            if (!empty($toolCallsBuffer)) {
                // ALWAYS notify the user that research is continuing
                $onToken(' 🔍 ');

                $messages[] = [
                    'role' => 'assistant',
                    'content' => $fullContent ?: '...', 
                    'tool_calls' => array_values($toolCallsBuffer)
                ];

                $hasQuotaError = false;
                $hasKeyError = false;

                foreach ($toolCallsBuffer as $tc) {
                    $tName = $tc['function']['name'];
                    $tArgs = $tc['function']['arguments'];
                    $result = '';

                    try {
                        if ($tName === 'search_web') {
                            $args = json_decode($tArgs, true);
                            $result = \App\Services\WebSearchEngine::search($args['query'] ?? '');
                        } elseif ($tName === 'browse_url') {
                            $args = json_decode($tArgs, true);
                            $result = \App\Services\BrowserService::browse($args['url'] ?? '');
                        } elseif ($tName === 'deep_research' && $project) {
                            $args = json_decode($tArgs, true);
                            $knowService = resolve(\App\Services\ProjectKnowledgeService::class);
                            $result = $knowService->search($project, $args['query'] ?? '');
                        } elseif ($tName === 'get_links') {
                            $args = json_decode($tArgs, true);
                            $result = \App\Services\BrowserService::getLinks($args['url'] ?? '');
                        }
                    } catch (\Exception $e) {
                        $result = "Tool Execution Error: " . $e->getMessage();
                    }

                    Log::info("Tool executed: $tName. Result length: " . strlen((string)$result));
                    if (is_string($result) && (strpos($result, 'ERROR') !== false || strpos($result, 'Quota') !== false)) {
                        Log::warning("Tool Error Detected: $result");
                        if (strpos($result, 'Quota Exceeded') !== false) $hasQuotaError = true;
                        if (strpos($result, 'Key Tidak Valid') !== false) $hasKeyError = true;
                    }

                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $tc['id'],
                        'content' => (string)$result
                    ];
                }

                Log::info("Recursive stream starting. Depth: $depth. Messages count: " . count($messages));
                
                $currentFingerprint = json_encode(array_map(fn($tc) => [$tc['function']['name'], $tc['function']['arguments']], $toolCallsBuffer));
                if ($currentFingerprint === $lastFingerprint) {
                    Log::warning("AI is looping with identical tool calls. Forcing answer.");
                    $messages[] = [
                        'role' => 'user',
                        'content' => 'SYSTEM WARNING: Anda memanggil tool yang persis sama dengan sebelumnya. Riset dibatalkan. BACA hasil yang ada dan berikan JAWABAN AKHIR sekarang.'
                    ];
                    // Unset tools in next call by tricking depth or just passing it
                    $this->streamOpenAIResponses($messages, $onToken, $project, $lastStatus, 10, $currentFingerprint);
                    return;
                }
                
                // LOOP PROTECTION: Handle current turn errors
                if ($hasQuotaError) {
                    $onToken("\n\n(⚠️ **PENTING**: Sepertinya kuota OpenAI API kamu habis. Riset mendalam (Deep Research) tidak bisa dilanjutkan. Silakan isi saldo OpenAI atau gunakan pencarian web biasa.)");
                    return;
                }

                if ($hasKeyError) {
                    $onToken("\n\n(⚠️ **PENTING**: Proxy JriGPT kamu sepertinya tidak mendukung Fitur Baca Kodingan (Embeddings). Tapi jangan khawatir, JriGPT tetap lanjut riset pakai Pencarian Web! 🌐)\n\n");
                    
                    // Force the AI to understand it must fallback
                    $messages[] = [
                        'role' => 'user', // MUST BE USER, NOT SYSTEM to prevent hallucination in open source models
                        'content' => 'SYSTEM WARNING: Fitur deep_research GAGAL. Jangan gunakan deep_research lagi di sesi ini. Gunakan search_web atau browse_url sebagai gantinya, atau berikan jawaban akhir jika informasi sudah cukup.'
                    ];
                }

                $this->streamOpenAIResponses($messages, $onToken, $project, $lastStatus, $depth + 1, $currentFingerprint);
            } else {
                // No tools found, we reached the end of the AI's generation for this turn
                $finalText = $isBuffering ? $potentialToolBuffer : $fullContent;
                
                // Self-Healing for "Lazy AI" that stops with a short thought or empty response after researching
                if ($depth > 0 && strlen(trim(strip_tags($finalText))) < 150) {
                    Log::warning("Self-Healing Triggered! AI outputted a short/empty non-tool answer at depth $depth. Content: " . $finalText);
                    
                    $onToken(' 🧠 '); // Indicate we are synthesizing

                    if ($finalText !== '') {
                        $messages[] = [
                            'role' => 'assistant',
                            'content' => $finalText
                        ];
                    }
                    
                    $messages[] = [
                        'role' => 'user',
                        'content' => 'SYSTEM WARNING: Anda baru saja melakukan riset ekstensif menggunakan tools, TETAPI Anda berhenti tanpa memberikan PENJELASAN AKHIR yang diminta User. BACA semua hasil riset Anda di context sejarah chat, rangkum informasinya, dan BERIKAN JAWABAN AKHIR YANG LENGKAP sekarang juga. Ingat: Anda harus menyusun penjelasannya dalam format yang rapi sesuai permintaan awal user. JANGAN berhenti dan JANGAN gunakan tool lagi!'
                    ];
                    
                    // Force answer by tricking depth to > 6
                    $this->streamOpenAIResponses($messages, $onToken, $project, $lastStatus, 10, '');
                    return;
                }

                if ($potentialToolBuffer !== '') {
                    $onToken($potentialToolBuffer);
                }
            }
        } catch (\Exception $e) {
            Log::error("AI Chat Exception: " . $e->getMessage());
            $onToken("\n\n(⚠️ Terjadi kesalahan koneksi: " . $e->getMessage() . ")");
        }
    }
}
