<?php

namespace App\Services;

use App\Models\Project;
use App\Utilities\DocumentChunker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProjectKnowledgeService
{
    protected VectorSearchService $vectorSearch;

    public function __construct(VectorSearchService $vectorSearch)
    {
        $this->vectorSearch = $vectorSearch;
    }

    /**
     * Index all documents and important sessions for a project.
     */
    public function indexProject(Project $project): bool
    {
        try {
            // 1. Clear old vectors for this project
            $this->vectorSearch->deleteByProject($project->id);

            // 2. Find all documents in project sessions
            $messagesWithDocs = $project->sessions()
                ->with('messages')
                ->get()
                ->flatMap(fn($s) => $s->messages)
                ->whereNotNull('attachment_url');

            foreach ($messagesWithDocs as $m) {
                $this->indexMessageDocument($project, $m->attachment_url);
            }

            // 3. (Optional) Index chat history text itself
            // For now, let's focus on documents as they are the "Deep Research" target.

            $project->update(['last_indexed_at' => now()]);
            return true;
        } catch (\Throwable $e) {
            Log::error("IndexProject Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Index a specific document/file.
     */
    public function indexMessageDocument(Project $project, string $attachmentUrl): void
    {
        if (!Storage::disk('public')->exists($attachmentUrl)) return;

        $path = Storage::disk('public')->path($attachmentUrl);
        $mime = mime_content_type($path);
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
            Log::error("Document Extraction Error ($attachmentUrl): " . $e->getMessage());
        }

        if (empty($extractedText)) return;

        $chunks = DocumentChunker::chunk($extractedText, 1000, 200);
        foreach ($chunks as $i => $chunk) {
            $embedding = $this->vectorSearch->getEmbedding($chunk);
            if ($embedding) {
                $vectorId = "p{$project->id}_" . md5($attachmentUrl) . "_$i";
                $this->vectorSearch->upsert($vectorId, $embedding, [
                    'project_id' => $project->id,
                    'file_name' => basename($attachmentUrl),
                    'text' => $chunk, // Pinecone stores metadata, keep chunk text here for retrieval
                ]);
            }
        }
    }

    /**
     * Perform Semantic Search for RAG.
     */
    public function search(Project $project, string $query, int $topK = 5): string
    {
        $embedding = $this->vectorSearch->getEmbedding($query);
        if (!$embedding) return "Gagal melakukan pencarian (Embedding error).";

        $matches = $this->vectorSearch->query($embedding, $topK, [
            'project_id' => ['$eq' => $project->id]
        ]);

        if (empty($matches)) {
            return "Tidak ditemukan informasi relevan di perpustakaan workspace.";
        }

        $formatted = "Ditemukan beberapa referensi relevan di Workspace Memory:\n\n";
        foreach ($matches as $i => $m) {
            $idx = $i + 1;
            $score = round($m['score'] * 100, 1);
            $file = $m['metadata']['file_name'] ?? 'Unknown Source';
            $text = $m['metadata']['text'] ?? '';
            
            $formatted .= "[$idx] Sumber: $file (Similiarity: $score%)\n";
            $formatted .= "Kutipan: $text\n\n";
        }

        return $formatted;
    }
}
