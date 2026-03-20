<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class BrowserService
{
    /**
     * Fetch the content of a URL and convert it to clean Markdown.
     */
    public static function browse(string $url): string
    {
        try {
            // Gunakan User-Agent yang umum agar tidak diblokir
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ])->timeout(30)->get($url);

            if (!$response->successful()) {
                return "Gagal mengakses URL: Server merespons dengan status {$response->status()}.";
            }

            $html = $response->body();
            return self::cleanHtmlToMarkdown($html, $url);

        } catch (\Exception $e) {
            Log::error("BrowserService Error ($url): " . $e->getMessage());
            return "Gagal mengakses URL: Terjadi kesalahan koneksi atau timeout.";
        }
    }

    /**
     * Sangat sederhana: Ekstrak teks utama dari HTML.
     * Untuk hasil "Premium", idealnya pakai library seperti Readability.php.
     */
    private static function cleanHtmlToMarkdown(string $html, string $baseUrl): string
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        // Handle encoding agar karakter khusus tidak rusak
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // Hapus elemen yang mengganggu AI (script, style, nav, footer, ads)
        $junkSelectors = [
            '//script', '//style', '//nav', '//footer', '//header', 
            '//aside', '//iframe', '//noscript', '//svg',
            '//div[contains(@class, "ad")]', '//div[contains(@class, "banner")]'
        ];
        foreach ($junkSelectors as $selector) {
            foreach ($xpath->query($selector) as $node) {
                $node->parentNode->removeChild($node);
            }
        }

        // Ambil elemen teks utama (h1, h2, h3, p, li)
        $text = "";
        $nodes = $xpath->query('//h1 | //h2 | //h3 | //h4 | //p | //li | //table');
        
        foreach ($nodes as $node) {
            $tag = strtolower($node->nodeName);
            $content = trim($node->textContent);
            
            if (empty($content)) continue;

            if (str_starts_with($tag, 'h')) {
                $level = str_repeat('#', (int)substr($tag, 1));
                $text .= "\n$level $content\n";
            } elseif ($tag === 'p') {
                $text .= "\n$content\n";
            } elseif ($tag === 'li') {
                $text .= "- $content\n";
            } elseif ($tag === 'table') {
                $text .= "\n[Tabel Terdeteksi - Teks Mentah]: " . trim($node->textContent) . "\n";
            }
        }

        // Batasi panjang konten agar tidak melebihi limit token AI (sekitar 12000 karakter)
        $content = trim($text);
        if (strlen($content) > 15000) {
            $content = mb_substr($content, 0, 15000) . "\n\n...(Konten dipotong karena terlalu panjang)...";
        }

        if (empty($content)) {
            return "Halaman ini tampaknya tidak memiliki teks utama yang bisa dibaca atau dilindungi bot.";
        }

        return "--- ISI HALAMAN DARI $baseUrl ---\n\n" . $content . "\n\n--- AKHIR HALAMAN ---";
    }
}
