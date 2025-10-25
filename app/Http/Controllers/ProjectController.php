<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function store(Request $r){
        $name = $r->input('name', 'Untitled Project');
        Project::create([
            'name'    => $name,
            'user_id' => $r->user()->id,
        ]);
        return back();
    }

    public function update(Request $r, Project $project){
        abort_unless($project->user_id === $r->user()->id, 403);
        $project->update($r->validate(['name'=>'required|string|max:100']));
        return back();
    }

    public function destroy(Request $r, Project $project){
        abort_unless($project->user_id === $r->user()->id, 403);
        $project->delete();
        return back();
    }
}
