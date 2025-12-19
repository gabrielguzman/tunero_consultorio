<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\AppointmentType;
use App\Models\HealthInsurance;

#[Layout('layouts.app')]
class AdminServices extends Component
{
    // Colecciones
    public $types;
    public $insurances;

    // --- FORMULARIO SERVICIOS (Tipos de Turno) ---
    public $type_id;
    public $type_name;
    public $type_duration = 30; // Default
    public $type_price = 0;
    public $type_color = '#3B82F6'; // Azul por defecto

    // --- FORMULARIO OBRAS SOCIALES ---
    public $insurance_id;
    public $insurance_name;

    // Modales
    public $modalTypeOpen = false;
    public $modalInsuranceOpen = false;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->types = AppointmentType::orderBy('name')->get();
        $this->insurances = HealthInsurance::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.admin-services');
    }

    // ==========================================
    // LÓGICA DE TIPOS DE TURNO (SERVICIOS)
    // ==========================================

    public function createType()
    {
        $this->resetTypeForm();
        $this->modalTypeOpen = true;
    }

    public function editType($id)
    {
        $type = AppointmentType::find($id);
        $this->type_id = $type->id;
        $this->type_name = $type->name;
        $this->type_duration = $type->duration_minutes;
        $this->type_price = $type->price;
        $this->type_color = $type->color;
        $this->modalTypeOpen = true;
    }

    public function saveType()
    {
        $this->validate([
            'type_name' => 'required|min:3',
            'type_duration' => 'required|integer|min:5|max:120',
            'type_price' => 'required|numeric|min:0',
            'type_color' => 'required',
        ]);

        AppointmentType::updateOrCreate(
            ['id' => $this->type_id],
            [
                'name' => $this->type_name,
                'duration_minutes' => $this->type_duration,
                'price' => $this->type_price,
                'color' => $this->type_color,
                'active' => true
            ]
        );

        session()->flash('message', 'Servicio guardado correctamente.');
        $this->closeModals();
        $this->loadData();
    }

    public function deleteType($id)
    {
        // Opcional: Verificar si tiene turnos asociados antes de borrar
        AppointmentType::destroy($id);
        $this->loadData();
    }

    // ==========================================
    // LÓGICA DE OBRAS SOCIALES
    // ==========================================

    public function createInsurance()
    {
        $this->resetInsuranceForm();
        $this->modalInsuranceOpen = true;
    }

    public function editInsurance($id)
    {
        $ins = HealthInsurance::find($id);
        $this->insurance_id = $ins->id;
        $this->insurance_name = $ins->name;
        $this->modalInsuranceOpen = true;
    }

    public function saveInsurance()
    {
        $this->validate(['insurance_name' => 'required|min:2']);

        HealthInsurance::updateOrCreate(
            ['id' => $this->insurance_id],
            ['name' => $this->insurance_name]
        );

        session()->flash('message', 'Obra Social guardada.');
        $this->closeModals();
        $this->loadData();
    }

    public function deleteInsurance($id)
    {
        HealthInsurance::destroy($id);
        $this->loadData();
    }

    // ==========================================
    // UTILIDADES
    // ==========================================

    public function closeModals()
    {
        $this->modalTypeOpen = false;
        $this->modalInsuranceOpen = false;
    }

    public function resetTypeForm()
    {
        $this->reset(['type_id', 'type_name', 'type_duration', 'type_price', 'type_color']);
        $this->type_color = '#3B82F6';
    }

    public function resetInsuranceForm()
    {
        $this->reset(['insurance_id', 'insurance_name']);
    }
}