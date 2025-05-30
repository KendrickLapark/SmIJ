@extends('adminlte::page')

@section('title', 'Control de Proyectos')

@section('plugins.Fullcalendar', true)

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; width: 100%; padding: 10px">
    <div style="display: flex; flex-direction: column; width: 100%; height: fit-content; background-color:white; box-shadow: 0 2px 5px rgba(0,0,0,0.1)">
        <div style="display: flex; flex-direction: row; justify-content: space-between; padding: 10px; border-bottom: 1px solid lightgray;">
            <div>
                <h5>Control de proyectos</h2>
            </div>
            <div>
                @can('isAdmin')
                    <button id="addProjectBtn" class="btn btn-primary px-5"><i class="fas fa-fw fa-plus"></i></button>
                @endcan
                <button id="addPdfReport" class="btn btn-primary px-5"><i class="fas fa-fw fa-file"></i></button>
            </div>
        </div>
        <div style="display: flex; flex-direction: column; flex-grow: 1; height: 400px; overflow-y: auto; padding: 10px">
            <ul id="projectsList" class="list-group" style="gap: 1px"></ul>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; width: 100%; height: 100vh; background-color: white; margin-inline: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1)">

        <div style="margin: 10px;">
            <select id="userSelect" class="form-select" style="width: 300px; background-color: white; border: 1px solid midnightblue; border-radius: 5px; padding: 5px;">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'selected' : '' }}>
                        Mi calendario ({{ $user->name }})
                    </option>
                @endforeach
            </select>
        </div>

        <div id="calendar" style="height: calc(100vh - 70px); max-height: 100%; overflow-y: auto"></div>
    </div>

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
                    <h5 class="modal-title">Evento</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="taskStart" class="form-label">Inicio</label>
                        <input type="datetime-local" name="start_datetime" id="taskStart" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskDescription" class="form-label">Texto informativo</label>
                        <textarea name="description" id="taskDescription" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="taskEnd" class="form-label">Fin tarea</label>
                        <input type="datetime-local" name="end_datetime" id="taskEnd" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" id="cancelTaskBtn" class="btn btn-danger">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="pdfReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="pdfReportForm" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Opciones del informe</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reportStart" class="form-label">Fecha Inicio</label>
                        <input type="date" id="reportStart" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="reportEnd" class="form-label">Fecha Fin</label>
                        <input type="date" id="reportEnd" name="end_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="reportUser" class="form-label">Usuario</label>
                        <select id="reportUser" name="user_id" class="form-select" required>
                            <option value="" selected disabled>Seleccione un usuario</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reportProject" class="form-label">Proyecto</label>
                        <select id="reportProject" name="project_id" class="form-select" disabled required>
                            <option value="" selected disabled>Seleccione un proyecto</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="generatePdfBtn" class="btn btn-primary" disabled>
                        <i class="fas fa-save"></i> Generar
                    </button>
                    <button type="button" id="generatePdfBtn" class="btn btn-danger" disabled>
                        <i class="fas fa-back"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('footer')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 20px;">
    <span>2025 <a href="https://solucionesinformaticasmj.com/">Soluciones informáticas MI, S.C.A</a></span>
