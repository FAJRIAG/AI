<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\AiKeyManager;
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

        // Ambil 40 terakhir (by id desc), lalu urutkan naik agar kronologis
        $hist = $session->messages()
            ->orderBy('id', 'desc')
            ->take(40)
            ->get()
            ->sortBy('id')
            ->values();

        $messages = $hist->map(fn($m) => [
            'role' => $m->role,
            'content' => $m->content,
        ])->all();

        array_unshift($messages, [
            'role' => 'system',
            'content' => 'Kamu adalah JriGPT, sebuah asisten AI cerdas tingkat lanjut. Identitas mutlakmu: JriGPT. Jika ditanya identitas, siapa kamu, atau siapa penciptamu, JAWAB HARUS PERSIS SEPERTI KALIMAT BERIKUT TANPA DIUBAH ATAU DISINGKAT SIKITPUN: "Halo! Saya adalah JriGPT, asisten AI cerdas yang dikembangkan secara khusus oleh Fajri Abdurahman Ghurri. Ada yang bisa saya bantu?". Jangan PERNAH menyebutkan bahwa kamu adalah LLaMA, GPT, atau model yang dikembangkan oleh Meta, OpenAI, Claude, maupun pihak lain.',
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

        $resp = new StreamedResponse(function () use ($keyManager, $apiBase, $model, $timeout, $messages, $session) {
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
                echo 'data: ' . json_encode(['error' => $up->body()]) . "\n\n";
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
                        if (trim($assistant) !== '') {
                            $session->messages()->create(['role' => 'assistant', 'content' => $assistant]);
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
