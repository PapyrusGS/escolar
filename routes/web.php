<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/index', function () {
    return view('index');
});

Route::get('/usuarios/create', function () {
    return view('usuarios.create');
});

Route::get('/usuarios', function () {
    return view('usuarios.index');
});

Route::get('/perfil', function () {
    return view('perfil');
});

Route::get('/cursos', function () {
    return view('cursos.index');
});

Route::get('/cursos/visualizacion', function () {
    $user = auth()->user();
    if ($user && (int) $user->IdRol === 2) {
        return redirect('/docente/cursos');
    }
    return view('cursos.visualizacion');
})->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/reportes', function () {
    return view('reportes');
});

Route::get('/docente/cursos', function () {
    return view('docente.cursos');
});

Route::get('/docente/notas', function () {
    return view('docente.notas');
});
