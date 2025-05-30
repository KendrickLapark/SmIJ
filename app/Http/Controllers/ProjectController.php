<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('creator')->orderBy('last_used_at', 'desc')->get();
        $users = User::all();
        return view('projects.index', compact('projects', 'users'));
    }

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function list()
    {
        $projects = Project::with('creator')
                ->join('users', 'projects.created_by_user_id', '=', 'users.id')
                ->orderBy('projects.created_at', 'asc')
                ->orderBy('users.name')
                ->select('projects.*')
                ->get();

        $projects = $projects->map(function($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'creator' => [
                    'name' => $project->creator->name ?? 'Desconocido',
                ],
                'created_at' => $project->created_at->toIso8601String(),
                'last_used_at' => $project->last_used_at ? $project->last_used_at->toIso8601String() : null,
            ];
        });
    
        return response()->json($projects);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $data['created_by_user_id'] = Auth::id(); 
        $project = Project::create($data);
    
        return response()->json(['message' => 'Proyecto creado correctamente', 'project' => $project]);
    }

}
