<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use Carbon\Carbon;

#[Layout('layouts.app')]
class DoctorAgenda extends Component
{
    public function render()
    {
        // Traemos solo los turnos de HOY, ordenados por hora
        $appointments = Appointment::with(['patient', 'type'])
            ->whereDate('start_time', Carbon::today())
            ->where('status', '!=', 'cancelled') // No mostramos los cancelados
            ->orderBy('start_time')
            ->get();

        return view('livewire.doctor-agenda', [
            'appointments' => $appointments
        ]);
    }

    // --- ACCIÓN PARA LLAMAR AL PACIENTE (TV) ---
    public function callPatient($appointmentId)
    {
        $appt = Appointment::find($appointmentId);
        
        if ($appt) {
            // Actualizamos la fecha de llamado. 
            // Esto dispara la alerta en la pantalla de TV automáticamente.
            $appt->update(['called_at' => now()]);
            
            // Opcional: Mensaje de éxito
            session()->flash('message', "Llamando a {$appt->patient->name}...");
        }
    }

    // Acción para marcar como "En Consulta" (Para cambiar el color en la lista)
    public function startConsultation($appointmentId)
    {
        Appointment::find($appointmentId)->update(['status' => 'in_progress']);
    }
}