<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VectorSearchService
{
    private string $pineconeKey;
    private string $pineconeUrl;
    private string $openAiKey;

    public function __construct()
    {
        $this->pineconeKey = env('PINECONE_API_KEY');
        $this->pineconeUrl = rtrim(env('PINECONE_INDEX_URL'), '/');
        $this->openAiKey = env('OPENAI_API_KEY');
    }

    /**
     * Generate embedding for a given text using OpenAI.
     */
    public function getEmbedding(string $text): ?array
    {
        try {
            $response = Http::withToken($this->openAiKey)
                ->post('https://api.openai.com/v1/embeddings', [
                    'model' => 'text-embedding-3-small',
                    'input' => $text,
                ]);

            if (!$response->successful()) {
                Log::error("OpenAI Embedding Error: " . $response->body());
                return null;
            }

            return $response->json('data.0.embedding');
        } catch (\Exception $e) {
            Log::error("OpenAI Embedding Exception: " . $e->getMessage());
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
