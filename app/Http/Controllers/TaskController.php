<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function tasksByUser(User $user)
    {
        $tasks = Task::where('user_id', $user->id)->get()->map(function($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->start_datetime->toIso8601String(),
                'end' => $task->end_datetime->toIso8601String(),
                'project_id' => $task->project_id,
                'description' => $task->description,
            ];
        });
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'description' => 'nullable|string',
        ]);
        
        $task = Task::create([
            'project_id' => $validated['project_id'],
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'start_datetime' => $validated['start_datetime'],
            'end_datetime' => $validated['end_datetime'],
            'description' => $validated['description'] ?? '',
        ]);        

        return response()->json(['message' => 'Tarea creada', 'task' => $task]);
    }

}
