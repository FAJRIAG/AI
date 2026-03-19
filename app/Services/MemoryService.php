<?php

namespace App\Services;

use App\Models\UserMemory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MemoryService
{
    /**
     * Mengekstrak fakta baru dari pesan user dan asisten.
     */
    public function extractAndStore($userId, $sessionId, $userMsg, $aiMsg)
    {
        $keyManager = new AiKeyManager();
        $apiKey = $keyManager->getCurrentKey();
        $apiBase = rtrim(config('ai.api_base', 'https://api.groq.com/openai/v1'), '/');
        $model = config('ai.model', 'openai/gpt-oss-120b');

        if (!$apiKey) return;

        $prompt = "Kamu adalah sistem pengekstrak memori. Tugasmu adalah mengambil fakta personal atau preferensi permanen pengguna dari percakapan berikut.
        
KONTEKS CHAT:
User: \"$userMsg\"
Asisten: \"$aiMsg\"

ATURAN:
1. Ekstrak HANYA fakta personal atau preferensi yang berguna untuk diingat di masa depan (misal: nama, hobi, tempat kerja, preferensi teknis).
2. Tuliskan dalam poin-poin singkat (bullet points).
3. JANGAN mengekstrak informasi yang bersifat sementara (misal: \"user sedang lapar\").
4. JIKA TIDAK ADA fakta baru yang layak diingat, balas HANYA dengan kata \"NONE\".
5. Balas LANGSUNG dengan poin-poinnya saja tanpa kalimat pembuka.";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ])->post("$apiBase/chat/completions", [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.1,
                'max_tokens' => 200,
            ]);

            if ($response->successful()) {
                $content = trim($response->json('choices.0.message.content'));
                if ($content !== 'NONE' && !empty($content)) {
                    // Simpan setiap poin sebagai fakta baru
                    $facts = explode("\n", $content);
                    foreach ($facts as $f) {
                        $cleanFact = ltrim(trim($f), '-* ');
                        if (empty($cleanFact)) continue;
                        
                        UserMemory::create([
                            'user_id' => $userId,
                            'session_id' => $sessionId,
                            'fact' => $cleanFact
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Memory Extraction Error: " . $e->getMessage());
        }
    }

    /**
     * Mendapatkan semua memori untuk user/session tertentu.
     */
    public function getMemories($userId = null, $sessionId = null)
    {
        $query = UserMemory::query();
        if ($userId) $query->where('user_id', $userId);
        elseif ($sessionId) $query->where('session_id', $sessionId);
        else return [];

        return $query->latest()->take(20)->pluck('fact')->toArray();
    }
}
