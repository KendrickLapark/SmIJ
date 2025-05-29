<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ReportController;

Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/users', [UserController::class, 'index' ])->name('users.index');
Route::get('/users/list', [UserController::class, 'list']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{user}', [UserController::class, 'update']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);

Route::middleware(['auth'])->group(function () {

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->middleware('can:isAdmin'); 
    Route::get('/projects/list', [ProjectController::class, 'list']); 
    Route::get('/tasks/user/{user}', [TaskController::class, 'tasksByUser']);
    Route::post('/tasks', [TaskController::class, 'store']); 
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::get('/reports/pdf', [ReportController::class, 'generatePdf'])->name('reports.pdf');
    Route::get('/users/{user}/projects', [UserController::class, 'projects']);
    
});
