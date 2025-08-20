<?php

use App\Http\Controllers\AgendamientoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\FrontendCitaController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas públicas para reservas de citas
// Route::prefix('citas')->name('appointments.')->group(function () {
//     Route::get('/', [AppointmentController::class, 'index'])->name('index');
//     Route::get('/servicio/{service}', [AppointmentController::class, 'show'])->name('show');
//     Route::get('/servicio/{service}/horarios', [AppointmentController::class, 'getAvailableSlots'])->name('slots');
//     Route::post('/servicio/{service}/reservar', [AppointmentController::class, 'store'])->name('store');
//     Route::get('/exito/{appointment}', [AppointmentController::class, 'success'])->name('success');
//     Route::get('/cancelar/{id}/{token}', [AppointmentController::class, 'cancel'])->name('cancel');
// });



// Route::get('/agendar-cita', [FrontendCitaController::class, 'create'])->name('citas.create');
// Route::post('/agendar-cita', [FrontendCitaController::class, 'store'])->name('citas.store');

// Rutas públicas para agendamiento de citas
Route::prefix('/')->group(function () {
    Route::get('agendar-cita', [AgendamientoController::class, 'index'])->name('agendamiento.index');
    
    // API endpoints para el sistema de agendamiento
    Route::get('secretarias/{secretaria}/areas', [AgendamientoController::class, 'getAreas']);
    Route::get('areas/{area}/tramites', [AgendamientoController::class, 'getTramites']);
    Route::get('tramites/{tramite}/configuracion', [AgendamientoController::class, 'getConfiguracionTramite']);
    Route::get('tramites/{tramite}/fechas', [AgendamientoController::class, 'getFechasDisponibles']);
    Route::get('tramites/{tramite}/horas/{fecha}', [AgendamientoController::class, 'getHorasDisponibles']);
    
    // Procesar agendamiento
    Route::post('agendar-cita', [AgendamientoController::class, 'store'])->name('agendamiento.store');
    
    // Generar PDF de comprobante
    Route::get('cita/{numeroCita}/pdf', [AgendamientoController::class, 'generarPDF'])->name('cita.pdf');
    
    // Consultar cita
    Route::post('consultar-cita', [AgendamientoController::class, 'consultarCita'])->name('cita.consultar');
});