<?php

namespace App\Livewire;

use App\Models\Appointment;
use Livewire\Component;

class Calendar extends Component
{
    public function render()
    {
        $events = [];

        $appointments = Appointment::with(['patient','type'])->get();
        
        foreach ($appointments as $appointment) {
            $events[] = [
                'id' => $appointment->id,
                'title' => $appointment->patient->name . ' - ' . $appointment->type->name,
                'start' => $appointment->start_time->toIso8601String(),
                'end'   => $appointment->end_time->toIso8601String(),
                'color' => $appointment->type->color, // El color que definimos en la DB (Verde/Rojo)
                // Datos extra para usar al hacer click
                'extendedProps' => [
                    'status' => $appointment->status,
                    'notes' => $appointment->doctor_notes,
                ]
            ];
        }

        return view('livewire.calendar', [
            'events' => json_encode($events)
        ]);
    }
}
