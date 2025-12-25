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
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

#[Layout('layouts.booking')]
class GuestBooking extends Component
{
    // --- VARIABLES DE ESTADO ---
    public $step = 1;

    // --- COLECCIONES ---
    public $types;
    public $insurances;
    public $existingPatients = [];

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

    // --- DATOS DE CUENTA (OPCIONAL) ---
    public $create_account = false; // Checkbox para decidir si crear cuenta
    public $password = '';
    public $password_confirmation = '';

    // --- DATOS DEL PACIENTE (Hijo/a) ---
    public $selected_patient_id = 'new';
    public $child_name = '';
    public $child_dob = '';
    public $child_insurance_id = '';
    public $child_affiliate = '';

    public function mount()
    {
        $this->types = AppointmentType::where('active', true)->get();
        $this->insurances = HealthInsurance::orderBy('name')->get();
        $this->selectedDate = Carbon::today()->format('Y-m-d');

        if (Auth::check()) {
            $user = Auth::user();
            $this->parent_name = $user->name;
            $this->parent_email = $user->email;
            $this->parent_phone = $user->phone;
            $this->existingPatients = Patient::where('user_id', $user->id)->get();
        }
    }

    public function updatedSelectedDate() { $this->calculateSlots(); }
    public function updatedTypeId() { $this->calculateSlots(); }

    // Al cambiar la selección de paciente (Nuevo vs Existente)
    public function updatedSelectedPatientId($value)
    {
        if ($value === 'new') {
            $this->reset(['child_name', 'child_dob', 'child_insurance_id', 'child_affiliate']);
        } else {
            $patient = $this->existingPatients->firstWhere('id', $value);
            if ($patient) {
                $this->child_name = $patient->name;
                $this->child_dob = $patient->birth_date;
                $this->child_insurance_id = $patient->health_insurance_id;
                $this->child_affiliate = $patient->affiliate_number;
            }
        }
    }

    // Cálculo de Horarios Disponibles
    public function calculateSlots()
    {
        $this->availableSlots = [];
        $this->selectedTime = null;

        if (!$this->type_id || !$this->selectedDate) return;

        $settings = BusinessSetting::first();
        $startHour = $settings ? $settings->start_hour : 9;
        $endHour = $settings ? $settings->end_hour : 17;
        $workWeekends = $settings ? $settings->work_weekends : false;

        $type = AppointmentType::find($this->type_id);
        if (!$type) return;

        $date = Carbon::parse($this->selectedDate);
        if (!$workWeekends && $date->isWeekend()) return;

        $booked = Appointment::whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(function ($appointment) {
                return Carbon::parse($appointment->start_time)->format('H:i');
            })->toArray();

        $current = $date->copy()->setHour($startHour)->setMinute(0);
        $end = $date->copy()->setHour($endHour)->setMinute(0);
        $duration = $type->duration_minutes;

        while ($current->lt($end)) {
            $timeStr = $current->format('H:i');
            // No mostrar pasado si es hoy
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

    public function bookNewAppointmentForFamily()
    {
        $type = AppointmentType::find($this->type_id);
        $previousTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);
        $idealNextSlot = $previousTime->copy()->addMinutes($type->duration_minutes)->format('H:i');

        $this->selected_patient_id = 'new';
        $this->reset(['child_name', 'child_dob', 'child_insurance_id', 'child_affiliate']);
        
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

    public function confirmBooking()
    {
        // 1. Rate Limiter (Anti-Spam)
        $key = 'booking:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('generic', "Demasiados intentos. Espera unos segundos.");
            return;
        }
        RateLimiter::hit($key, 60);

        // 2. Definir Reglas de Validación
        $rules = [
            'parent_name' => 'required|string|min:3',
            'parent_dni' => 'required|numeric',
            'parent_phone' => 'required|numeric|min_digits:8',
            'child_name' => 'required|string|min:3',
            'child_dob' => 'required|date|before:today',
        ];

        if (Auth::check()) {
            $rules['parent_email'] = 'required|email';
        } else {
            // Si es invitado, validamos email único
            $rules['parent_email'] = 'required|email|unique:users,email';
            
            // SOLO validamos password si el usuario marcó la casilla
            if ($this->create_account) {
                $rules['password'] = 'required|min:8|confirmed';
            }
        }

        $this->validate($rules);

        // 3. Chequeo de Concurrencia (Doble reserva)
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

        // 4. GESTIÓN DEL USUARIO
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->phone !== $this->parent_phone) {
                $user->update(['phone' => $this->parent_phone]);
            }
        } else {
            // Decidimos la contraseña: La que puso O una aleatoria segura
            $finalPassword = $this->create_account 
                ? $this->password 
                : Str::random(16);

            $user = User::create([
                'name' => $this->parent_name,
                'email' => $this->parent_email,
                'phone' => $this->parent_phone,
                'password' => Hash::make($finalPassword),
                'role' => 'patient',
            ]);
            
            // Solo logueamos automáticamente si el usuario quizo crear cuenta
            if ($this->create_account) {
                Auth::login($user);
            }
        }

        // 5. GESTIÓN DEL PACIENTE
        $patient = null;
        if ($this->selected_patient_id !== 'new') {
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

        if (!$patient) {
            $patient = Patient::create([
                'user_id' => $user->id,
                'name' => $this->child_name,
                'birth_date' => $this->child_dob,
                'health_insurance_id' => $this->child_insurance_id ?: null,
                'affiliate_number' => $this->child_affiliate ?: null,
            ]);
            
            if (Auth::check()) {
                $this->existingPatients = Patient::where('user_id', $user->id)->get();
            }
        }

        // 6. CREAR EL TURNO
        $type = AppointmentType::find($this->type_id);
        
        Appointment::create([
            'user_id' => $user->id,
            'patient_id' => $patient->id,
            'appointment_type_id' => $this->type_id,
            'start_time' => $checkDate,
            'end_time' => $checkDate->copy()->addMinutes($type->duration_minutes),
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