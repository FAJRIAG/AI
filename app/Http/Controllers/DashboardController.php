<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Project, ChatSession};

class DashboardController extends Controller
{
    // public function __construct(){ $this->middleware('auth'); }  // JANGAN dipakai di Laravel 12

    public function index(Request $r)
    {
        $projects = Project::where('user_id', $r->user()->id)->latest()->get();

        $currentSession = null;
        $sid = $r->query('session');
        $pid = $r->query('project'); // New: targeted project filtering

        // 1. Resolve Session if provided
        if ($sid !== null && $sid !== '') {
            try {
                $currentSession = ChatSession::with('messages', 'project')->findOrFail($sid);
                abort_unless($currentSession->project->user_id === $r->user()->id, 403);
                $pid = $currentSession->project_id; // Sync project filter with session
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return redirect()->route('vip.home');
            }
        }

        // 2. Resolve Active Project
        if (!$pid && $projects->isNotEmpty()) {
            $pid = $projects->first()->id;
        }

        $activeProject = $projects->find($pid);

        // 3. Auto-create default if nothing exists
        if (!$activeProject) {
            $activeProject = Project::create([
                'user_id' => $r->user()->id,
                'name' => 'My Project',
                'description' => 'Ini adalah workspace default JriGPT kamu.'
            ]);
            $projects->prepend($activeProject);
            $pid = $activeProject->id;
        }

        // 4. Resolve the Session if not provided
        if (!$currentSession) {
            $latestInProject = $activeProject->sessions()->latest()->first();
            if ($latestInProject) {
                return redirect()->route('vip.home', ['session' => $latestInProject->id, 'project' => $pid]);
            } else {
                $session = $activeProject->sessions()->create(['title' => 'New Chat']);
                return redirect()->route('vip.home', ['session' => $session->id, 'project' => $pid]);
            }
        }

        // 5. Build Sidebar Session List (Filtered by Project)
        $sessions = $activeProject->sessions()->latest()->get()->map(fn($s) => [
            'sid' => $s->id,
            'title' => $s->title ?? 'Untitled',
        ])->all();

        return view('vip.dashboard', [
            'projects' => $projects,
            'activeProject' => $activeProject,
            'currentSession' => $currentSession,
            'sessions' => $sessions,
            'sid' => $currentSession->id,
            'currentMode' => $currentSession->mode ?? 'default',
        ]);
    }

    protected function mapSessionsFromProjects($projects)
    {
        return $projects->flatMap(function ($p) {
            return $p->sessions->map(fn($s) => [
                'sid' => $s->id, // gunakan kolom `sid` jika kamu punya
                'title' => $s->title ?? 'Untitled',
            ]);
        })->values()->all();
    }

    // === NEW: tombol "New chat" untuk VIP ===
    public function quickSession(Request $r)
    {
        // Ambil project terbaru milik user, atau bikin default
        $project = Project::where('user_id', $r->user()->id)->latest()->first();
        if (!$project) {
            $project = Project::create([
                'user_id' => $r->user()->id,
                'name' => 'My Project',
            ]);
        }

        // Buat session/chat baru
        $session = $project->sessions()->create([
            'title' => 'New Chat',
        ]);

        // Kembali ke VIP home, dengan session terpilih
        return redirect()->route('vip.home', ['session' => $session->id]);
    }
}
