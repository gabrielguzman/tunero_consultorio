<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use Carbon\Carbon;

#[Layout('layouts.empty')] // Usaremos un layout vacío (sin menús ni barras)
class WaitingRoomTv extends Component
{
    public $lastCall = null;
    public $upcomingList = [];

    // Esta función se ejecuta automáticamente cada 3 segundos
    public function refreshData()
    {
        // 1. Buscamos si alguien fue llamado en los últimos 15 segundos
        // (El tiempo que dura la alerta en pantalla)
        $this->lastCall = Appointment::with('patient')
            ->where('status', 'confirmed') // O 'waiting' si usas ese estado
            ->whereNotNull('called_at')
            ->where('called_at', '>=', Carbon::now()->subSeconds(15))
            ->orderBy('called_at', 'desc')
            ->first();

        // 2. Buscamos la lista de los próximos 5 pacientes esperando
       $this->upcomingList = Appointment::with('patient')
        ->whereDate('start_time', Carbon::today()) // Solo turnos de HOY
        // ->where('start_time', '>=', Carbon::now())  <--- COMENTA O BORRA ESTA LÍNEA PARA PROBAR
        ->where('status', 'confirmed')
        ->orderBy('start_time', 'asc')
        ->take(5)
        ->get();
            
        // Si hay una llamada nueva, emitimos evento al navegador para el sonido
        if ($this->lastCall) {
            $this->dispatch('play-sound');
        }
    }

    public function render()
    {
        return view('livewire.waiting-room-tv');
    }
}