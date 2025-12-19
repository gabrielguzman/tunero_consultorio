<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Patient;
use App\Models\HealthInsurance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('layouts.app')]
class PatientManager extends Component
{
    public $patients;
    public $insurances;

    public $isModalOpen = false;
    public $isEditing = false;

    // Campos
    public $patient_id;
    public $name;
    public $dni; // <--- NUEVO
    public $birth_date;
    public $health_insurance_id;
    public $affiliate_number;
    public $medical_alerts;

    // PROPIEDAD COMPUTADA: Calcula la edad en vivo para mostrar en el form
    public function getCalculatedAgeProperty()
    {
        if (!$this->birth_date) return '';
        try {
            return Carbon::parse($this->birth_date)->age . ' años';
        } catch (\Exception $e) {
            return '';
        }
    }

    public function mount()
    {
        $this->insurances = HealthInsurance::orderBy('name')->get();
        $this->loadPatients();
    }

    public function loadPatients()
    {
        $this->patients = Patient::where('user_id', Auth::id())
            ->with('healthInsurance')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.patient-manager');
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->isEditing = true;
        
        $p = Patient::where('user_id', Auth::id())->find($id);
        
        if ($p) {
            $this->patient_id = $p->id;
            $this->name = $p->name;
            $this->dni = $p->dni; // <--- CARGAR DNI
            $this->birth_date = $p->birth_date;
            $this->health_insurance_id = $p->health_insurance_id;
            $this->affiliate_number = $p->affiliate_number;
            $this->medical_alerts = $p->medical_alerts;
            
            $this->isModalOpen = true;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'dni' => 'nullable|numeric|digits_between:7,8', // <--- VALIDACIÓN DNI
            'birth_date' => 'required|date|before:today',
            'health_insurance_id' => 'nullable|exists:health_insurances,id',
            'affiliate_number' => 'nullable|string|max:50',
            'medical_alerts' => 'nullable|string|max:255',
        ]);

        Patient::updateOrCreate(
            ['id' => $this->patient_id],
            [
                'user_id' => Auth::id(),
                'name' => $this->name,
                'dni' => $this->dni, // <--- GUARDAR DNI
                'birth_date' => $this->birth_date,
                'health_insurance_id' => $this->health_insurance_id ?: null,
                'affiliate_number' => $this->affiliate_number,
                'medical_alerts' => $this->medical_alerts
            ]
        );

        session()->flash('message', $this->isEditing ? 'Datos actualizados.' : '¡Hijo/a registrado!');
        $this->closeModal();
        $this->loadPatients();
    }

    public function delete($id)
    {
        $p = Patient::where('user_id', Auth::id())->find($id);
        if ($p) {
            $p->delete();
            session()->flash('message', 'Eliminado correctamente.');
            $this->loadPatients();
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['name', 'dni', 'birth_date', 'health_insurance_id', 'affiliate_number', 'medical_alerts', 'patient_id']);
    }
}