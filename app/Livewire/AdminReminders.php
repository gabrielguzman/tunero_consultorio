<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AdminReminders extends Component
{
    public function render()
    {
        // Buscamos turnos de MAÑANA
        $tomorrow = Carbon::tomorrow();

        $appointments = Appointment::with(['patient.user', 'type'])
            ->whereDate('start_time', $tomorrow)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();

        return view('livewire.admin-reminders', [
            'appointments' => $appointments,
            'date' => $tomorrow
        ]);
    }
    
    // Función para marcar como "Enviado" (Opcional, requiere campo en BD)
    public function markAsSent($id)
    {
        // Aquí podrías guardar en la BD que ya se le avisó
        // Appointment::find($id)->update(['reminder_sent' => true]);
    }
}