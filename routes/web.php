<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/index', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/usuarios', function () {
    return view('usuarios.index');
});

Route::get('/usuarios/create', function () {
    return view('usuarios.create');
});

Route::get('/perfil', function () {
    return view('perfil');
});

Route::get('/cursos', function () {
    return view('cursos.index');
});

Route::get('/cursos/visualizacion', function () {
    return view('cursos.visualizacion');
});

Route::get('/cursos/inscripcion', function () {
    return view('cursos.inscripcion');
});

Route::get('/mis-notas', function () {
    return view('mis-notas');
});

Route::get('/reportes', function () {
    return view('reportes');
});

Route::get('/notificaciones', function () {
    return view('notificaciones');
});

Route::get('/docente/cursos', function () {
    return view('docente.cursos');
});

Route::get('/docente/notas', function () {
    return view('docente.notas');
});
