<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class WebSearchEngine
{
    /**
     * Lakukan pencarian web menggunakan DuckDuckGo HTML (Gratis) atau Tavily (Berbayar/Free Tier).
     */
    public static function search(string $query): string
    {
        $provider = env('WEB_SEARCH_PROVIDER', 'duckduckgo');
        
        if ($provider === 'tavily' && env('TAVILY_API_KEY')) {
            return self::searchTavily($query);
        }

        return self::searchDuckDuckGo($query);
    }

    private static function searchDuckDuckGo(string $query): string
    {
        try {
            $url = 'https://html.duckduckgo.com/html/';
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            ])->asForm()->post($url, [
                'q' => $query
            ]);

            if (!$response->successful()) {
                return "Pencarian gagal: Server DuckDuckGo menolak permintaan (Mungkin rate-limited).";
            }

            $html = $response->body();

            // Sembunyikan E_WARNING untuk HTML tidak valid yang wajar
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);
            // DuckDuckGo HTML result items are inside <a class="result__url"> and <a class="result__snippet">
            $resultNodes = $xpath->query('//div[contains(@class, "result__body")]');
            
            $results = [];
            foreach ($resultNodes as $index => $node) {
                if ($index >= 5) break; // Cukup ambil top 5 untuk AI

                $titleNode = $xpath->query('.//h2/a', $node)->item(0);
                $snippetNode = $xpath->query('.//a[contains(@class, "result__snippet")]', $node)->item(0);
                
                if ($titleNode && $snippetNode) {
                    $results[] = [
                        'title' => trim($titleNode->textContent),
                        'snippet' => trim($snippetNode->textContent)
                    ];
                }
            }

            if (empty($results)) {
                return "Tidak ada hasil pencarian web yang ditemukan untuk '{$query}'.";
            }

            $formatted = "Hasil Pencarian Web untuk '{$query}':\n";
            foreach ($results as $i => $r) {
                $idx = $i + 1;
                $formatted .= "{$idx}. Judul: {$r['title']}\n   Ringkasan: {$r['snippet']}\n\n";
            }

            return $formatted;

        } catch (\Exception $e) {
            Log::error("WebSearchEngine DDG Error: " . $e->getMessage());
            return "Pencarian gagal: Terjadi kesalahan teknis saat scraping. ({$e->getMessage()})";
        }
    }

    private static function searchTavily(string $query): string
    {
        try {
            $apiKey = env('TAVILY_API_KEY');
            $response = Http::post('https://api.tavily.com/search', [
                'api_key' => $apiKey,
                'query' => $query,
                'search_depth' => 'basic',
                'max_results' => 5
            ]);

            if (!$response->successful()) {
                return "Pencarian Tavily gagal: " . $response->body();
            }

            $data = $response->json();
            $formatted = "Hasil Pencarian Web (Tavily) untuk '{$query}':\n";
            foreach ($data['results'] ?? [] as $i => $r) {
                $idx = $i + 1;
                $formatted .= "{$idx}. Judul: {$r['title']}\n   URL: {$r['url']}\n   Ringkasan: {$r['content']}\n\n";
            }
            return $formatted;
        } catch (\Exception $e) {
            return "Pencarian Tavily gagal: Terjadi kesalahan koneksi.";
        }
    }
}
