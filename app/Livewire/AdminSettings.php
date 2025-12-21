<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\WorkSchedule;
use App\Models\BlockedDay;
use App\Models\BusinessSetting; // Importante: Agregamos este modelo
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class AdminSettings extends Component
{
    // --- 1. PROPIEDADES DE IDENTIDAD (Del nuevo código) ---
    public $business_name;
    public $contact_phone;
    public $contact_email;

    // --- 2. PROPIEDADES DE HORARIOS (Del código avanzado actual) ---
    public $scheduleData = []; 
    
    // --- 3. PROPIEDADES DE BLOQUEOS (Del código avanzado actual) ---
    public $blockedDays;
    public $blockDate;
    public $blockReason;

    public function mount()
    {
        // Cargamos toda la información al iniciar
        $this->loadBusinessSettings();
        $this->loadScheduleAndBlocks();
    }

    // --- PARTE A: CARGAR DATOS GENERALES ---
    public function loadBusinessSettings()
    {
        $settings = BusinessSetting::first();
        
        if ($settings) {
            $this->business_name = $settings->business_name;
            $this->contact_phone = $settings->contact_phone;
            $this->contact_email = $settings->contact_email;
        } else {
            // Valores por defecto si no existe
            $this->business_name = 'Consultorio Médico';
        }
    }

    // --- PARTE B: CARGAR HORARIOS Y BLOQUEOS ---
    public function loadScheduleAndBlocks()
    {
        // 1. Cargar Días Bloqueados (Feriados/Vacaciones)
        $this->blockedDays = BlockedDay::where('date', '>=', now())
                                       ->orderBy('date')
                                       ->get();

        // 2. Cargar Horarios en el array temporal
        $dbSchedules = WorkSchedule::all();

        // Iteramos del 1 (Lunes) al 6 (Sábado) - O al 0 (Domingo) si quieres
        foreach(range(1, 6) as $dayNum) {
            $record = $dbSchedules->firstWhere('day_of_week', $dayNum);
            
            $this->scheduleData[$dayNum] = [
                'active' => $record ? true : false,
                'start'  => $record ? \Carbon\Carbon::parse($record->start_time)->format('H:i') : '09:00',
                'end'    => $record ? \Carbon\Carbon::parse($record->end_time)->format('H:i') : '17:00',
                'is_dirty' => false, 
            ];
        }
    }

    // --- GUARDAR IDENTIDAD DEL NEGOCIO ---
    public function saveBusinessSettings()
    {
        $this->validate([
            'business_name' => 'required|string|max:255',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
        ]);

        // Buscamos o creamos el primer registro
        $settings = BusinessSetting::firstOrNew();

        $settings->business_name = $this->business_name;
        $settings->contact_phone = $this->contact_phone;
        $settings->contact_email = $this->contact_email;
        
        // NOTA: No guardamos start_hour/end_hour aquí porque usamos WorkSchedule
        $settings->save();

        session()->flash('message_settings', '¡Datos del consultorio actualizados!');
    }

    // --- LÓGICA DE HORARIOS (Mantuvimos tu lógica avanzada) ---
    public function updatedScheduleData($value, $key)
    {
        $parts = explode('.', $key);
        $dayNum = $parts[0];
        $this->scheduleData[$dayNum]['is_dirty'] = true;
    }

    public function saveDay($dayNum)
    {
        $data = $this->scheduleData[$dayNum];

        // Validar lógica básica
        if($data['active'] && $data['end'] <= $data['start']) {
            $this->addError("scheduleData.$dayNum.end", "La hora fin debe ser mayor a la de inicio.");
            return;
        }

        if ($data['active']) {
            WorkSchedule::updateOrCreate(
                ['day_of_week' => $dayNum],
                [
                    'start_time' => $data['start'],
                    'end_time' => $data['end']
                ]
            );
        } else {
            WorkSchedule::where('day_of_week', $dayNum)->delete();
        }

        $this->scheduleData[$dayNum]['is_dirty'] = false;
        session()->flash('message_' . $dayNum, '¡Horario guardado!');
    }

    public function resetDay($dayNum)
    {
        $record = WorkSchedule::where('day_of_week', $dayNum)->first();
        
        $this->scheduleData[$dayNum] = [
            'active' => $record ? true : false,
            'start'  => $record ? \Carbon\Carbon::parse($record->start_time)->format('H:i') : '09:00',
            'end'    => $record ? \Carbon\Carbon::parse($record->end_time)->format('H:i') : '17:00',
            'is_dirty' => false,
        ];
    }

    // --- LÓGICA DE BLOQUEOS (Mantuvimos tu lógica original) ---
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
        $this->loadScheduleAndBlocks(); 
    }

    public function unblockDate($id)
    {
        BlockedDay::destroy($id);
        $this->loadScheduleAndBlocks();
    }
}