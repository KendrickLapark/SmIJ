<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de tareas</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
        }
        th, td {
            border: 1px solid #444;
            padding: 8px;
            font-size: 12px;
        }
        th {
            background-color: #f0f0f0;
        }
        .total {
            font-weight: bold;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div>
        <div style="display: flex; margin-bottom: 20px; border: 1px solid midnightblue; padding: 10px;">
            <h5 style="margin-inline: auto">1 - SOLUCIONES INFORMÁTICAS MJ S.C.A</h3>
            <div style="display: flex; flex-direction: row; gap: 20px; align-items: center;">
                <div style="display: flex; flex-direction: column;">
                    <div style="display: flex; flex-direction: row; border: 1px solid midnightblue; padding: 2px;">
                        <div style="background-color:midnightblue; color: white">DESDE FECHA</div>
                        <div>{{ $request->start_date }}</div>
                    </div>
                    <div style="display: flex; flex-direction: row; border: 1px solid midnightblue; padding: 2px;">
                        <div style="background-color:midnightblue; color: white">HASTA FECHA</div>
                        <div>{{ $request->end_date }}</div>
                    </div>
                </div>
                
                <div style="display: flex; flex-direction: column;">
                    <div style="display: flex; flex-direction: row; border: 1px solid midnightblue; padding: 2px;">
                        <div style="display: flex; flex-direction: row;">PROYECTO</div>
                        <div>{{ $project ? $project->name : 'Todos' }}</div>
                    </div>
                    <div style="display: flex; flex-direction: row; border: 1px solid midnightblue; padding: 2px;">
                        <div style="display: flex; flex-direction: row;">USUARIO</div>
                        <div>{{ $request->user}}</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="width: fit-content; color: midnightblue; text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 20px; border: 1px solid midnightblue; padding: 5px 10px;">
            INFORME DE TAREAS REALIZADAS
        </div>

        <table>
            <thead>
                <tr>
                    <th colspan="6">
                        {{ $project ? $project->name : 'Todos los proyectos' }}
                    </th>
                </tr>
                <tr >
                    <th>ID</th>
                    <th>Fecha y hora inicio</th>
                    <th>Fecha y hora fin</th>
                    <th>Duración (minutos)</th>
                    <th>Usuario</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                    <tr>
                        <td>{{ $task->id }}</td>
                        <td>{{ $task->start_datetime }}</td>
                        <td>{{ $task->end_datetime }}</td>
                        <td>{{ number_format($task->duration_minutes, 0) }}</td>
                        <td>{{ $task->user->name }}</td>
                        <td>{{ $task->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="total">Total minutos: {{ number_format($totalMinutes, 0) }}</p>
    </div>
</body>
</html>
