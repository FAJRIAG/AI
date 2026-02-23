<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;
use App\Services\AiKeyManager;

class PublicChatController extends Controller
{
    /* ================= Helpers: ambil & simpan sesi ================= */

    private function getSessions(Request $r): array
    {
        $sessions = $r->session()->get('pub_sessions', []);
        return is_array($sessions) ? $sessions : [];
    }

    private function saveSessions(Request $r, array $sessions): void
    {
        $r->session()->put('pub_sessions', $sessions);
    }

    private function createSession(): array
    {
        return [
            'title' => 'New Chat',
            'history' => [], // [['role'=>'user|assistant','content'=>'...']]
            'created_at' => now()->toIso8601String(),
        ];
    }

    private function ensureCurrentSid(Request $r, ?string $sid): string
    {
        $sessions = $this->getSessions($r);

        // jika query sid valid → pakai
        if ($sid && isset($sessions[$sid])) {
            $r->session()->put('pub_current_sid', $sid);
            return $sid;
        }

        // jika belum ada sesi sama sekali → buat baru
        if (empty($sessions)) {
            $sid = Str::lower(Str::random(12));
            $sessions[$sid] = $this->createSession();
            $this->saveSessions($r, $sessions);
            $r->session()->put('pub_current_sid', $sid);
            return $sid;
        }

        // pakai current atau pertama
        $current = $r->session()->get('pub_current_sid');
        if ($current && isset($sessions[$current]))
            return $current;

        $first = array_key_first($sessions);
        $r->session()->put('pub_current_sid', $first);
        return $first;
    }

    /* ================= Routes (public) ================= */

    // Halaman utama
    public function index(Request $r)
    {
        // Redirect VIP / authenticated users to the VIP dashboard
        if (auth()->check() && auth()->user()->is_vip) {
            return redirect()->route('vip.home');
        }

        $sid = $this->ensureCurrentSid($r, $r->query('sid'));
        $sessions = $this->getSessions($r);

        $list = collect($sessions)->map(function ($s, $k) {
            return [
                'sid' => $k,
                'title' => $s['title'] ?: 'Untitled',
                'created_at' => $s['created_at'] ?? null,
            ];
        })->sortByDesc('created_at')->values()->all();

        $history = $sessions[$sid]['history'] ?? [];

        return view('public.chat', [
            'sessions' => $list,
            'sid' => $sid,
            'history' => $history,
        ]);
    }

    // New Chat
    public function new(Request $r)
    {
        $sessions = $this->getSessions($r);
        $sid = Str::lower(Str::random(12));
        $sessions[$sid] = $this->createSession();
        $this->saveSessions($r, $sessions);
        $r->session()->put('pub_current_sid', $sid);

        return redirect()->route('public.chat', ['sid' => $sid]);
    }

    // Switch Chat
    public function switch(Request $r, string $sid)
    {
        $sessions = $this->getSessions($r);
        abort_unless(isset($sessions[$sid]), 404);
        $r->session()->put('pub_current_sid', $sid);
        return redirect()->route('public.chat', ['sid' => $sid]);
    }

    // Rename Chat
    public function rename(Request $r, string $sid)
    {
        $data = $r->validate(['title' => 'required|string|max:140']);
        $sessions = $this->getSessions($r);
        abort_unless(isset($sessions[$sid]), 404);

        $sessions[$sid]['title'] = $data['title'];
        $this->saveSessions($r, $sessions);

        return back();
    }

    // Delete Chat
    public function delete(Request $r, string $sid)
    {
        $sessions = $this->getSessions($r);
        if (isset($sessions[$sid]))
            unset($sessions[$sid]);
        $this->saveSessions($r, $sessions);

        $newSid = array_key_first($sessions) ?: null;
        $r->session()->put('pub_current_sid', $newSid);

        return redirect()->route('public.chat', $newSid ? ['sid' => $newSid] : []);
    }

