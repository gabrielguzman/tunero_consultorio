<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AdminFinance extends Component
{
    use WithPagination;

    public $date_filter; // Para filtrar por día
    public $month_filter; // Para ver el mes entero (opcional, por ahora usaremos día)
    
    // Variables para el Modal de Cobro
    public $selectedAppointment = null;
    public $amount;
    public $method = 'efectivo';
    public $showModal = false;

    public function mount()
    {
        $this->date_filter = Carbon::today()->format('Y-m-d');
    }

    // --- ABRIR MODAL PARA COBRAR ---
    public function openPaymentModal($appointmentId)
    {
        $this->selectedAppointment = Appointment::with('type')->find($appointmentId);
        
        // Si ya tiene pago, lo cargamos. Si no, sugerimos el precio del tipo de turno.
        $this->amount = $this->selectedAppointment->paid_amount ?? $this->selectedAppointment->type->price;
        $this->method = $this->selectedAppointment->payment_method ?? 'efectivo';
        
        $this->showModal = true;
    }

    // --- GUARDAR PAGO ---
    public function savePayment()
    {
        $this->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string'
        ]);

        if ($this->selectedAppointment) {
            $this->selectedAppointment->update([
                'paid_amount' => $this->amount,
                'payment_method' => $this->method,
                'payment_date' => now(),
                'status' => 'completed' // Al cobrar, lo marcamos como completado automáticamente
            ]);

            $this->showModal = false;
            $this->reset(['selectedAppointment', 'amount']);
            session()->flash('message', '¡Pago registrado correctamente!');
        }
    }

    public function render()
    {
        // 1. Buscamos los turnos de la fecha seleccionada que NO estén cancelados
        $appointments = Appointment::with(['patient', 'type'])
            ->whereDate('start_time', $this->date_filter)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();

        // 2. Calculamos Totales (Caja Diaria)
        $totalCash = $appointments->where('payment_method', 'efectivo')->sum('paid_amount');
        $totalTransfer = $appointments->where('payment_method', 'transferencia')->sum('paid_amount');
        $totalInsurance = $appointments->where('payment_method', 'obra_social')->sum('paid_amount');
        $total = $appointments->sum('paid_amount');

        return view('livewire.admin-finance', [
            'appointments' => $appointments,
            'summary' => [
                'cash' => $totalCash,
                'transfer' => $totalTransfer,
                'insurance' => $totalInsurance,
                'total' => $total
            ]
        ]);
    }
}