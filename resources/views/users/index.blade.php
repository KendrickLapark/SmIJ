@extends('adminlte::page')

@section('title', 'Control de usuarios')

@section('content')
<div class="container">
    <h2 class="mb-4">Gestión de Usuarios</h2>

    <form id="userForm">
        @csrf
        <input type="hidden" id="user_id">

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin">
            <label class="form-check-label" for="is_admin">Administrador</label>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>

    <hr class="my-4">

    <h4>Lista de usuarios</h4>
    <table class="table table-bordered table-striped" id="usersTable">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Admin</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script type="text/javascript">
$(document).ready(function () {
    console.log('JS funcionando');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    fetchUsers();

    function fetchUsers() {
        $.get("/users/list", function (data) {
            let rows = '';
            data.forEach(user => {
                rows += `
                    <tr>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.is_admin ? 'Sí' : 'No'}</td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="${user.id}">Eliminar</button>
                        </td>
                    </tr>`;
            });
            $("#usersTable tbody").html(rows);
        });
    }

    $("#userForm").submit(function (e) {
        e.preventDefault();

        const userId = $("#user_id").val();
        const formData = {
            name: $("#name").val(),
            email: $("#email").val(),
            password: $("#password").val(),
            is_admin: $("#is_admin").is(':checked') ? 1 : 0,
        };

        let url = '/users';
        let method = 'POST';

        if (userId) {
            url = `/users/${userId}`;
            method = 'PUT';
        }

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function () {
                $("#userForm")[0].reset();
                $("#user_id").val('');
                fetchUsers();
            },
            error: function (xhr) {
                alert('Error al guardar el usuario');
                console.error(xhr.responseText);
            }
        });
    });

    $(document).on('click', '.edit-btn', function () {
        const userId = $(this).data('id');
        $.get(`/users/${userId}`, function (user) {
            $("#user_id").val(user.id);
            $("#name").val(user.name);
            $("#email").val(user.email);
            $("#is_admin").prop('checked', user.is_admin);
            $("#password").val(''); // No mostrar password por seguridad
        });
    });

    $(document).on('click', '.delete-btn', function () {
        const userId = $(this).data('id');
        if (!confirm("¿Estás seguro de eliminar este usuario?")) return;

        $.ajax({
            url: `/users/${userId}`,
            type: 'DELETE',
            success: function () {
                fetchUsers();
            },
            error: function () {
                alert('Error al eliminar el usuario');
            }
        });
    });
});
</script>
@endpush