</div>
@stop

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    let projectModal = new bootstrap.Modal(document.getElementById('projectModal'));
    let taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
    $('#cancelTaskBtn').on('click', function() {
        taskModal.hide();
    });
    const authenticatedUserId = {{ auth()->id() }};
    let currentUserId = authenticatedUserId;

    $('#userSelect').val(authenticatedUserId); 
       
    $('#userSelect').change(function() {
        currentUserId = $(this).val();
        calendar.refetchEvents();
    });

    $('#addProjectBtn').on('click', function () {
        projectModal.show();
    });

    const pdfReportModal = new bootstrap.Modal(document.getElementById('pdfReportModal'));

    $('#addPdfReport').on('click', function() {
        $('#pdfReportForm')[0].reset();
        $('#reportProject').html('<option value="" selected disabled>Seleccione un proyecto</option>').prop('disabled', true);
        $('#generatePdfBtn').prop('disabled', true);
        pdfReportModal.show();
    });

    $('#reportUser').on('change', function() {
    let userId = $(this).val();
    if (!userId) {
        $('#reportProject').prop('disabled', true);
        return;
    }
    $('#reportProject').prop('disabled', true).html('<option>Cargando...</option>');
        $.ajax({
            url: '/users/' + userId + '/projects', 
            type: 'GET',
            success: function(projects) {
                let options = '<option value="" selected disabled>Seleccione un proyecto</option>';
                projects.forEach(p => {
                    options += `<option value="${p.id}">${p.name}</option>`;
                });
                $('#reportProject').html(options).prop('disabled', false);
            },
            error: function() {
                alert('Error cargando proyectos del usuario');
                $('#reportProject').html('<option value="" selected disabled>Seleccione un proyecto</option>').prop('disabled', true);
            }
        });
    });

    function toggleGenerateButton() {
        const start = $('#reportStart').val();
        const end = $('#reportEnd').val();
        const user = $('#reportUser').val();
        const project = $('#reportProject').val();

        $('#generatePdfBtn').prop('disabled', !(start && end && user && project));
    }

    $('#reportStart, #reportEnd, #reportUser, #reportProject').on('change input', toggleGenerateButton);

    $('#pdfReportForm').on('submit', function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        let queryString = $.param({
            start_date: $('#reportStart').val(),
            end_date: $('#reportEnd').val(),
            user_id: $('#reportUser').val(),
            project_id: $('#reportProject').val(),
        });

        window.open('/reports/pdf?' + queryString, '_blank');

        $('#pdfReportModal').modal('hide');
    });


    function loadProjects() {
        $.get('/projects/list', function (projects) {
            let html = '';
            projects.forEach(p => {
                let createdAt = new Date(p.created_at);
                const formattedDate = createdAt.toLocaleString('es-ES', {
                    dateStyle: 'medium',
                    timeStyle: 'short'
                });
                html += `<li class="list-group-item bg-gradient-warning fc-event" 
                            data-event='{"title":"${p.name}"}' 
                            data-project-id="${p.id}"
                            style="cursor: move; margin-bottom: 5px;">
                            <div style="display: flex; flex-direction: column; align-items: space-between;">
                                <div>${p.name} - creado por ${p.creator.name} </div> 
                                <div> ${formattedDate} </div>
                            </div>
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
            selectable: true,
            select: function(info) {
                $('#taskStart').val(info.start.toISOString().slice(0,16)); 
                $('#taskEnd').val(info.end.toISOString().slice(0,16));
    
                taskModal.show();
            },
            headerToolbar: {
                left: 'prev customView next',                  
                center: 'title',           
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            customButtons: {
                customView: {
                    text: '',
                    click: function() {
                        if (calendar.view.type === 'dayGridMonth') {
                            calendar.changeView('timeGridDay');
                        } else {
                            calendar.changeView('dayGridMonth');
                        }
                    }
                }
            },
            views: {
                dayGridMonth: { buttonText: 'Mes' },
                timeGridWeek: { buttonText: 'Semana' },
                timeGridDay: { 
                    buttonText: 'Día',
                    slotLabelFormat: {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: false
                    },
                    allDaySlot: false,
                }
            },
            drop: function(info) {
                let originalEl = info.draggedEl; 
    let projectId = originalEl.getAttribute('data-project-id');
    if (!projectId) {
        alert('No se pudo identificar el proyecto.');
        return;
    }

    $('#projectsList li').each(function() {
        if ($(this).data('project-id') == projectId) {
            $(this).remove(); 
        }
    });

    $('#taskProjectId').val(projectId);
    $('#taskTitle').val(originalEl.innerText.trim());

    let dt = new Date(info.date);
    let dtEnd = new Date(dt.getTime() + 30*60000);
    
    function formatLocalDateTime(date) {
        const pad = n => n.toString().padStart(2, '0');
        return `${date.getFullYear()}-${pad(date.getMonth()+1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

    let dtLocalStart = formatLocalDateTime(dt);
    let dtLocalEnd = formatLocalDateTime(dtEnd);


    $('#taskStart').val(dtLocalStart);
    $('#taskEnd').val(dtLocalEnd);

    taskModal.show();
            },
            eventReceive: function(info) {
                info.event.remove();
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
            },
            eventContent: function(arg) {
                let title = arg.event.title;
                let description = arg.event.extendedProps.description || '';
                return { 
                    html: `<div style="font-weight:600;">${title}</div><div style="font-size:smaller;">${description}</div>`
                };
            },
            eventDrop: function(info) {
                const event = info.event;

                let start = event.start.toISOString();
                let end;

                if (event.end) {
                    end = event.end.toISOString();
                } else {
                    let tempEnd = new Date(event.start.getTime() + 60 * 60 * 1000); // +1 hora
                    end = tempEnd.toISOString();
                }

                $.ajax({
                    url: '/tasks/' + event.id,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_datetime: start,
                        end_datetime: end
                    },
                    success: function(res) {
                        alert(res.message || 'Tarea actualizada');
                    },
                    error: function() {
                        alert('Error al actualizar la tarea');
                        info.revert(); 
                    }
                });
            },
            eventResize: function(info) {
                const event = info.event;

                $.ajax({
                    url: '/tasks/' + event.id,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_datetime: event.start.toISOString(),
                        end_datetime: event.end ? event.end.toISOString() : null
                    },
                    success: function(res) {
                        alert(res.message || 'Tarea actualizada');
                    },
                    error: function() {
                        alert('Error al actualizar la tarea');
                        info.revert();
                    }
                });
            },
        });
        calendar.render();

        $('.fc-customView-button').html('<i class="fas fa-calendar"></i>');

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

