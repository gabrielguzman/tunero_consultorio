<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\WorkSchedule;
use App\Models\BlockedDay;
use App\Models\Patient; // Importante
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('layouts.app')]
class PatientBooking extends Component
{
    // Datos Maestros
    public $types;
    public $userPatients; // Lista de hijos del usuario
    
    // Formulario
    public $patient_id; // Hijo seleccionado
    public $type_id;    // Tipo de consulta
    public $selectedDate;
    public $selectedTime;
    public $patient_notes;
    
    // UI Data
    public $availableSlots = [];
    public $currentStep = 1; // Para controlar flujo visual (opcional)

    public function mount()
    {
        // 1. Cargar Tipos de Turno
        $this->types = AppointmentType::active()->get();
        $this->type_id = $this->types->first()->id ?? null;

        // 2. Cargar Hijos del Usuario Logueado
        $this->userPatients = Auth::user()->patients;
        $this->patient_id = $this->userPatients->first()->id ?? null;

        // 3. Fecha inicial: Hoy
        $this->selectedDate = now()->format('Y-m-d');
        
        // 4. Calcular slots iniciales
        $this->generateAvailableSlots();
    }

    // Se dispara cuando el usuario cambia la fecha en el input
    public function updatedSelectedDate() 
    {
        $this->reset(['selectedTime']); // Limpiar hora si cambia el día
        $this->generateAvailableSlots();
    }

    // Se dispara cuando cambia el tipo de consulta (recalcula tiempos)
    public function updatedTypeId()
    {
        $this->reset(['selectedTime']);
        $this->generateAvailableSlots();
    }

    public function generateAvailableSlots()
    {
        $this->availableSlots = [];
        
        // Validaciones básicas
        if (!$this->selectedDate || !$this->type_id) return;

        $date = Carbon::parse($this->selectedDate);
        if ($date->isPast() && !$date->isToday()) return;

        // 1. Chequear Bloqueos (Feriados)
        if (BlockedDay::where('date', $date->format('Y-m-d'))->exists()) {
            return; // Día cerrado
        }

        // 2. Chequear Horario Laboral del Médico
        $dayOfWeek = $date->dayOfWeek; // 0=Dom, 6=Sab
        $schedule = WorkSchedule::where('day_of_week', $dayOfWeek)->first();

        if (!$schedule) return; // No trabaja este día de la semana

        // 3. Buscar turnos ya ocupados
        $bookedAppointments = Appointment::whereDate('start_time', $date->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->get();

        // 4. Generar Slots
        $type = AppointmentType::find($this->type_id);
        $duration = $type ? $type->duration_minutes : 30;

        $start = Carbon::parse($this->selectedDate . ' ' . $schedule->start_time);
        $end = Carbon::parse($this->selectedDate . ' ' . $schedule->end_time);
        $now = now();

        while ($start->copy()->addMinutes($duration) <= $end) {
            $slotStart = $start->copy();
            $slotEnd = $start->copy()->addMinutes($duration);

            // Filtro: Si es hoy, no mostrar horas pasadas
            if ($date->isToday() && $slotStart->lt($now)) {
                $start->addMinutes($duration);
                continue;
            }

            // Filtro: Superposición con turnos existentes
            $isOccupied = $bookedAppointments->contains(function ($app) use ($slotStart, $slotEnd) {
                // Lógica de superposición de rangos
                return $app->start_time < $slotEnd && $app->end_time > $slotStart;
            });

            if (!$isOccupied) {
                $this->availableSlots[] = $slotStart->format('H:i');
            }

            $start->addMinutes($duration);
        }
    }

    public function selectTime($time)
    {
        $this->selectedTime = $time;
    }

    public function saveAppointment()
    {
        $this->validate([
            'patient_id' => 'required',
            'type_id' => 'required',
            'selectedDate' => 'required|date',
            'selectedTime' => 'required',
        ]);

        // Crear fechas Carbon
        $start = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);
        $type = AppointmentType::find($this->type_id);
        $end = $start->copy()->addMinutes($type->duration_minutes);

        // Doble check de seguridad
        $exists = Appointment::where('status', '!=', 'cancelled')
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhereBetween('end_time', [$start, $end]);
            })->exists();

        if ($exists) {
            $this->addError('selectedTime', '¡Ups! Alguien te ganó el turno hace un segundo.');
            $this->generateAvailableSlots(); // Recargar
            return;
        }

        Appointment::create([
            'user_id' => Auth::id(),
            'patient_id' => $this->patient_id,
            'appointment_type_id' => $this->type_id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'scheduled',
            'patient_notes' => $this->patient_notes,
            'created_by' => Auth::id(),
        ]);

        session()->flash('success', '¡Turno confirmado! Te esperamos.');
        $this->reset(['selectedTime', 'patient_notes']);
        $this->generateAvailableSlots();
    }
}