<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\AppointmentType;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Patient;
use App\Models\HealthInsurance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;

#[Layout('layouts.booking')]
class GuestBooking extends Component
{
    // Pasos del Wizard
    public $step = 1;

    // Colecciones para los selectores
    public $types;
    public $insurances;

    // Selección de Turno
    public $type_id;
    public $selectedDate;
    public $selectedTime;
    public $availableSlots = [];

    // Datos del PADRE/MADRE (Usuario)
    public $parent_name = '';
    public $parent_dni = '';
    public $parent_email = '';
    public $parent_phone = '';

    // Datos del HIJO/A (Paciente)
    public $child_name = '';
    public $child_dob = '';
    public $child_insurance_id = '';
    public $child_affiliate = ''; // <--- ESTA ERA LA VARIABLE QUE FALTABA

    public function mount()
    {
        // Cargar datos iniciales (solo activos)
        $this->types = AppointmentType::where('active', true)->get();
        $this->insurances = HealthInsurance::orderBy('name')->get();

        // Configurar fecha por defecto (hoy)
        $this->selectedDate = Carbon::today()->format('Y-m-d');
    }

    // Detectar cambios para recalcular horarios
    public function updatedSelectedDate()
    {
        $this->calculateSlots();
    }
    public function updatedTypeId()
    {
        $this->calculateSlots();
    }

    public function calculateSlots()
    {
        $this->availableSlots = [];
        $this->selectedTime = null;

        if (!$this->type_id || !$this->selectedDate) return;

        $startHour = 9;
        $endHour = 17;

        $type = AppointmentType::find($this->type_id);
        if (!$type) return;

        $duration = $type->duration_minutes;
        $date = Carbon::parse($this->selectedDate);

        if ($date->isWeekend()) return;

        // Buscar turnos ocupados
        $booked = Appointment::whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(function ($appointment) {
                return Carbon::parse($appointment->start_time)->format('H:i');
            })
            ->toArray();

        // Generar slots
        $current = $date->copy()->setHour($startHour)->setMinute(0);
        $end = $date->copy()->setHour($endHour)->setMinute(0);

        while ($current->lt($end)) {
            $timeStr = $current->format('H:i');

            // Si es hoy, no mostrar horas pasadas
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
        $this->step = 2; // Pasar a datos personales
    }

    public function back()
    {
        $this->step = 1;
        $this->calculateSlots();
    }

    // --- FUNCIÓN PARA AGENDAR OTRO FAMILIAR ---
    public function bookNewAppointmentForFamily()
    {
        // 1. Calcular el horario consecutivo ideal ANTES de borrar los datos
        $type = AppointmentType::find($this->type_id);
        $previousTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);
        $idealNextSlot = $previousTime->copy()->addMinutes($type->duration_minutes)->format('H:i');

        // 2. Limpiar datos del hijo (para cargar el nuevo)
        $this->child_name = '';
        $this->child_dob = '';
        $this->child_insurance_id = '';
        $this->child_affiliate = '';

        // 3. Recalcular disponibilidad REAL de la base de datos AHORA MISMO
        // (Esto verifica si alguien tomó el turno hace 1 segundo)
        $this->calculateSlots();

        // 4. LÓGICA DE CONTINUIDAD
        if (in_array($idealNextSlot, $this->availableSlots)) {
            // A) ¡ÉXITO! El turno consecutivo está libre.
            $this->selectedTime = $idealNextSlot; // Lo seleccionamos solo
            $this->step = 2; // Saltamos directo al formulario de datos (Vía Rápida)

            // Opcional: Mensaje para avisarle
            session()->flash('auto_msg', "Para tu comodidad, seleccionamos automáticamente el turno consecutivo de las {$idealNextSlot} hs.");
        } else {
            // B) MALA SUERTE. Alguien lo ocupó o es el fin del día.
            $this->selectedTime = null;
            $this->step = 1; // Volvemos al calendario para que elija otro
        }
    }

    public function confirmBooking()
    {
        $key = 'booking:' . request()->ip();

        // Si intentó más de 3 veces en 1 minuto...
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('generic', "Demasiados intentos. Por favor espera $seconds segundos.");
            return;
        }

        RateLimiter::hit($key, 60); // 60 segundos de memoria

        // 1. LIMPIEZA PREVIA (Sanitization)
        // Quitamos puntos, guiones y espacios del DNI y Teléfono antes de validar
        $this->parent_dni = preg_replace('/[^0-9]/', '', $this->parent_dni);
        $this->parent_phone = preg_replace('/[^0-9]/', '', $this->parent_phone);

        // 2. VALIDACIÓN (Ahora sí pasará si pusiste puntos)
        $this->validate([
            'parent_name' => 'required|string|min:3',
            'parent_dni' => 'required|numeric|digits_between:7,8',
            'parent_email' => 'required|email',
            'parent_phone' => 'required|numeric|min_digits:8', // min_digits es mejor que string
            'child_name' => 'required|string|min:3',
            'child_dob' => 'required|date|before:today',
        ]);

        // --- BLOQUEO DE SEGURIDAD (ANTI-DOBLE RESERVA) ---
        $checkDate = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);

        $isTaken = Appointment::where('start_time', $checkDate)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($isTaken) {
            // Ups, alguien ganó de mano.
            $this->addError('selectedTime', '¡Lo sentimos! Alguien acaba de reservar este horario hace un instante.');
            $this->step = 1; // Lo mandamos de vuelta al calendario
            $this->calculateSlots(); // Actualizamos la grilla
            return; // DETENEMOS TODO
        }

        // 3. LOGICA DE GUARDADO (IGUAL QUE ANTES)
        $user = User::where('email', $this->parent_email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $this->parent_name,
                'email' => $this->parent_email,
                'phone' => $this->parent_phone,
                'password' => Hash::make(Str::random(10)),
                'role' => 'patient',
            ]);
        } else {
            // Actualizamos teléfono si no tenía
            if (!$user->phone) $user->update(['phone' => $this->parent_phone]);
        }

        $patient = Patient::where('user_id', $user->id)
            ->where('name', $this->child_name)
            ->first();

        if (!$patient) {
            $patient = Patient::create([
                'user_id' => $user->id,
                'name' => $this->child_name,
                'birth_date' => $this->child_dob,
                'health_insurance_id' => $this->child_insurance_id ?: null,
                // Agregamos affiliate por si acaso, aunque esté vacío
                'affiliate_number' => $this->child_affiliate ?? null,
            ]);
        }

        $startTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);
        $type = AppointmentType::find($this->type_id);

        Appointment::create([
            'user_id' => $user->id,
            'patient_id' => $patient->id,
            'appointment_type_id' => $this->type_id,
            'start_time' => $startTime,
            'end_time' => $startTime->copy()->addMinutes($type->duration_minutes),
            'status' => 'confirmed',
            'patient_notes' => 'Turno reservado vía Web Pública',
            'created_by' => $user->id,
        ]);

        RateLimiter::clear($key);
        $this->step = 3;
    }

    public function render()
    {
        return view('livewire.guest-booking');
    }
}
