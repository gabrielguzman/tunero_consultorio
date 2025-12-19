<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\PatientBooking; 
use App\Livewire\AdminSettings;  
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- RUTAS PÚBLICAS (Invitados) ---

// 1. La ruta pública se queda con "/reservar" (es más fácil para compartir)
Route::get('/reservar', App\Livewire\GuestBooking::class)->name('guest.booking');

// 2. La Home redirige a la reserva pública
Route::get('/', function () {
    return redirect()->route('guest.booking');
});


// --- RUTAS PROTEGIDAS (Requieren Login) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // === ÁREA DE LA DOCTORA (ADMIN) ===
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/configuracion', AdminSettings::class)->name('admin.settings');
    Route::get('/servicios', App\Livewire\AdminServices::class)->name('admin.services');
    Route::get('/reportes', App\Livewire\AdminStats::class)->name('admin.stats');
    Route::get('/pacientes', App\Livewire\AdminPatients::class)->name('admin.patients');


    // === ÁREA DEL PACIENTE (PADRES) ===
    
    // CAMBIO IMPORTANTE AQUÍ:
    // Cambiamos la URL de '/reservar' a '/nuevo-turno' para que no choque con la pública.
    Route::get('/nuevo-turno', PatientBooking::class)->name('patient.booking');
    
    Route::get('/mis-turnos', App\Livewire\PatientAppointments::class)->name('patient.appointments');
    Route::get('/mis-hijos', App\Livewire\PatientManager::class)->name('patient.manager');
    Route::get('/mis-estudios', App\Livewire\PatientDocuments::class)->name('patient.documents');


    // === GESTIÓN DE PERFIL ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';