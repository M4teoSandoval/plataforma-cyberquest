<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StudentController::class, 'landing'])->name('landing');
Route::get('/empezar', [StudentController::class, 'showRegister'])->name('registro');
Route::post('/empezar', [StudentController::class, 'register'])->name('registro.store');
Route::post('/reingreso', [StudentController::class, 'requestReentry'])->name('reingreso.solicitar');
Route::get('/reingreso/espera', [StudentController::class, 'reentryWait'])->name('reingreso.espera');
Route::get('/reingreso/estado', [StudentController::class, 'reentryStatus'])->name('reingreso.estado');
Route::get('/misiones', [StudentController::class, 'missions'])->name('misiones');
Route::post('/misiones/{mission}/flag', [StudentController::class, 'submitFlag'])->name('flag.submit');
Route::post('/finalizar', [StudentController::class, 'finish'])->name('finalizar');
Route::get('/resultado', [StudentController::class, 'results'])->name('resultado');
Route::get('/encuesta', [StudentController::class, 'survey'])->name('encuesta');
Route::get('/certificado', [StudentController::class, 'certificate'])->name('certificado');
Route::post('/salir', [StudentController::class, 'logout'])->name('salir');

Route::get('/profesor', [TeacherController::class, 'showLogin'])->name('profesor.login');
Route::post('/profesor', [TeacherController::class, 'login'])->name('profesor.login.store');
Route::get('/profesor/panel', [TeacherController::class, 'panel'])->name('profesor.panel');
Route::get('/profesor/exportar', [TeacherController::class, 'export'])->name('profesor.exportar');
Route::post('/profesor/estudiantes/{student}/reingreso', [TeacherController::class, 'allowReentry'])->name('profesor.reingreso');
Route::delete('/profesor/estudiantes/{student}/reingreso', [TeacherController::class, 'rejectReentry'])->name('profesor.reingreso.rechazar');
Route::delete('/profesor/estudiantes/{student}', [TeacherController::class, 'destroy'])->name('profesor.eliminar');
Route::post('/profesor/salir', [TeacherController::class, 'logout'])->name('profesor.salir');
