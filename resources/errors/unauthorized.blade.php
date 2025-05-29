@extends('layouts.app')

@section('title', 'No autorizado')

@section('content')
<div class="container text-center mt-5">
    <h1 class="display-4">403 - No autorizado</h1>
    <p class="lead">No tienes permiso para acceder a esta página.</p>
    <a href="{{ url()->previous() }}" class="btn btn-primary">Volver</a>
</div>
@endsection
