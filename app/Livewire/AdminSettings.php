<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\WorkSchedule;
use App\Models\BlockedDay;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class AdminSettings extends Component
{
    // Array temporal para editar los inputs sin guardar en BD todavía
    public $scheduleData = []; 
    
    // Para bloqueos
    public $blockedDays;
    public $blockDate;
    public $blockReason;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // 1. Cargar Días Bloqueados
        $this->blockedDays = BlockedDay::where('date', '>=', now())
                                       ->orderBy('date')
                                       ->get();

        // 2. Cargar Horarios en el array temporal
        // Estructura: [ 1 => ['start'=>'09:00', 'end'=>'17:00', 'active'=>true], ... ]
        
        $dbSchedules = WorkSchedule::all();

        foreach(range(1, 6) as $dayNum) {
            $record = $dbSchedules->firstWhere('day_of_week', $dayNum);
            
            $this->scheduleData[$dayNum] = [
                'active' => $record ? true : false,
                'start'  => $record ? \Carbon\Carbon::parse($record->start_time)->format('H:i') : '09:00',
                'end'    => $record ? \Carbon\Carbon::parse($record->end_time)->format('H:i') : '17:00',
                'is_dirty' => false, // Para saber si se modificó algo
            ];
        }
    }

    // Se ejecuta cuando modificas cualquier input
    public function updatedScheduleData($value, $key)
    {
        // Extraemos el día modificado del key (ej: "1.start")
        $parts = explode('.', $key);
        $dayNum = $parts[0];
        
        // Marcamos que ese día tiene cambios sin guardar
        $this->scheduleData[$dayNum]['is_dirty'] = true;
    }

    // Botón GUARDAR (Fila individual)
    public function saveDay($dayNum)
    {
        $data = $this->scheduleData[$dayNum];

        if ($data['active']) {
            // Guardar o Actualizar
            WorkSchedule::updateOrCreate(
                ['day_of_week' => $dayNum],
                [
                    'start_time' => $data['start'],
                    'end_time' => $data['end']
                ]
            );
        } else {
            // Si desmarcó el checkbox, borramos el horario
            WorkSchedule::where('day_of_week', $dayNum)->delete();
        }

        // Quitamos la marca de "sucio" (modificado)
        $this->scheduleData[$dayNum]['is_dirty'] = false;
        
        session()->flash('message_' . $dayNum, '¡Guardado!');
    }

    // Botón CANCELAR (Deshacer cambios)
    public function resetDay($dayNum)
    {
        // Recargamos solo ese día desde la BD
        $record = WorkSchedule::where('day_of_week', $dayNum)->first();
        
        $this->scheduleData[$dayNum] = [
            'active' => $record ? true : false,
            'start'  => $record ? \Carbon\Carbon::parse($record->start_time)->format('H:i') : '09:00',
            'end'    => $record ? \Carbon\Carbon::parse($record->end_time)->format('H:i') : '17:00',
            'is_dirty' => false,
        ];
    }

    // --- LÓGICA DE BLOQUEOS (IGUAL QUE ANTES) ---
    public function blockNewDate()
    {
        $this->validate([
            'blockDate' => 'required|date|after:today',
            'blockReason' => 'required|string|max:50'
        ]);

        BlockedDay::create([
            'date' => $this->blockDate,
            'reason' => $this->blockReason,
            'full_day' => true,
            'created_by' => Auth::id()
        ]);

        $this->reset(['blockDate', 'blockReason']);
        $this->loadData(); // Recargar lista
    }

    public function unblockDate($id)
    {
        BlockedDay::destroy($id);
        $this->loadData();
    }
}