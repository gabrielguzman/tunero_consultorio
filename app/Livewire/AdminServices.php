<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AppointmentType;
use App\Models\HealthInsurance; // <--- FALTABA ESTO
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AdminServices extends Component
{
    // --- VARIABLES PARA SERVICIOS (Tipos de Turno) ---
    public $type_id, $type_name, $type_price, $type_duration, $type_color;
    public $modalTypeOpen = false;

    // --- VARIABLES PARA OBRAS SOCIALES ---
    public $insurance_id, $insurance_name;
    public $modalInsuranceOpen = false;

    // --- RENDERIZADO (Aquí enviamos ambas listas a la vista) ---
    public function render()
    {
        return view('livewire.admin-services', [
            'types' => AppointmentType::orderBy('name')->get(),
            'insurances' => HealthInsurance::orderBy('name')->get(), // <--- ESTO FALTABA
        ]);
    }

    // ==========================================
    // LÓGICA DE SERVICIOS (Tipos de Turno)
    // ==========================================

    public function createType()
    {
        $this->resetTypeFields();
        $this->modalTypeOpen = true;
    }

    public function editType($id)
    {
        $type = AppointmentType::findOrFail($id);
        $this->type_id = $id;
        $this->type_name = $type->name;
        $this->type_price = $type->price;
        $this->type_duration = $type->duration_minutes;
        $this->type_color = $type->color;
        $this->modalTypeOpen = true;
    }

    public function saveType()
    {
        $this->validate([
            'type_name' => 'required|min:3',
            'type_price' => 'required|numeric',
            'type_duration' => 'required|integer|min:5',
            'type_color' => 'required',
        ]);

        AppointmentType::updateOrCreate(['id' => $this->type_id], [
            'name' => $this->type_name,
            'price' => $this->type_price,
            'duration_minutes' => $this->type_duration,
            'color' => $this->type_color,
            'active' => $this->type_id ? AppointmentType::find($this->type_id)->active : true
        ]);

        $this->closeModals();
        session()->flash('message', 'Servicio guardado correctamente.');
    }

    public function toggleStatus($id)
    {
        $type = AppointmentType::find($id);
        if ($type) {
            $type->active = !$type->active;
            $type->save();
        }
    }

    public function deleteType($id)
    {
        try {
            AppointmentType::find($id)?->delete();
            session()->flash('message', 'Servicio eliminado.');
        } catch (\Exception $e) {
            session()->flash('message', 'No se puede eliminar porque tiene turnos asociados. Úsalo como inactivo.');
        }
    }

    public function resetTypeFields()
    {
        $this->type_id = null;
        $this->type_name = '';
        $this->type_price = '';
        $this->type_duration = 30;
        $this->type_color = '#3B82F6';
    }

    // ==========================================
    // LÓGICA DE OBRAS SOCIALES
    // ==========================================

    public function createInsurance()
    {
        $this->resetInsuranceFields();
        $this->modalInsuranceOpen = true;
    }

    public function editInsurance($id)
    {
        $ins = HealthInsurance::findOrFail($id);
        $this->insurance_id = $id;
        $this->insurance_name = $ins->name;
        $this->modalInsuranceOpen = true;
    }

    public function saveInsurance()
    {
        $this->validate([
            'insurance_name' => 'required|min:2|unique:health_insurances,name,' . $this->insurance_id,
        ]);

        HealthInsurance::updateOrCreate(['id' => $this->insurance_id], [
            'name' => $this->insurance_name
        ]);

        $this->closeModals();
        session()->flash('message', 'Obra Social guardada.');
    }

    public function deleteInsurance($id)
    {
        try {
            HealthInsurance::find($id)?->delete();
            session()->flash('message', 'Obra Social eliminada.');
        } catch (\Exception $e) {
            session()->flash('message', 'No se puede eliminar porque hay pacientes con esta Obra Social.');
        }
    }

    public function resetInsuranceFields()
    {
        $this->insurance_id = null;
        $this->insurance_name = '';
    }

    // ==========================================
    // UTILIDADES
    // ==========================================

    public function closeModals()
    {
        $this->modalTypeOpen = false;
        $this->modalInsuranceOpen = false;
        $this->resetTypeFields();
        $this->resetInsuranceFields();
    }
}