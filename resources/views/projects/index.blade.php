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

    <div style="width: 100%; height: 100vh; background-color: lightblue;">
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

@push('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>

<script>
$(document).ready(function () {
    let projectModal = new bootstrap.Modal(document.getElementById('projectModal'));

    $('#addProjectBtn').on('click', function () {
        projectModal.show();
    });

    function loadProjects() {
        $.get('/projects/list', function (projects) {
            let html = '';
            projects.forEach(p => {
                html += `<li class="list-group-item" draggable="true">${p.name} - creado por ${p.creator.name}</li>`;
            });
            $('#projectsList').html(html);
        });
    }
    loadProjects();

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


        $(document).ready(function() {

            var SITEURL = "{{ url('/') }}";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });           

            var calendar = $('#calendar').fullCalendar({
                dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto',
                    'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ],
                header: {
                    left: 'prev',
                    center: 'title,today',
                    right: 'next'
                },
                buttonText: {
                    today: 'HOY',
                    day: 'DIA',
                    week: 'SEMANA',
                    month: 'MES'
                },
                
                firstDay: 1,
                editable: true,
                displayEventTime: true,
                events: SITEURL + "/fullcalender",
                displayEventTime: false,
                editable: true,
                events: [
                ],
                editable: false,
                selectable: true,
                selectHelper: true,               

                dayClick: function( date, jsEvent, view){               

                    //date=moment(date).format("dddd DD [de] MMMM");  
                    
                    //$request->session()->flash('FechaSeleccionada', date);

                    $('#fullcalendarModal .modal-title').text(date); 

                    $('#fullcalendarModal').modal('show');                         
                                        
                },

                eventClick: function(event) {
                    location.href = 'showThatActivity/' + event.id;
                }

            });

        });
});
</script>
@endpush
