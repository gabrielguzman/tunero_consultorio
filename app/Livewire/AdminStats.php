<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AdminStats extends Component
{
    public $month;
    public $year;

    public function mount()
    {
        // Iniciamos con la fecha actual
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
    }

    public function render()
    {
        // Definimos el rango de fechas según el filtro seleccionado
        $start = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // 1. FACTURACIÓN REAL (Solo turnos 'completed')
        // Usamos join para sumar el precio desde el tipo de turno
        $income = Appointment::whereBetween('start_time', [$start, $end])
            ->where('status', 'completed')
            ->join('appointment_types', 'appointments.appointment_type_id', '=', 'appointment_types.id')
            ->sum('appointment_types.price');

        // 2. CANTIDAD DE TURNOS (Totales agendados, excluyendo cancelados)
        $totalAppointments = Appointment::whereBetween('start_time', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->count();

        // 3. PACIENTES NUEVOS (Registrados en este periodo)
        $newPatients = Patient::whereBetween('created_at', [$start, $end])->count();

        // 4. TURNOS DE HOY (Dato operativo fijo, no depende del filtro mes/año)
        $todayAppointments = Appointment::whereDate('start_time', Carbon::today())
            ->where('status', '!=', 'cancelled')
            ->count();

        // 5. GRÁFICO: PACIENTES POR OBRA SOCIAL
        $byInsurance = Appointment::whereBetween('start_time', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->leftJoin('health_insurances', 'patients.health_insurance_id', '=', 'health_insurances.id')
            ->select(
                DB::raw('IFNULL(health_insurances.name, "Particular") as name'), 
                DB::raw('count(*) as total')
            )
            ->groupBy('health_insurances.name')
            ->orderByDesc('total')
            ->take(5) // Solo el top 5
            ->get();

        // 6. GRÁFICO: SERVICIOS MÁS VENDIDOS (Top Treatments)
        $topServices = Appointment::whereBetween('start_time', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->join('appointment_types', 'appointments.appointment_type_id', '=', 'appointment_types.id')
            ->select('appointment_types.name', DB::raw('count(*) as total'))
            ->groupBy('appointment_types.name')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('livewire.admin-stats', [
            'income' => $income,
            'totalAppointments' => $totalAppointments,
            'newPatients' => $newPatients,
            'todayAppointments' => $todayAppointments,
            'byInsurance' => $byInsurance,
            'topServices' => $topServices
        ]);
    }
}