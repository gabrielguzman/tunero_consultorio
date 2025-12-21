<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\AppointmentType;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Patient;
use App\Models\HealthInsurance;
use App\Models\BusinessSetting; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.booking')]
class GuestBooking extends Component
{
    // --- VARIABLES DE ESTADO ---
    public $step = 1;

    // --- COLECCIONES ---
    public $types;
    public $insurances;
    public $existingPatients = []; // Lista de hijos del usuario logueado

    // --- SELECCIÓN DE TURNO ---
    public $type_id;
    public $selectedDate;
    public $selectedTime;
    public $availableSlots = [];

    // --- DATOS DEL RESPONSABLE (Padre/Madre) ---
    public $parent_name = '';
    public $parent_dni = '';
    public $parent_email = '';
    public $parent_phone = '';

    // --- DATOS DEL PACIENTE (Hijo/a) ---
    public $selected_patient_id = 'new'; // 'new' o ID del paciente
    public $child_name = '';
    public $child_dob = '';
    public $child_insurance_id = '';
    public $child_affiliate = '';

    public function mount()
    {
        // 1. Cargar datos básicos
        $this->types = AppointmentType::where('active', true)->get();
        $this->insurances = HealthInsurance::orderBy('name')->get();
        $this->selectedDate = Carbon::today()->format('Y-m-d');

        // 2. Si está logueado, precargar datos del Padre y sus Hijos
        if (Auth::check()) {
            $user = Auth::user();
            
            // Datos del Padre
            $this->parent_name = $user->name;
            $this->parent_email = $user->email;
            $this->parent_phone = $user->phone;
            // $this->parent_dni = $user->dni; // Descomentar si tu User tiene columna dni
            
            // Cargar Hijos existentes
            $this->existingPatients = Patient::where('user_id', $user->id)->get();
        }
    }

    // --- LISTENERS DE CAMBIO ---

    // Cuando cambia la fecha o el tipo, recalculamos horarios
    public function updatedSelectedDate() { $this->calculateSlots(); }
    public function updatedTypeId() { $this->calculateSlots(); }

    // Cuando selecciona un hijo de la lista (o elige 'Nuevo')
    public function updatedSelectedPatientId($value)
    {
        if ($value === 'new') {
            // Limpiar para cargar uno nuevo
            $this->child_name = '';
            $this->child_dob = '';
            $this->child_insurance_id = '';
            $this->child_affiliate = '';
        } else {
            // Buscar al hijo y autocompletar
            $patient = $this->existingPatients->firstWhere('id', $value);
            if ($patient) {
                $this->child_name = $patient->name;
                $this->child_dob = $patient->birth_date;
                $this->child_insurance_id = $patient->health_insurance_id;
                $this->child_affiliate = $patient->affiliate_number;
            }
        }
    }

    // --- LÓGICA DE HORARIOS ---
    public function calculateSlots()
    {
        $this->availableSlots = [];
        $this->selectedTime = null;

        if (!$this->type_id || !$this->selectedDate) return;

        // Leer configuración del negocio (Horarios)
        $settings = BusinessSetting::first();
        $startHour = $settings ? $settings->start_hour : 9;
        $endHour = $settings ? $settings->end_hour : 17;
        $workWeekends = $settings ? $settings->work_weekends : false;

        $type = AppointmentType::find($this->type_id);
        if (!$type) return;

        $date = Carbon::parse($this->selectedDate);

        // Validar fin de semana
        if (!$workWeekends && $date->isWeekend()) return;

        // Turnos ya ocupados en la BD
        $booked = Appointment::whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(function ($appointment) {
                return Carbon::parse($appointment->start_time)->format('H:i');
            })
            ->toArray();

        // Generar grilla
        $current = $date->copy()->setHour($startHour)->setMinute(0);
        $end = $date->copy()->setHour($endHour)->setMinute(0);
        $duration = $type->duration_minutes;

        while ($current->lt($end)) {
            $timeStr = $current->format('H:i');

            // No mostrar horarios pasados si es hoy
            if ($date->isToday() && $current->lt(now())) {
                $current->addMinutes($duration);
                continue;
            }

            if (!in_array($timeStr, $booked)) {
                $this->availableSlots[] = $timeStr;
            }
            $current->addMinutes($duration);
        }
    }

    public function selectSlot($time)
    {
        $this->selectedTime = $time;
        $this->step = 2;
    }

    public function back()
    {
        $this->step = 1;
        $this->calculateSlots();
    }

