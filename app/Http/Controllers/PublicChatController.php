<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;
use App\Services\AiKeyManager;
use App\Services\AiChat;

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

        // siapkan messages (max 15 agar payload tidak 413)
        $hist = array_slice($sessions[$sid]['history'], -15);
        $messages = array_map(fn($m) => [
            'role' => $m['role'],
            'content' => mb_strimwidth($m['content'], 0, 2000, "..."), // Truncate content
        ], $hist);
        array_unshift($messages, [
            'role' => 'system',
            'content' => 'Kamu adalah JriGPT, sebuah asisten AI cerdas tingkat lanjut. Identitas mutlakmu: JriGPT. Jika ditanya identitas, siapa kamu, atau siapa penciptamu, JAWAB HARUS PERSIS SEPERTI KALIMAT BERIKUT TANPA DIUBAH ATAU DISINGKAT SIKITPUN: "Halo! Saya adalah JriGPT, asisten AI cerdas yang dikembangkan secara khusus oleh Fajri Abdurahman Ghurri. Ada yang bisa saya bantu?".

ATURAN KETAT IDENTITAS & KEMAMPUAN:
1. Kamu sepenuhnya berbasis teks dan TIDAK memiliki kemampuan untuk melihat, membaca, atau menerima input berupa gambar/foto (vision). Jika pengguna bertanya atau meminta kamu melihat gambar, tegaskan dengan sopan bahwa kamu adalah model AI berbasis teks dan tidak bisa melihat gambar.
2. Jangan pernah menyebut OpenAI, GPT-4, Llama, Anthropic, atau entitas/model AI pihak ketiga lain. Kamu dikembangkan secara eksklusif dan mandiri oleh Fajri Abdurahman Ghurri.

ATURAN FORMAT MATEMATIKA (SANGAT PENTING):
1. Setiap rumus matematika yang berdiri sendiri (blok/centered) WAJIB dibungkus dengan `\[` pada awal dan `\]` pada akhir baris. Pastikan backslash (\) ikut ditulis.
2. Variabel atau rumus di dalam paragraf (inline) WAJIB dibungkus dengan `\(` dan `\)`. Pastikan backslash (\) ikut ditulis.
3. DILARANG KERAS MENGGUNAKAN $$ ATAU $ SEBAGAI PEMBUNGKUS RUMUS MATH.

CONTOH BENAR (Perhatikan penggunaan backslash):
Untuk menghitung luas lingkaran, gunakan rumus berikut:
\[
A = \pi r^2
\]
di mana \(r\) adalah jari-jari lingkaran. Diberikan integral \(\int_0^1 x^2 \, dx = \frac{1}{3}\).

CONTOH SALAH (DILARANG KERAS):
$$ A = \pi r^2 $$
[ A = \pi r^2 ]
$r$ atau (r) adalah jari-jari lingkaran.
Luas lingkaran adalah A = \pi r^2.
\int_0^1 x^2 dx = 1/3',
        ]);

        $keyManager = new AiKeyManager();
        $apiKey = $keyManager->getCurrentKey();
        $apiBase = rtrim(config('ai.api_base', 'https://api.groq.com/openai/v1'), '/');
        $model = config('ai.model', 'openai/gpt-oss-120b');
        $timeout = (int) config('ai.timeout', 120);

        if (!$apiKey) {
            return response()->json(['error' => 'AI API key missing'], 500);
        }

        $resp = new StreamedResponse(function () use ($messages, $sid, $r) {
            $ai = new AiChat();
            $assistant = '';

            $ai->stream($messages, function ($token) use (&$assistant) {
                $assistant .= $token;
                echo "event: token\n";
                echo 'data: ' . json_encode(['token' => $token], JSON_UNESCAPED_UNICODE) . "\n\n";
                @ob_flush();
                @flush();
            });

            // Simpan assistant message terakhir ke session
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
        });

        $resp->headers->set('Content-Type', 'text/event-stream');
        $resp->headers->set('Cache-Control', 'no-cache, no-transform');
        $resp->headers->set('X-Accel-Buffering', 'no');
        return $resp;
    }
}
