<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class AdminStats extends Component
{
    public $month;
    public $year;

    public function mount()
    {
        // Por defecto, el mes actual
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
    }

    public function render()
    {
        // Filtro de fechas
        $start = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // 1. Turnos Totales en el periodo
        $totalAppointments = Appointment::whereBetween('start_time', [$start, $end])
            ->where('status', '!=', 'cancelled') // Ignoramos cancelados
            ->count();

        // 2. Ingresos Estimados (Sumamos el precio del TIPO de turno de los turnos completados)
        // Nota: Esto asume que el precio es el actual. 
        // En un sistema avanzado, deberías guardar el precio histórico en la tabla appointments.
        $income = Appointment::whereBetween('start_time', [$start, $end])
            ->where('status', 'completed') // Solo lo facturado/realizado
            ->join('appointment_types', 'appointments.appointment_type_id', '=', 'appointment_types.id')
            ->sum('appointment_types.price');

        // 3. Pacientes por Obra Social (Gráfico de torta textual)
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
            ->get();

        return view('livewire.admin-stats', [
            'totalAppointments' => $totalAppointments,
            'income' => $income,
            'byInsurance' => $byInsurance
        ]);
    }
}