    // --- AGENDAR OTRO HERMANO (VIA RÁPIDA) ---
    public function bookNewAppointmentForFamily()
    {
        $type = AppointmentType::find($this->type_id);
        $previousTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);
        $idealNextSlot = $previousTime->copy()->addMinutes($type->duration_minutes)->format('H:i');

        // Resetear datos del niño a "Nuevo"
        $this->selected_patient_id = 'new';
        $this->child_name = '';
        $this->child_dob = '';
        $this->child_insurance_id = '';
        $this->child_affiliate = '';

        $this->calculateSlots();

        if (in_array($idealNextSlot, $this->availableSlots)) {
            $this->selectedTime = $idealNextSlot;
            $this->step = 2;
            session()->flash('auto_msg', "Turno consecutivo ({$idealNextSlot} hs) seleccionado automáticamente.");
        } else {
            $this->selectedTime = null;
            $this->step = 1;
        }
    }

    // --- CONFIRMAR Y GUARDAR ---
    public function confirmBooking()
    {
        // 1. Rate Limiter (Anti-Spam)
        $key = 'booking:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('generic', "Demasiados intentos. Espera unos segundos.");
            return;
        }
        RateLimiter::hit($key, 60);

        // 2. Limpieza de inputs
        $this->parent_dni = preg_replace('/[^0-9]/', '', $this->parent_dni);
        $this->parent_phone = preg_replace('/[^0-9]/', '', $this->parent_phone);

        // 3. Validación
        $this->validate([
            'parent_name' => 'required|string|min:3',
            'parent_dni' => 'required|numeric|digits_between:7,8',
            'parent_email' => 'required|email',
            'parent_phone' => 'required|numeric|min_digits:8',
            'child_name' => 'required|string|min:3',
            'child_dob' => 'required|date|before:today',
        ]);

        // 4. Anti-Doble Reserva (Concurrency Check)
        $checkDate = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);
        $isTaken = Appointment::where('start_time', $checkDate)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($isTaken) {
            $this->addError('selectedTime', 'Este horario acaba de ser ocupado.');
            $this->step = 1;
            $this->calculateSlots();
            return;
        }

        // 5. GESTIÓN DE USUARIO (Padre/Madre)
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
            // Actualizar teléfono si cambió
            if ($user->phone !== $this->parent_phone) {
                $user->update(['phone' => $this->parent_phone]);
            }
        } else {
            // Buscar por email o Crear
            $user = User::firstOrCreate(
                ['email' => $this->parent_email],
                [
                    'name' => $this->parent_name,
                    'phone' => $this->parent_phone,
                    'password' => Hash::make(Str::random(10)), // Password temporal
                    'role' => 'patient',
                ]
            );
        }

        // 6. GESTIÓN DEL PACIENTE (Hijo/a)
        $patient = null;

        if ($this->selected_patient_id !== 'new') {
            // A) Actualizar existente
            $patient = Patient::find($this->selected_patient_id);
            if ($patient) {
                $patient->update([
                    'name' => $this->child_name,
                    'birth_date' => $this->child_dob,
                    'health_insurance_id' => $this->child_insurance_id ?: null,
                    'affiliate_number' => $this->child_affiliate ?: null,
                ]);
            }
        }

        // B) Crear nuevo si no existe o se eligió 'new'
        if (!$patient) {
            $patient = Patient::create([
                'user_id' => $user->id,
                'name' => $this->child_name,
                'birth_date' => $this->child_dob,
                'health_insurance_id' => $this->child_insurance_id ?: null,
                'affiliate_number' => $this->child_affiliate ?: null,
            ]);
            
            // Si estaba logueado, recargamos la lista para la próxima
            if(Auth::check()) {
                $this->existingPatients = Patient::where('user_id', $user->id)->get();
            }
        }

        // 7. CREAR TURNO
        $startTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);
        $type = AppointmentType::find($this->type_id);

        Appointment::create([
            'user_id' => $user->id,
            'patient_id' => $patient->id,
            'appointment_type_id' => $this->type_id,
            'start_time' => $startTime,
            'end_time' => $startTime->copy()->addMinutes($type->duration_minutes),
            'status' => 'confirmed',
            'patient_notes' => 'Reserva Web',
            'created_by' => $user->id,
        ]);

        RateLimiter::clear($key);
        $this->step = 3;
    }

    public function render()
    {
        $businessName = BusinessSetting::first()->business_name ?? 'Consultorio Médico';
        return view('livewire.guest-booking', ['businessName' => $businessName]);
    }
}