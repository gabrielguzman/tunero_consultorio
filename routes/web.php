<?php

use App\Http\Controllers\ProfileController;
// use App\Livewire\PatientBooking;  <-- BORRAR O COMENTAR ESTO
use App\Livewire\AdminSettings;  
use Illuminate\Support\Facades\Route;

/* --- RUTAS PÚBLICAS (Invitados y Pacientes Logueados) --- */

// Esta es la ÚNICA ruta de reservas ahora. Sirve para todos.
Route::get('/reservar', App\Livewire\GuestBooking::class)->name('guest.booking');

Route::get('/', function () {
    return redirect()->route('guest.booking');
});


/* --- RUTAS PROTEGIDAS --- */
Route::middleware(['auth', 'verified'])->group(function () {

    // === ÁREA DOCTORA ===
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::get('/configuracion', AdminSettings::class)->name('admin.settings');
    Route::get('/servicios', App\Livewire\AdminServices::class)->name('admin.services');
    Route::get('/reportes', App\Livewire\AdminStats::class)->name('admin.stats');
    Route::get('/pacientes', App\Livewire\AdminPatients::class)->name('admin.patients');
    Route::get('/finanzas', App\Livewire\AdminFinance::class)->name('admin.finance');
    Route::get('/recordatorios', App\Livewire\AdminReminders::class)->name('admin.reminders');


    // === ÁREA PACIENTE ===
    Route::get('/mis-turnos', App\Livewire\PatientAppointments::class)->name('patient.appointments');
    Route::get('/mis-hijos', App\Livewire\PatientManager::class)->name('patient.manager');
    Route::get('/mis-estudios', App\Livewire\PatientDocuments::class)->name('patient.documents');


    // === PERFIL ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';