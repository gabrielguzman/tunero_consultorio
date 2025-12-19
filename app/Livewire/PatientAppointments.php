<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('layouts.app')]
class PatientAppointments extends Component
{
    public $upcomingAppointments;
    public $pastAppointments;

    public function mount()
    {
        $this->loadAppointments();
    }

    public function loadAppointments()
    {
        // Buscamos turnos donde el usuario sea el padre/madre (user_id)
        // Ordenados por fecha
        $all = Appointment::where('user_id', Auth::id())
            ->with(['patient', 'type']) // Cargamos relaciones para no hacer mil consultas
            ->orderBy('start_time', 'asc')
            ->get();

        // Separamos usando las colecciones de Laravel
        $this->upcomingAppointments = $all->filter(function ($app) {
            return $app->start_time >= now() && $app->status !== 'cancelled';
        });

        // El historial incluye los pasados Y los cancelados
        $this->pastAppointments = $all->filter(function ($app) {
            return $app->start_time < now() || $app->status === 'cancelled';
        })->sortByDesc('start_time'); // Los más recientes primero
    }

    // Función para cancelar un turno
    public function cancelAppointment($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', Auth::id()) // Seguridad: que sea suyo
            ->first();

        if ($appointment) {
            // Opción A: Borrarlo definitivamente
            // $appointment->delete(); 
            
            // Opción B: Marcarlo como cancelado (Recomendado para historial)
            $appointment->update(['status' => 'cancelled']);
            
            session()->flash('message', 'El turno ha sido cancelado correctamente.');
            $this->loadAppointments(); // Recargar listas
        }
    }

    public function render()
    {
        return view('livewire.patient-appointments');
    }
}