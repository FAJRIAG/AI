<?php

namespace App\Http\Controllers;

use App\Models\{Project, ChatSession};
use Illuminate\Http\Request;

class ChatSessionController extends Controller
{
    public function store(Request $r, Project $project){
        abort_unless($project->user_id === $r->user()->id, 403);
        $s = $project->sessions()->create([
            'title' => $r->input('title','New Chat'),
        ]);
        return redirect()->route('vip.home', ['session'=>$s->id]);
    }

    public function update(Request $r, ChatSession $session){
        abort_unless($session->project->user_id === $r->user()->id, 403);
        $session->update($r->validate(['title'=>'required|string|max:140']));
        return back();
    }

    public function destroy(Request $r, ChatSession $session){
        abort_unless($session->project->user_id === $r->user()->id, 403);

        // (opsional, kalau ada FK) hapus messages dulu:
        if (method_exists($session, 'messages')) {
            $session->messages()->delete();
        }

        $session->delete();

        // PENTING: kembali ke VIP home TANPA query ?session=...
        return redirect()->route('vip.home');
    }

    public function fetch(Request $r, ChatSession $session){
        abort_unless($session->project->user_id === $r->user()->id, 403);
        return response()->json($session->messages()->latest()->take(50)->get());
    }
}