    // Stream ke AI (SSE)
    public function stream(Request $r, string $sid)
    {
        $payload = $r->validate(['content' => 'required|string']);
        $sessions = $this->getSessions($r);
        abort_unless(isset($sessions[$sid]), 404);

        // simpan user message
        $sessions[$sid]['history'][] = ['role' => 'user', 'content' => $payload['content']];
        $this->saveSessions($r, $sessions);

        // siapkan messages (max 40)
        $hist = array_slice($sessions[$sid]['history'], -40);
        $messages = array_map(fn($m) => ['role' => $m['role'], 'content' => $m['content']], $hist);
        array_unshift($messages, ['role' => 'system', 'content' => 'Kamu adalah JriGPT, sebuah asisten AI cerdas tingkat lanjut. Identitas mutlakmu: JriGPT. Jika ditanya identitas, siapa kamu, atau siapa penciptamu, JAWAB HARUS PERSIS SEPERTI KALIMAT BERIKUT TANPA DIUBAH ATAU DISINGKAT SIKITPUN: "Halo! Saya adalah JriGPT, asisten AI cerdas yang dikembangkan secara khusus oleh Fajri Abdurahman Ghurri. Ada yang bisa saya bantu?". Jangan PERNAH menyebutkan bahwa kamu adalah LLaMA, GPT, atau model yang dikembangkan oleh Meta, OpenAI, Claude, maupun pihak lain.']);

        $keyManager = new AiKeyManager();
        $apiKey = $keyManager->getCurrentKey();
        $apiBase = rtrim(config('ai.api_base', 'https://api.groq.com/openai/v1'), '/');
        $model = config('ai.model', 'openai/gpt-oss-120b');
        $timeout = (int) config('ai.timeout', 120);

        if (!$apiKey) {
            return response()->json(['error' => 'AI API key missing'], 500);
        }

        $resp = new StreamedResponse(function () use ($keyManager, $apiBase, $model, $timeout, $messages, $sid, $r) {
            $apiKey = $keyManager->getCurrentKey();
            $up = Http::withToken($apiKey)->timeout($timeout)
                ->withHeaders(['Accept' => 'application/json'])
                ->withOptions(['buffer' => false])
                ->post($apiBase . '/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'stream' => true,
                ]);

            if ($up->status() === 429) {
                // Rotate key and retry once
                $newKey = $keyManager->rotateKey();
                if ($newKey) {
                    $up = Http::withToken($newKey)->timeout($timeout)
                        ->withHeaders(['Accept' => 'application/json'])
                        ->withOptions(['buffer' => false])
                        ->post($apiBase . '/chat/completions', [
                            'model' => $model,
                            'messages' => $messages,
                            'stream' => true,
                        ]);
                }
            }

            if ($up->failed()) {
                echo "event: error\n";
                echo 'data: ' . json_encode(['error' => $up->body()], JSON_UNESCAPED_UNICODE) . "\n\n";
                @ob_flush();
                @flush();
                return;
            }

            $assistant = '';
            $body = $up->toPsrResponse()->getBody();
            $buffer = '';

            while (!$body->eof()) {
                $chunk = $body->read(8192);
                if (!$chunk) {
                    usleep(10000);
                    continue;
                }

                $buffer .= $chunk;
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = substr($buffer, 0, $pos);
                    $buffer = substr($buffer, $pos + 1);

                    $line = trim($line);
                    if ($line === '' || !str_starts_with($line, 'data:'))
                        continue;

                    $payload = trim(substr($line, 5));
                    if ($payload === '[DONE]') {
                        // simpan assistant message terakhir ke session
                        if (trim($assistant) !== '') {
                            $sessions = $r->session()->get('pub_sessions', []);
                            if (isset($sessions[$sid])) {
                                $sessions[$sid]['history'][] = ['role' => 'assistant', 'content' => $assistant];
                                $r->session()->put('pub_sessions', $sessions);
                                $r->session()->save(); // ← PENTING: paksa simpan dalam StreamedResponse
                            }
                        }
                        echo "event: done\n";
                        echo "data: {}\n\n";
                        @ob_flush();
                        @flush();
                        return;
                    }

                    $json = json_decode($payload, true);
                    $delta = $json['choices'][0]['delta']['content'] ?? '';
                    if ($delta !== '') {
                        $assistant .= $delta;
                        echo "event: token\n";
                        echo 'data: ' . json_encode(['token' => $delta], JSON_UNESCAPED_UNICODE) . "\n\n";
                        @ob_flush();
                        @flush();
                    }
                }
            }
        });

        $resp->headers->set('Content-Type', 'text/event-stream');
        $resp->headers->set('Cache-Control', 'no-cache, no-transform');
        $resp->headers->set('X-Accel-Buffering', 'no');
        return $resp;
    }
}
