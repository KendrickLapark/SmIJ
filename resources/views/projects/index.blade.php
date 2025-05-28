@extends('adminlte::page')

@section('title', 'Control de Proyectos')

@section('content')
<div style="display: flex; flex-direction: row; width: 100%;">
    <div style="display: flex; flex-direction: column; width: 100%; height: 100vh; background-color:burlywood">
        <div style="display: flex; flex-direction: row; justify-content: space-between; margin: 10px">
            <div>
                <h2>Control de proyectos</h2>
            </div>
            <div>
                @can('isAdmin')
                    <button id="addProjectBtn" class="btn btn-primary px-5"><i class="fas fa-fw fa-plus"></i></button>
                @endcan
                <button id="addTaskBtn" class="btn btn-secondary px-5"><i class="fas fa-fw fa-file"></i></button>
            </div>
        </div>
        <div>
            <ul id="projectsList" class="list-group mb-5"></ul>
        </div>
    </div>

    <div style="width: 100%; height: 100vh; wire:background-color: lightblue;">

        <h3>Calendario de Tareas</h3>
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
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
</div>
@endsection

@push('css')
@push('css')
<!-- FullCalendar CSS -->
<link href="https://unpkg.com/fullcalendar@6.1.4/main.min.css" rel="stylesheet" />
@endpush

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/fullcalendar@6.1.4/main.min.js"></script>
<script>
  // tu código aquí
</script>
@endpush

@endpush

@push('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/main.min.js"></script>

<script>
$(document).ready(function() {
    let projectModal = new bootstrap.Modal(document.getElementById('projectModal'));

    $('#addProjectBtn').click(() => projectModal.show());

    function loadProjects() {
        $.get('/projects/list', function(projects) {
            let html = '';
            projects.forEach(p => {
                html += `<li class="list-group-item">${p.name} - creado por ${p.creator.name}</li>`;
            });
            $('#projectsList').html(html);
        });
    }
    loadProjects();

    $('#projectForm').submit(function(e) {
        e.preventDefault();
        let data = $(this).serialize();

        $.post('/projects', data, function(res) {
            alert(res.message);
            projectModal.hide();
            $('#projectForm')[0].reset();
            loadProjects();
        }).fail(() => alert('Error al crear proyecto'));
    });

    // Inicializar FullCalendar
    let calendarEl = document.getElementById('calendar');
    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [] // Puedes cargar eventos vía AJAX más adelante
    });
    calendar.render();
});
</script>
@endpush
