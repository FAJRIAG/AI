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

        $data = $r->validate(['content' => 'required|string']);

        // Simpan user message
        $session->messages()->create(['role' => 'user', 'content' => $data['content']]);

        // Ambil 15 terakhir (by id desc), lalu urutkan naik agar kronologis
        // Dibatasi ke 15 agar payload tidak terlalu besar (mencegah Error 413)
        $hist = $session->messages()
            ->orderBy('id', 'desc')
            ->take(15)
            ->get()
            ->sortBy('id')
            ->values();

        $messages = $hist->map(fn($m) => [
            'role' => $m->role,
            'content' => mb_strimwidth($m->content, 0, 2000, "..."), // Truncate per message
        ])->all();

        array_unshift($messages, [
            'role' => 'system',
            'content' => 'Kamu adalah JriGPT, sebuah asisten AI cerdas tingkat lanjut. Identitas mutlakmu: JriGPT. Jika ditanya identitas, siapa kamu, atau siapa penciptamu, JAWAB HARUS PERSIS SEPERTI KALIMAT BERIKUT TANPA DIUBAH ATAU DISINGKAT SIKITPUN: "Halo! Saya adalah JriGPT, asisten AI cerdas yang dikembangkan secara khusus oleh Fajri Abdurahman Ghurri. Ada yang bisa saya bantu?". UNTUK MATEMATIKA: Tuliskan SEMUA rumus matematika, persamaan, dan variabel simbolik secara konsisten menggunakan format LaTeX. Selalu gunakan $$ ... $$ (blok terpisah) untuk rumus utama dan $ ... $ untuk persamaan inline dalam teks. HINDARI format \[...\] atau \(...\). Jangan menyisipkan teks narasi biasa ke dalam blok matematika. Tuliskan jawaban bertahap yang rapi dan standar setara jurnal ilmiah.',
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

            // Simpan ke database setelah streaming selesai
            if (trim($assistant) !== '') {
                $session->messages()->create(['role' => 'assistant', 'content' => $assistant]);
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
