<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\AppointmentType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Calendar extends Component
{
    // Variables para el Calendario
    public $events = [];

    // Variables para el Modal y Formulario
    public $isModalOpen = false;
    public $newDate;
    public $newTime;
    
    // Campos del formulario
    public $patient_id;
    public $type_id;
    public $notes;

    // Listas para los desplegables
    public $patients;
    public $types;

    public function mount()
    {
        // Cargamos listas una sola vez al iniciar
        $this->patients = Patient::all();
        $this->types = AppointmentType::active()->get();
    }

    public function render()
{
    $this->loadEvents(); // Cargar eventos actualizados
    
    // CAMBIO: Pasamos 'events' explícitamente a la vista
    return view('livewire.calendar', [
        'events' => $this->events
    ]);
}

    public function loadEvents()
    {
        $appointments = Appointment::with(['patient', 'type'])->get();

        $this->events = $appointments->map(function ($app) {
            return [
                'id' => $app->id,
                'title' => $app->patient->name . ' (' . $app->type->name . ')',
                'start' => $app->start_time->toIso8601String(),
                'end'   => $app->end_time->toIso8601String(),
                'color' => $app->type->color,
                'extendedProps' => [
                    'notes' => $app->doctor_notes
                ]
            ];
        })->toJson();
    }

    // --- FUNCIONES DEL MODAL ---

    // Esta función la llama JS cuando haces clic en una fecha
    public function openModal($dateStr)
    {
        $date = Carbon::parse($dateStr);
        
        $this->newDate = $date->format('Y-m-d');
        $this->newTime = $date->format('H:i');
        
        // Valores por defecto
        $this->type_id = $this->types->first()->id ?? null;
        $this->patient_id = $this->patients->first()->id ?? null;
        
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['notes']);
    }

    public function saveAppointment()
    {
        // 1. Validar datos básicos
        $this->validate([
            'patient_id' => 'required',
            'type_id' => 'required',
            'newDate' => 'required',
            'newTime' => 'required',
        ]);

        // 2. Calcular fecha inicio y fin
        $start = Carbon::createFromFormat('Y-m-d H:i', $this->newDate . ' ' . $this->newTime);
        
        // Buscamos la duración del tipo de turno seleccionado
        $type = AppointmentType::find($this->type_id);
        $end = $start->copy()->addMinutes($type->duration_minutes);

        // 3. Guardar (Aquí es donde tu hermana "Admin" ignora validaciones de horario)
        Appointment::create([
            'user_id' => Patient::find($this->patient_id)->user_id, // El padre del paciente
            'patient_id' => $this->patient_id,
            'appointment_type_id' => $this->type_id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'scheduled', // Confirmado por defecto si lo hace ella
            'doctor_notes' => $this->notes,
            'created_by' => Auth::id(),
            'is_overtime' => true, // Asumimos flexibilidad total para ella
        ]);

        // 4. Cerrar y avisar al frontend
        $this->closeModal();
        
        // Despachamos evento para recargar calendario sin F5
        $this->dispatch('appointment-saved'); 
    }
}