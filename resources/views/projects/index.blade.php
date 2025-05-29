@extends('adminlte::page')

@section('title', 'Control de Proyectos')

@section('plugins.Fullcalendar', true)

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; width: 100%; padding: 10px">
    <div style="display: flex; flex-direction: column; width: 100%; height: fit-content; background-color:white">
        <div style="display: flex; flex-direction: row; justify-content: space-between; padding: 10px; border-bottom: 1px solid lightgray;">
            <div>
                <h5>Control de proyectos</h2>
            </div>
            <div>
                @can('isAdmin')
                    <button id="addProjectBtn" class="btn btn-primary px-5"><i class="fas fa-fw fa-plus"></i></button>
                @endcan
                <button id="addTaskBtn" class="btn btn-secondary px-5"><i class="fas fa-fw fa-file"></i></button>
            </div>
        </div>
        <div style="display: flex; flex-direction: column; flex-grow: 1; height: 400px; overflow-y: auto; padding: 10px">
            <ul id="projectsList" class="list-group" style="gap: 10px"></ul>
        </div>
    </div>

    <div style="width: 100%; height: 100vh; background-color: lightblue;">

        <div style="margin-bottom: 15px;">
            <label for="userSelect" class="form-label">Usuario:</label>
            <select id="userSelect" class="form-select" style="max-width: 300px;">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="calendar"></div>
    </div>

    <!-- Modal para añadir proyecto -->
    <div class="modal fade" id="projectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="projectForm" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Proyecto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="name" id="projectName" class="form-control" placeholder="Nombre del proyecto" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" style="display: flex; margin-block: 5px;">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="taskForm" class="modal-content">
                @csrf
                <input type="hidden" name="project_id" id="taskProjectId">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="taskTitle" class="form-label">Título</label>
                        <input type="text" name="title" id="taskTitle" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskStart" class="form-label">Fecha inicio</label>
                        <input type="datetime-local" name="start_datetime" id="taskStart" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskEnd" class="form-label">Fecha fin</label>
                        <input type="datetime-local" name="end_datetime" id="taskEnd" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskDescription" class="form-label">Descripción</label>
                        <textarea name="description" id="taskDescription" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar tarea</button>
                </div>
            </form>
        </div>
    </div>


</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    let projectModal = new bootstrap.Modal(document.getElementById('projectModal'));
    let taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
    let currentUserId = {{ auth()->id() }};

    $('#userSelect').change(function() {
        currentUserId = $(this).val();
        calendar.refetchEvents();
    });


    $('#addProjectBtn').on('click', function () {
        projectModal.show();
    });

    function loadProjects() {
        $.get('/projects/list', function (projects) {
            let html = '';
            projects.forEach(p => {
                html += `<li class="list-group-item bg-gradient-warning fc-event" 
                            data-event='{"title":"${p.name}"}' 
                            data-project-id="${p.id}"
                            style="cursor: move; margin-bottom: 5px;">
                            ${p.name} - creado por ${p.creator.name}
                         </li>`;
            });
            $('#projectsList').html(html);
            initExternalEvents();
        });
    }

    function initExternalEvents() {
        new FullCalendar.Draggable(document.getElementById('projectsList'), {
            itemSelector: '.fc-event',
            eventData: function (el) {
                return JSON.parse(el.getAttribute('data-event'));
            }
        });
    }

    $('#projectForm').submit(function (e) {
        e.preventDefault();
        let data = $(this).serialize();

        $.post('/projects', data, function (res) {
            alert(res.message);
            projectModal.hide();
            $('#projectForm')[0].reset();
            loadProjects();
        }).fail(() => alert('Error al crear proyecto'));
    });

    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            droppable: true,
            editable: true,
            drop: function(info) {
                let projectId = info.draggedEl.getAttribute('data-project-id');
                if (!projectId) {
                    alert('No se pudo identificar el proyecto.');
                    return;
                }
                $('#taskProjectId').val(projectId);
                $('#taskTitle').val(info.draggedEl.innerText.trim());
                let dt = new Date(info.date);
                let dtLocal = dt.toISOString().slice(0,16);
                $('#taskStart').val(dtLocal);
                $('#taskEnd').val(dtLocal);

                taskModal.show();
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: '/tasks/user/' + currentUserId,
                    dataType: 'json',
                    success: function(data) {
                        successCallback(data);
                    },
                    error: function() {
                        failureCallback();
                    }
                });
            }
        });
        calendar.render();

        // Enviar formulario de tarea y agregar evento al calendario
        $('#taskForm').submit(function(e) {
            e.preventDefault();
            let formData = $(this).serialize();

            $.post('/tasks', formData, function(res) {
                alert(res.message);
                taskModal.hide();
                calendar.refetchEvents();
            }).fail(() => alert('Error al crear tarea'));
        });
    }

    loadProjects();
});
</script>
@endpush

