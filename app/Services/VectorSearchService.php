<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VectorSearchService
{
    private ?string $pineconeKey;
    private ?string $pineconeUrl;
    private ?string $apiKey;
    private ?string $apiUrl;
    private ?string $model;

    public function __construct()
    {
        $this->pineconeKey = env('PINECONE_API_KEY');
        $this->pineconeUrl = env('PINECONE_INDEX_URL') ? rtrim(env('PINECONE_INDEX_URL'), '/') : null;
        
        $provider = env('AI_PROVIDER', 'openai');
        
        if ($provider === 'jrigpt') {
            $this->apiKey = env('AI_API_KEY_1');
            $rawUrl = env('AI_URL_1', 'https://api.openai.com/v1/chat/completions');
            // Extract base URL from JriGPT endpoint (e.g., https://jrigpt.proxyman.com?model=...)
            $parsedUrl = parse_url($rawUrl);
            $baseUrl = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? 'api.openai.com');
            // Proxies usually have a /v1/embeddings endpoint
            $this->apiUrl = $baseUrl . '/v1/embeddings';
            $this->model = 'text-embedding-3-small'; // Standard for many proxies
        } else {
            $this->apiKey = env('OPENAI_API_KEY');
            $this->apiUrl = 'https://api.openai.com/v1/embeddings';
            $this->model = 'text-embedding-3-small';
        }
    }

    /**
     * Generate embedding for a given text.
     */
    public function getEmbedding(string $text)
    {
        if (!$this->apiKey) {
            Log::warning("VectorSearchService: API KEY is missing in .env. Skipping embedding generation.");
            return null;
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'input' => $text,
                ]);

            if (!$response->successful()) {
                $error = $response->json('error.message') ?? $response->body();
                if (strpos($error, 'insufficient_quota') !== false) {
                    Log::warning("Embedding Quota Exceeded: " . $error);
                    return "ERROR: API Quota Exceeded. Mohon cek billing Anda.";
                }

                if (strpos($error, 'Incorrect API key') !== false || strpos($error, 'invalid_api_key') !== false) {
                    Log::warning("Embedding Key Error: " . $error);
                    return "ERROR: API Key Tidak Valid untuk Embeddings. Pastikan provider Anda mendukung endpoint /v1/embeddings.";
                }

                Log::error("Embedding Error: " . $response->body());
                return null;
            }

            return $response->json('data.0.embedding');
        } catch (\Exception $e) {
            Log::error("Embedding Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Upsert vectors to Pinecone.
     */
    public function upsert(string $id, array $vector, array $metadata = []): bool
    {
        try {
            $response = Http::withHeaders([
                'Api-Key' => $this->pineconeKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->pineconeUrl}/vectors/upsert", [
                'vectors' => [
                    [
                        'id' => $id,
                        'values' => $vector,
                        'metadata' => $metadata
                    ]
                ]
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Pinecone Upsert Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Query Pinecone for similar vectors.
     */
    public function query(array $vector, int $topK = 5, array $filter = []): array
    {
        try {
            $payload = [
                'vector' => $vector,
                'topK' => $topK,
                'includeMetadata' => true,
            ];

            if (!empty($filter)) {
                $payload['filter'] = $filter;
            }

            $response = Http::withHeaders([
                'Api-Key' => $this->pineconeKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->pineconeUrl}/query", $payload);

            if (!$response->successful()) {
                Log::error("Pinecone Query Error: " . $response->body());
                return [];
            }

            return $response->json('matches') ?? [];
        } catch (\Exception $e) {
            Log::error("Pinecone Query Exception: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete vectors from Pinecone by filter (e.g., project_id).
     */
    public function deleteByProject(int $projectId): bool
    {
        try {
            $response = Http::withHeaders([
                'Api-Key' => $this->pineconeKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->pineconeUrl}/vectors/delete", [
                'filter' => ['project_id' => ['$eq' => $projectId]]
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Pinecone Delete Exception: " . $e->getMessage());
            return false;
        }
    }
}
