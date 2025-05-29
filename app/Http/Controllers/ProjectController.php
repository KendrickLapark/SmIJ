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
                    ->orderByDesc('last_used_at')
                    ->get();
    
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
