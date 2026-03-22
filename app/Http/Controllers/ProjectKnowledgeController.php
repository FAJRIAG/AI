<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ProjectKnowledgeService;
use Illuminate\Http\Request;

class ProjectKnowledgeController extends Controller
{
    protected ProjectKnowledgeService $knowledgeService;

    public function __construct(ProjectKnowledgeService $knowledgeService)
    {
        $this->knowledgeService = $knowledgeService;
    }

    /**
     * Trigger knowledge indexing for a project.
     */
    public function sync(Request $request, Project $project)
    {
        // Ensure user owns the project
        abort_unless($project->user_id === $request->user()->id, 403);

        $success = $this->knowledgeService->indexProject($project);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Memori project berhasil disinkronisasi ke Pinecone.',
                'last_indexed_at' => $project->last_indexed_at->diffForHumans()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal sinkronisasi. Pastikan API Key Pinecone & OpenAI sudah benar.'
        ], 500);
    }
}
