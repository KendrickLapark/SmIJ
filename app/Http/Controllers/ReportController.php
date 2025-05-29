<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function generatePdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        $tasks = Task::where('project_id', $request->project_id)
            ->whereBetween('start_datetime', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
            ->with('user') // para el nombre del usuario
            ->get();

        $totalMinutes = 0;
        $tasks->transform(function ($task) use (&$totalMinutes) {
            $start = $task->start_datetime;
            $end = $task->end_datetime ?? $task->start_datetime;
            $minutes = (strtotime($end) - strtotime($start)) / 60;
            $task->duration_minutes = $minutes;
            $totalMinutes += $minutes;
            return $task;
        });

        $user = User::find($request->user_id);
        $project = Project::find($request->project_id);

        $pdf = PDF::loadView('reports.tasks_pdf', compact('tasks', 'totalMinutes', 'user', 'project', 'request'));

        return $pdf->stream('Reporte_de_tareas.pdf');
    }
}
