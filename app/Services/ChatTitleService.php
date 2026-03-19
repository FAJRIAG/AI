<?php

namespace App\Services;

use App\Models\ChatSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatTitleService
{
    /**
     * Generate and save title for public (session-based) chat.
     */
    public function generateForPublic(string $sid, array $history): ?string
    {
        Log::info("ChatTitleService: generateForPublic called for SID: $sid");
        $sessions = session('pub_sessions', []);
        
        if (!isset($sessions[$sid])) {
            Log::info("ChatTitleService: SID $sid not found in session.");
            return null;
        }

        $currentTitle = strtolower(trim($sessions[$sid]['title'] ?? ''));
        Log::info("ChatTitleService: Current Title for SID $sid is '$currentTitle'");

        // Only generate if title is "new chat" or "untitled"
        if (!in_array($currentTitle, ['new chat', 'untitled', ''])) {
            Log::info("ChatTitleService: Skipping generation for SID $sid. Title is already set to something else.");
            return null;
        }

        // Need at least one message from user and ideally one from AI
        if (count($history) < 2) {
            Log::info("ChatTitleService: History too short (count: " . count($history) . ") for SID $sid.");
            return null;
        }

        $title = $this->queryAiForTitle($history);
        
        if ($title) {
            $sessions = session('pub_sessions', []); // Refetch 
            if (isset($sessions[$sid])) {
                $sessions[$sid]['title'] = $title;
                session(['pub_sessions' => $sessions]);
                session()->save();
                Log::info("Smart Title Generated (Public): '$title' for SID: $sid");
                return $title;
            }
        } else {
            Log::warning("ChatTitleService: AI failed to generate title for SID $sid.");
        }

        return null;
    }

    /**
     * Generate and save title for VIP (database-backed) chat.
     */
    public function generateForVip(ChatSession $session): ?string
    {
        Log::info("ChatTitleService: generateForVip called for Session ID: " . $session->id);
        $currentTitle = strtolower(trim($session->title ?? ''));
        Log::info("ChatTitleService: Current Title for Session " . $session->id . " is '$currentTitle'");

        // Only generate if title is "new chat" or "untitled"
        if (!in_array($currentTitle, ['new chat', 'untitled', ''])) {
            Log::info("ChatTitleService: Skipping generation for Session " . $session->id . ". Title is already set.");
            return null;
        }

        $history = $session->messages()->orderBy('id', 'asc')->get()->map(fn($m) => [
            'role' => $m->role,
            'content' => $m->content
        ])->toArray();

        if (count($history) < 1) { // At least user message
            Log::info("ChatTitleService: History is empty for Session " . $session->id);
            return null;
        }

        $title = $this->queryAiForTitle($history);
        
        if ($title) {
            $session->title = $title;
            $session->save();
            Log::info("Smart Title Generated (VIP): '$title' for Session ID: " . $session->id);
            return $title;
        } else {
            Log::warning("ChatTitleService: AI failed to generate title for Session " . $session->id);
        }

        return null;
    }

    /**
     * Query AI to generate a short title from history.
     */
    private function queryAiForTitle(array $history): ?string
    {
        $keyManager = new AiKeyManager();
        $apiKey = $keyManager->getCurrentKey();
        $apiBase = rtrim(config('ai.api_base', 'https://api.groq.com/openai/v1'), '/');
        $model = config('ai.model', 'openai/gpt-oss-120b');

        if (!$apiKey) return null;

        // Take only first few messages to save context and speed up
        $relevantHistory = array_slice($history, 0, 4);
        $contextText = "";
        foreach ($relevantHistory as $msg) {
            $role = ucfirst($msg['role']);
            $content = is_array($msg['content']) ? ($msg['content'][0]['text'] ?? '') : $msg['content'];
            $contextText .= "$role: $content\n";
        }

        $prompt = "Tugas Anda adalah membuat judul singkat (maksimal 3-6 kata) yang representatif untuk percakapan berikut.
        
KONTEKS PERCAKAPAN:
$contextText

ATURAN:
1. JANGAN gunakan tanda kutip.
2. JANGAN gunakan titik di akhir judul.
3. Berikan judul LANGSUNG dalam format teks biasa tanpa embel-embel.
4. Judul harus dalam bahasa yang sama dengan pesan pengguna.
5. JIKA bahasanya tidak jelas, gunakan Bahasa Indonesia.";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ])->post("$apiBase/chat/completions", [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.5,
                'max_tokens' => 50,
            ]);

            if ($response->successful()) {
                $content = trim($response->json('choices.0.message.content'));
                // Remove potential quotes or trailing dots
                $content = trim($content, '"\' ');
                $content = rtrim($content, '.');
                return $content ?: null;
            }
        } catch (\Exception $e) {
            Log::error("Chat Title Generation Error: " . $e->getMessage());
        }

        return null;
    }
}
