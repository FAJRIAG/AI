<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

        $messages = $hist->map(fn ($m) => [
            'role'    => $m->role,
            'content' => $m->content,
        ])->all();

        array_unshift($messages, [
            'role'    => 'system',
            'content' => 'You are a helpful Indonesian AI assistant.',
        ]);

        // Konfigurasi AI
        $apiKey  = env('AI_API_KEY');
        $apiBase = rtrim(env('AI_API_BASE', 'https://api.openai.com/v1'), '/');
        $model   = env('AI_MODEL', 'gpt-4o-mini');
        $timeout = (int) env('AI_TIMEOUT', 120);

        if (!$apiKey) {
            return response()->json(['error' => 'AI API key missing'], 500);
        }

        $resp = new StreamedResponse(function () use ($apiKey, $apiBase, $model, $timeout, $messages, $session) {
            $up = Http::withToken($apiKey)->timeout($timeout)
                ->withHeaders(['Accept' => 'application/json'])
                ->withOptions(['buffer' => false])
                ->post($apiBase . '/chat/completions', [
                    'model'    => $model,
                    'messages' => $messages,
                    'stream'   => true,
                ]);

            if ($up->failed()) {
                echo "event: error\n";
                echo 'data: ' . json_encode(['error' => $up->body()]) . "\n\n";
                @ob_flush(); @flush(); return;
            }

            $assistant = '';
            $body = $up->toPsrResponse()->getBody();

            while (!$body->eof()) {
                $chunk = $body->read(8192);
                if (!$chunk) { usleep(10000); continue; }

                foreach (preg_split("/\r\n|\n|\r/", $chunk) as $line) {
                    $line = trim($line);
                    if ($line === '' || !str_starts_with($line, 'data:')) continue;

                    $payload = trim(substr($line, 5));
                    if ($payload === '[DONE]') {
                        if (trim($assistant) !== '') {
                            $session->messages()->create(['role' => 'assistant', 'content' => $assistant]);
                        }
                        echo "event: done\n";
                        echo "data: {}\n\n";
                        @ob_flush(); @flush(); return;
                    }

                    $json  = json_decode($payload, true);
                    $delta = $json['choices'][0]['delta']['content'] ?? '';
                    if ($delta !== '') {
                        $assistant .= $delta;
                        echo "event: token\n";
                        echo 'data: ' . json_encode(['token' => $delta], JSON_UNESCAPED_UNICODE) . "\n\n";
                        @ob_flush(); @flush();
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
