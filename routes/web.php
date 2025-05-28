<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/users', function(){
    return view('users.index');
})->name('users.index');
Route::get('/users/list', [UserController::class, 'list']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{user}', [UserController::class, 'update']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Ruta para Control proyectos
Route::get('/projects', function () {
    return view('projects.index');
})->name('projects.index');

Route::middleware(['auth'])->group(function () {

    Route::get('/projects', [ProjectController::class, 'index'])->middleware('can:isAdmin')->name('projects.index'); // vista principal proyectos y calendario

    Route::post('/projects', [ProjectController::class, 'store'])->middleware('can:isAdmin'); // solo admin puede añadir proyectos

    Route::get('/projects/list', [ProjectController::class, 'list']); // listado ajax proyectos ordenados

    Route::get('/tasks/user/{user}', [TaskController::class, 'tasksByUser']); // tareas de usuario en ajax para calendario

    Route::post('/tasks', [TaskController::class, 'store']); // crear tarea (arrastrar al calendario)

    Route::get('/reports/tasks', [TaskController::class, 'report'])->name('tasks.report'); // vista filtro informes
    Route::post('/reports/tasks/pdf', [TaskController::class, 'generatePdf']); // generar PDF filtrado

});
