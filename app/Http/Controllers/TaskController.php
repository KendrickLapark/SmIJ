<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class TaskController extends Controller
{
    public function tasksByUser(Request $request, $userId)
    {
        $tasks = Task::with('project')
            ->where('user_id', $userId)
            ->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    public function generateReport(Request $request)
    {
        $filters = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'user_id' => 'nullable|exists:users,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Task::query()->with('project', 'user');

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['date_from'])) {
            $query->where('start_time', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('end_time', '<=', $filters['date_to']);
        }

        $tasks = $query->get();

        // Calcular tiempo total por proyecto
        $grouped = $tasks->groupBy('project.name')->map(function($tasks) {
            $totalDuration = $tasks->sum(function($task) {
                return $task->end_time->diffInMinutes($task->start_time);
            });
            return [
                'tasks' => $tasks,
                'total_duration' => $totalDuration,
            ];
        });

        $pdf = PDF::loadView('tasks.report', compact('grouped'));

        return $pdf->download('reporte_tareas.pdf');
    }
}
