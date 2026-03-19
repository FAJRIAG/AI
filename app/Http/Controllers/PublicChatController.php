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
        $payload = $r->validate([
            'content' => 'required|string',
            'attachment_url' => 'nullable|string'
        ]);
        $sessions = $this->getSessions($r);
        abort_unless(isset($sessions[$sid]), 404);

        // simpan user message
        $userMsgData = ['role' => 'user', 'content' => $payload['content']];
        if (!empty($payload['attachment_url'])) {
            $userMsgData['attachment_url'] = $payload['attachment_url'];
        }
        $sessions[$sid]['history'][] = $userMsgData;
        $this->saveSessions($r, $sessions);

        // siapkan messages (max 15 agar payload tidak 413)
        $hist = array_slice($sessions[$sid]['history'], -15);
        $messages = array_map(function ($m) {
            if (!empty($m['attachment_url']) && \Storage::disk('public')->exists($m['attachment_url'])) {
                $path = \Storage::disk('public')->path($m['attachment_url']);
                $mime = mime_content_type($path);
                
                // JIKA GAMBAR -> Gunakan struktur Vision (Multi-modal)
                if (str_starts_with($mime, 'image/')) {
                    $b64 = base64_encode(file_get_contents($path));
                    return [
                        'role' => $m['role'],
                        'content' => [
                            ['type' => 'text', 'text' => mb_strimwidth($m['content'], 0, 4000, "...")] ,
                            ['type' => 'image_url', 'image_url' => ['url' => "data:$mime;base64,$b64"]]
                        ]
                    ];
                }

                // JIKA DOKUMEN (PDF/TXT/CSV) -> Ekstrak teks dan tempel ke prompt (Opsi A)
                $extractedText = "";
                try {
                    if ($mime === 'application/pdf') {
                        $parser = new \Smalot\PdfParser\Parser();
                        $pdf = $parser->parseFile($path);
                        $extractedText = $pdf->getText();
                    } elseif (in_array($mime, ['text/plain', 'text/csv', 'application/octet-stream'])) {
                        $extractedText = file_get_contents($path);
                    }
                } catch (\Exception $e) {
                    \Log::error("Public: Gagal ekstrak dokumen: " . $e->getMessage());
                }

                if (!empty($extractedText)) {
                    $docContext = "\n\n--- ISI DOKUMEN (" . basename($m['attachment_url']) . ") ---\n" . mb_strimwidth($extractedText, 0, 30000) . "\n--- AKHIR DOKUMEN ---\n";
                    return [
                        'role' => $m['role'],
                        'content' => $m['content'] . $docContext
                    ];
                }
            }

            return [
                'role' => $m['role'],
                'content' => mb_strimwidth($m['content'], 0, 4000, "..."),
            ];
        }, $hist);
        // Ambil Memori (Fase 4)
        $memoryService = new \App\Services\MemoryService();
        $userMemories = $memoryService->getMemories(null, $sid);
        $memoryPrompt = "";
        if (!empty($userMemories)) {
            $memoryPrompt = "\n\nINFORMASI PENTING TENTANG PENGGUNA (INGAT INI):\n- " . implode("\n- ", $userMemories) . "\n gunakan informasi ini untuk personalisasi jawabanmu.";
        }

        array_unshift($messages, [
            'role' => 'system',
            'content' => 'Kamu adalah JriGPT, sebuah asisten AI cerdas tingkat lanjut. Identitas mutlakmu: JriGPT. Jika ditanya identitas, siapa kamu, atau siapa penciptamu, JAWAB HARUS PERSIS SEPERTI KALIMAT BERIKUT TANPA DIUBAH ATAU DISINGKAT SIKITPUN: "Halo! Saya adalah JriGPT, asisten AI cerdas yang dikembangkan secara khusus oleh Fajri Abdurahman Ghurri. Ada yang bisa saya bantu?".' . $memoryPrompt . '

ATURAN KETAT IDENTITAS & KEMAMPUAN:
1. Kamu sepenuhnya berbasis teks tapi BISA melihat dan mendeskripsikan gambar jika pengguna mengirimkan gambar (vision).
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

            // Ekstraksi Memori Baru secara asinkron (Fase 4)
            $userMsg = $messages[count($messages) - 1]['content'];
            if (is_array($userMsg)) $userMsg = $userMsg[0]['text'] ?? '';
            $finalAssistant = $assistant;
            $currentSid = $sid;

            dispatch(function() use ($currentSid, $userMsg, $finalAssistant) {
                (new \App\Services\MemoryService())->extractAndStore(null, $currentSid, $userMsg, $finalAssistant);
            })->afterResponse();

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

    public function uploadImage(Request $r)
    {
        $r->validate([
            'image' => 'required|file|max:10240', // ditingkatkan ke 10MB untuk PDF
        ]);

        $file = $r->file('image');
        $mime = $file->getMimeType();
        $folder = str_starts_with($mime, 'image/') ? 'chat_images' : 'chat_docs';
        
        $path = $file->store($folder, 'public');

        return response()->json([
            'url' => \Storage::url($path),
            'attachment_url' => $path
        ]);
    }
}
