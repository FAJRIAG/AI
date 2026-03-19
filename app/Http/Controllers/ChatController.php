<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\AiKeyManager;
use App\Services\AiChat;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    // Jangan pakai __construct middleware di Laravel 12 (sudah dikunci via group route)

    public function stream(Request $r, ChatSession $session)
    {
        // Pastikan milik user login
        abort_unless($session->project && $session->project->user_id === $r->user()->id, 403);

        $data = $r->validate([
            'content' => 'required|string',
            'attachment_url' => 'nullable|string'
        ]);

        \Log::info("Stream Request Data:", $data);

        // Simpan user message
        $userMsgData = ['role' => 'user', 'content' => $data['content']];
        if (!empty($data['attachment_url'])) {
            $userMsgData['attachment_url'] = $data['attachment_url'];
        }
        $session->messages()->create($userMsgData);

        // Ambil 15 terakhir (by id desc), lalu urutkan naik agar kronologis
        // Dibatasi ke 15 agar payload tidak terlalu besar (mencegah Error 413)
        $hist = $session->messages()
            ->orderBy('id', 'desc')
            ->take(15)
            ->get()
            ->sortBy('id')
            ->values();

        $messages = $hist->map(function ($m) {
            if ($m->attachment_url && \Storage::disk('public')->exists($m->attachment_url)) {
                $path = \Storage::disk('public')->path($m->attachment_url);
                $mime = mime_content_type($path);
                
                // JIKA GAMBAR -> Gunakan struktur Vision (Multi-modal)
                if (str_starts_with($mime, 'image/')) {
                    $b64 = base64_encode(file_get_contents($path));
                    return [
                        'role' => $m->role,
                        'content' => [
                            ['type' => 'text', 'text' => mb_strimwidth($m->content, 0, 4000, "...")] ,
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
                    \Log::error("Gagal ekstrak dokumen: " . $e->getMessage());
                }

                if (!empty($extractedText)) {
                    $docContext = "\n\n--- ISI DOKUMEN (" . basename($m->attachment_url) . ") ---\n" . mb_strimwidth($extractedText, 0, 30000) . "\n--- AKHIR DOKUMEN ---\n";
                    return [
                        'role' => $m->role,
                        'content' => $m->content . $docContext
                    ];
                }
            }
            
            return [
                'role' => $m->role,
                'content' => mb_strimwidth($m->content, 0, 4000, "..."),
            ];
        })->all();

        // Ambil Memori Jangka Panjang (Fase 4)
        $memoryService = new \App\Services\MemoryService();
        $userMemories = $memoryService->getMemories($r->user()->id);
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

        // Konfigurasi AI
        $keyManager = new AiKeyManager();
        $apiKey = $keyManager->getCurrentKey();
        $apiBase = rtrim(config('ai.api_base', 'https://api.groq.com/openai/v1'), '/');
        $model = config('ai.model', 'openai/gpt-oss-120b');
        $timeout = (int) config('ai.timeout', 120);

        if (!$apiKey) {
            return response()->json(['error' => 'AI API key missing'], 500);
        }

        $resp = new StreamedResponse(function () use ($messages, $session) {
            $ai = new AiChat();
            $assistant = '';

            $ai->stream($messages, function ($token) use (&$assistant, $session) {
                $assistant .= $token;
                echo "event: token\n";
                echo 'data: ' . json_encode(['token' => $token], JSON_UNESCAPED_UNICODE) . "\n\n";
                @ob_flush();
                @flush();
            });

            // Ekstraksi Memori Baru secara asinkron (Fase 4)
            // Menggunakan dispatch agar tidak menghambat response 'done'
            $userMsg = $messages[count($messages) - 1]['content']; // Pesan terakhir user
            if (is_array($userMsg)) {
                // Handle vision format
                $userMsg = $userMsg[0]['text'] ?? '';
            }
            
            $finalAssistant = $assistant;
            $userId = auth()->id();
            
            dispatch(function() use ($userId, $userMsg, $finalAssistant, $session) {
                \Log::info("VIP: Dispatch afterResponse started for Session ID: " . $session->id);
                (new \App\Services\MemoryService())->extractAndStore($userId, null, $userMsg, $finalAssistant);
            })->afterResponse();

            // Simpan ke database setelah streaming selesai
            if (trim($assistant) !== '') {
                $session->messages()->create(['role' => 'assistant', 'content' => $assistant]);
            }

            // SMART TITLE (Otomatis ganti New Chat - Kirim via SSE agar UI update tanpa refresh)
            $newTitle = (new \App\Services\ChatTitleService())->generateForVip($session);
            if ($newTitle) {
                echo "event: rename\n";
                echo 'data: ' . json_encode(['title' => $newTitle, 'id' => $session->id], JSON_UNESCAPED_UNICODE) . "\n\n";
                @ob_flush(); @flush();
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
        
        \Log::info("File Uploaded: $path");

        return response()->json([
            'url' => \Storage::url($path),
            'attachment_url' => $path
        ]);
    }
}
