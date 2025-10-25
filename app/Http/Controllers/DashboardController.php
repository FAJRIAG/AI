<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Project, ChatSession};

class DashboardController extends Controller
{
    // public function __construct(){ $this->middleware('auth'); }  // JANGAN dipakai di Laravel 12

    public function index(Request $r)
    {
        $projects = Project::with(['sessions:id,project_id,title,created_at'])
            ->where('user_id', $r->user()->id)
            ->latest()->get();

        $currentSession = null;
        $sid = $r->query('session'); // bisa "0" atau string kosong, jadi cek eksplisit
        if ($sid !== null && $sid !== '') {
            $currentSession = ChatSession::with('messages','project')->findOrFail($sid);
            abort_unless($currentSession->project->user_id === $r->user()->id, 403);
        }

        return view('vip.dashboard', [
            'projects'       => $projects,
            'currentSession' => $currentSession,
            'sessions'       => $this->mapSessionsFromProjects($projects), // biar sidebar punya flat list
            'sid'            => $currentSession?->id,
        ]);
    }

    protected function mapSessionsFromProjects($projects)
    {
        return $projects->flatMap(function ($p) {
            return $p->sessions->map(fn ($s) => [
                'sid'   => $s->id, // gunakan kolom `sid` jika kamu punya
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
                'name'    => 'My Project',
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
