<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\WorkSchedule;
use App\Models\BlockedDay;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('layouts.app')]
class AdminSettings extends Component
{
    // --- 1. PROPIEDADES DEL FORMULARIO ---
    public $business_name = '';
    public $contact_phone = '';
    public $contact_email = '';
    public $max_turnos_por_dia = 0;
    public $allow_overbooking = false;

    // --- 2. HORARIOS Y BLOQUEOS ---
    public $scheduleData = []; 
    public $blockedDays;
    public $blockDate;
    public $blockReason;

    public function mount()
    {
        // Cargamos los datos apenas entra a la página
        $this->loadBusinessSettings();
        $this->loadScheduleAndBlocks();
    }

    // --- A. CARGAR DATOS GENERALES ---
    public function loadBusinessSettings()
    {
        // Usamos first() para traer la configuración, sin importar el ID
        $settings = BusinessSetting::first();
        
        if ($settings) {
            $this->business_name = $settings->business_name;
            $this->contact_phone = $settings->contact_phone;
            $this->contact_email = $settings->contact_email;
            // Usamos operador ternario para evitar errores si el campo es null
            $this->max_turnos_por_dia = $settings->max_turnos_por_dia ?? 0;
            $this->allow_overbooking = (bool) $settings->allow_overbooking;
        } else {
            // Valores por defecto si la base de datos está vacía
            $this->business_name = 'Consultorio Médico';
        }
    }

    // --- B. CARGAR HORARIOS Y BLOQUEOS ---
    public function loadScheduleAndBlocks()
    {
        $this->blockedDays = BlockedDay::where('date', '>=', now())->orderBy('date')->get();
        $dbSchedules = WorkSchedule::all();

        foreach(range(1, 6) as $dayNum) {
            $record = $dbSchedules->firstWhere('day_of_week', $dayNum);
            // Formateamos las horas con seguridad
            $start = $record ? Carbon::parse($record->start_time)->format('H:i') : '09:00';
            $end = $record ? Carbon::parse($record->end_time)->format('H:i') : '17:00';

            $this->scheduleData[$dayNum] = [
                'active' => $record ? true : false,
                'start'  => $start,
                'end'    => $end,
                'is_dirty' => false, 
            ];
        }
    }

    // --- C. GUARDAR IDENTIDAD (Aquí estaba el problema) ---
    public function saveBusinessSettings()
    {
        $this->validate([
            'business_name' => 'required|string|max:255',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'max_turnos_por_dia' => 'nullable|integer|min:0',
            'allow_overbooking' => 'boolean'
        ]);

        // Buscamos el primero, si no existe, creamos una instancia nueva
        $settings = BusinessSetting::firstOrNew([]);

        $settings->business_name = $this->business_name;
        $settings->contact_phone = $this->contact_phone;
        $settings->contact_email = $this->contact_email;
        $settings->max_turnos_por_dia = $this->max_turnos_por_dia ?? 0;
        $settings->allow_overbooking = $this->allow_overbooking ? 1 : 0;
        
        $settings->save();

        // Recargamos las variables para confirmar visualmente
        $this->loadBusinessSettings();

        session()->flash('message_settings', '¡Configuración guardada correctamente!');
    }

    // --- D. LÓGICA DE HORARIOS (Sin cambios, funcionaba bien) ---
    public function updatedScheduleData($value, $key)
    {
        $parts = explode('.', $key);
        if(isset($parts[0]) && isset($this->scheduleData[$parts[0]])) {
            $this->scheduleData[$parts[0]]['is_dirty'] = true;
        }
    }

    public function saveDay($dayNum)
    {
        $data = $this->scheduleData[$dayNum];
        
        if($data['active'] && $data['end'] <= $data['start']) {
            $this->addError("scheduleData.$dayNum.end", "Hora fin incorrecta.");
            return;
        }

        if ($data['active']) {
            WorkSchedule::updateOrCreate(
                ['day_of_week' => $dayNum],
                ['start_time' => $data['start'], 'end_time' => $data['end']]
            );
        } else {
            WorkSchedule::where('day_of_week', $dayNum)->delete();
        }
        
        $this->scheduleData[$dayNum]['is_dirty'] = false;
        session()->flash('message_' . $dayNum, 'Horario guardado.');
    }

    public function resetDay($dayNum)
    {
        $record = WorkSchedule::where('day_of_week', $dayNum)->first();
        $this->scheduleData[$dayNum] = [
            'active' => $record ? true : false,
            'start'  => $record ? Carbon::parse($record->start_time)->format('H:i') : '09:00',
            'end'    => $record ? Carbon::parse($record->end_time)->format('H:i') : '17:00',
            'is_dirty' => false,
        ];
    }

    // --- E. LÓGICA DE BLOQUEOS ---
    public function blockNewDate()
    {
        $this->validate(['blockDate' => 'required|date|after:today', 'blockReason' => 'required|string']);
        
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