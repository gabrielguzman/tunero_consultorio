<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\AppointmentType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Calendar extends Component
{
    public $events = []; // Lo pasaremos como Array, no como JSON string
    public $patients;
    public $types;

    // Modales
    public $isCreateModalOpen = false;
    public $isEditModalOpen = false;

    // Formulario
    public $selectedAppointmentId = null;
    public $newDate;
    public $newTime;
    public $patient_id;
    public $type_id;
    public $notes;
    public $doctor_notes;
    public $status;

    public function mount()
    {
        $this->patients = Patient::all();
        $this->types = AppointmentType::active()->get();
    }

    public function render()
    {
        $this->loadEvents();
        return view('livewire.calendar');
    }

    public function loadEvents()
    {
        $appointments = Appointment::with(['patient', 'type'])->get();

        // Transformamos a Array (no JSON string todavía)
        $this->events = $appointments->map(function ($app) {
            
            // BLINDAJE: Si el paciente o el tipo se borró, saltamos este turno para no romper el calendario
            if (!$app->patient || !$app->type) {
                return null;
            }

            // Colores
            $color = match($app->status) {
                'completed' => '#9CA3AF', // Gris
                'cancelled' => '#EF4444', // Rojo
                'scheduled' => $app->type->color ?? '#3B82F6',
                default => '#3B82F6'
            };

            return [
                'id' => $app->id,
                'title' => $app->patient->name,
                'start' => $app->start_time->toIso8601String(),
                'end'   => $app->end_time->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'status' => $app->status,
                    'notes' => $app->patient_notes
                ]
            ];
        })
        ->filter() // Elimina los nulos (datos rotos)
        ->values() // Reordena índices
        ->toArray(); // Devolvemos array puro
    }

    // --- FUNCIONES DEL CALENDARIO ---

    public function openCreateModal($dateStr)
    {
        $this->resetForm();
        $date = Carbon::parse($dateStr);
        $this->newDate = $date->format('Y-m-d');
        $this->newTime = $date->format('H:i');
        $this->isCreateModalOpen = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $app = Appointment::find($id);
        
        if (!$app) return;

        $this->selectedAppointmentId = $app->id;
        $this->patient_id = $app->patient_id;
        $this->type_id = $app->appointment_type_id;
        $this->newDate = $app->start_time->format('Y-m-d');
        $this->newTime = $app->start_time->format('H:i');
        $this->notes = $app->patient_notes;
        $this->doctor_notes = $app->doctor_notes;
        $this->status = $app->status;

        $this->isEditModalOpen = true;
    }

    public function saveNew()
    {
        $this->validate([
            'patient_id' => 'required',
            'type_id' => 'required',
            'newDate' => 'required',
            'newTime' => 'required',
        ]);

        $start = Carbon::parse($this->newDate . ' ' . $this->newTime);
        $type = AppointmentType::find($this->type_id);
        $end = $start->copy()->addMinutes($type->duration_minutes);
        $patient = Patient::find($this->patient_id);

        Appointment::create([
            'user_id' => $patient->user_id,
            'patient_id' => $this->patient_id,
            'appointment_type_id' => $this->type_id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'scheduled',
            'doctor_notes' => $this->notes,
            'created_by' => Auth::id(),
            'is_overtime' => true,
        ]);

        $this->closeModals();
        $this->dispatch('refresh-calendar'); 
    }

    public function saveEdit()
    {
        $app = Appointment::find($this->selectedAppointmentId);
        
        $start = Carbon::parse($this->newDate . ' ' . $this->newTime);
        if ($start->ne($app->start_time)) {
             $end = $start->copy()->addMinutes($app->type->duration_minutes);
             $app->start_time = $start;
             $app->end_time = $end;
        }

        $app->doctor_notes = $this->doctor_notes;
        $app->status = $this->status;
        $app->save();

        $this->closeModals();
        $this->dispatch('refresh-calendar');
    }

    public function deleteAppointment()
    {
        if ($this->selectedAppointmentId) {
            Appointment::destroy($this->selectedAppointmentId);
        }
        $this->closeModals();
        $this->dispatch('refresh-calendar');
    }

    public function closeModals()
    {
        $this->isCreateModalOpen = false;
        $this->isEditModalOpen = false;
    }

    public function resetForm()
    {
        $this->reset(['newDate', 'newTime', 'patient_id', 'type_id', 'notes', 'doctor_notes', 'selectedAppointmentId', 'status']);
        $this->type_id = $this->types->first()->id ?? null;
    }
}