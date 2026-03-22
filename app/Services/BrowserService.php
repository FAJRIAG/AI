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
            // Gunakan Jina Reader (https://r.jina.ai/) untuk hasil Markdown yang "Premium"
            // Jina Reader menangani JavaScript rendering dan bypass banyak bot protection.
            $readerUrl = 'https://r.jina.ai/' . $url;
            
            $response = Http::withHeaders([
                'X-Return-Format' => 'markdown',
            ])->timeout(30)->get($readerUrl);

            if ($response->successful()) {
                $content = $response->body();
                
                // Cek apakah ada indikasi bot block (999 LinkedIn, dsb)
                if (str_contains($content, 'error 999') || str_contains($content, 'CAPTCHA') || str_contains($content, 'Access Denied')) {
                    return "--- INFO DARI $url ---\n\nMaaf, situs ini ($url) memproteksi akses otomatis. Saya tidak bisa membaca detail isinya secara langsung. Mohon andalkan ringkasan dari hasil pencarian umum saja.";
                }
                // Potong jika terlalu panjang
                if (strlen($content) > 15000) {
                    $content = mb_substr($content, 0, 15000) . "\n\n...(Konten dipotong agar tidak kepenuhan)...";
                }
                return "--- ISI HALAMAN DARI $url ---\n\n" . $content . "\n\n--- AKHIR HALAMAN ---";
            }

            // Fallback: Jika Jina gagal, coba akses langsung (Simple Scraping)
            $directResponse = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            ])->timeout(20)->get($url);

            if ($directResponse->successful()) {
                return self::cleanHtmlToMarkdown($directResponse->body(), $url);
            }

            return "Gagal mengakses URL: Server merespons dengan status {$response->status()}.";

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

    /**
     * Extract all links from a URL.
     */
    public static function getLinks(string $url): string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            ])->timeout(20)->get($url);

            if (!$response->successful()) {
                return "Gagal mengambil link: Server merespons dengan status {$response->status()}.";
            }

            $html = $response->body();
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);
            $links = [];
            $nodes = $xpath->query('//a[@href]');

            foreach ($nodes as $node) {
                $href = $node->getAttribute('href');
                $text = trim($node->textContent);

                if (empty($href) || str_starts_with($href, '#') || str_starts_with($href, 'javascript:')) {
                    continue;
                }

                // Resolve relative URL
                if (!str_starts_with($href, 'http')) {
                    $parsed = parse_url($url);
                    $base = $parsed['scheme'] . '://' . $parsed['host'];
                    if (str_starts_with($href, '/')) {
                        $href = $base . $href;
                    } else {
                        $href = $base . '/' . $href;
                    }
                }

                $links[] = "- [$text]($href)";
            }

            // Deduplicate and limit
            $links = array_unique($links);
            $links = array_slice($links, 0, 50); // Limit to 50 links

            if (empty($links)) {
                return "Tidak ada link yang ditemukan di halaman ini.";
            }

            return "--- DAFTAR LINK DI $url ---\n\n" . implode("\n", $links) . "\n\n--- AKHIR DAFTAR ---";

        } catch (\Exception $e) {
            Log::error("BrowserService getLinks Error ($url): " . $e->getMessage());
            return "Gagal mengambil link: Terjadi kesalahan koneksi atau timeout.";
        }
    }
}
