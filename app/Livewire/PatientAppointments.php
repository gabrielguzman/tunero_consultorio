<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout; // <--- 1. IMPORTAR ESTO

#[Layout('layouts.app')] // <--- 2. AGREGAR ESTA LÍNEA
class PatientAppointments extends Component
{
    public function cancel($id)
    {
        // Solo permitir cancelar si el turno pertenece al usuario logueado
        $appointment = Appointment::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($appointment) {
            $appointment->status = 'cancelled';
            $appointment->cancelled_by = Auth::id();
            $appointment->save();
            session()->flash('message', 'Turno cancelado correctamente.');
        }
    }

    public function render()
    {
        $appointments = Appointment::with('type')
            ->where('user_id', Auth::id())
            ->where('start_time', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();

        return view('livewire.patient-appointments', compact('appointments'));
    }
}