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

    // Estado del Modal
    public $isModalOpen = false;
    public $isEditing = false;
    public $activeTab = 'basic'; // Controla la pestaña activa: basic, health, birth, admin

    // --- CAMPOS DE LA BASE DE DATOS ---
    public $patient_id;

    // 1. Datos Básicos
    public $name, $dni, $birth_date, $sex, $place_of_birth;

    // 2. Cobertura
    public $health_insurance_id, $affiliate_number;

    // 3. Salud y Físico
    public $blood_type, $height_cm, $current_weight, $vaccination_complete = false;
    public $medical_alerts, $allergies, $background_diseases, $current_medication;

    // 4. Perinatales (Nacimiento)
    public $pregnancy_type, $birth_type, $gestational_age, $birth_weight;

    // 5. Administrativos
    public $clinical_history_number, $discharge_date, $is_active = true, $observations;

    // PROPIEDAD COMPUTADA: Edad
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

            // 1. Básicos
            $this->name = $p->name;
            $this->dni = $p->dni;
            $this->birth_date = $p->birth_date ? $p->birth_date->format('Y-m-d') : null;
            $this->sex = $p->sex;
            $this->place_of_birth = $p->place_of_birth;

            // 2. Cobertura
            $this->health_insurance_id = $p->health_insurance_id;
            $this->affiliate_number = $p->affiliate_number;

            // 3. Salud
            $this->blood_type = $p->blood_type;
            $this->height_cm = $p->height_cm;
            $this->current_weight = $p->current_weight;
            $this->vaccination_complete = (bool)$p->vaccination_complete;
            $this->medical_alerts = $p->medical_alerts;
            $this->allergies = $p->allergies;
            $this->background_diseases = $p->background_diseases;
            $this->current_medication = $p->current_medication;

            // 4. Perinatales
            $this->pregnancy_type = $p->pregnancy_type;
            $this->birth_type = $p->birth_type;
            $this->gestational_age = $p->gestational_age;
            $this->birth_weight = $p->birth_weight;

            // 5. Admin
            $this->clinical_history_number = $p->clinical_history_number;
            $this->discharge_date = $p->discharge_date ? $p->discharge_date->format('Y-m-d') : null;
            $this->is_active = (bool)$p->is_active;
            $this->observations = $p->observations;

            $this->isModalOpen = true;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'dni' => 'nullable|numeric|digits_between:7,8',
            'birth_date' => 'required|date|before:today',
            'health_insurance_id' => 'nullable|exists:health_insurances,id',
            'current_weight' => 'nullable|numeric|min:0|max:200',
            'height_cm' => 'nullable|integer|min:0|max:250',
            'birth_weight' => 'nullable|numeric|min:0|max:10',
        ]);

        Patient::updateOrCreate(
            ['id' => $this->patient_id],
            [
                'user_id' => Auth::id(),
                'name' => $this->name,
                'dni' => $this->dni,
                'birth_date' => $this->birth_date,
                'sex' => $this->sex,
                'place_of_birth' => $this->place_of_birth,

                'health_insurance_id' => $this->health_insurance_id ?: null,
                'affiliate_number' => $this->affiliate_number,

                'blood_type' => $this->blood_type,
                'height_cm' => $this->height_cm,
                'current_weight' => $this->current_weight,
                'vaccination_complete' => $this->vaccination_complete,

                'medical_alerts' => $this->medical_alerts,
                'allergies' => $this->allergies,
                'background_diseases' => $this->background_diseases,
                'current_medication' => $this->current_medication,

                'pregnancy_type' => $this->pregnancy_type,
                'birth_type' => $this->birth_type,
                'gestational_age' => $this->gestational_age,
                'birth_weight' => $this->birth_weight,

                'clinical_history_number' => $this->clinical_history_number,
                'discharge_date' => $this->discharge_date ?: null,
                'is_active' => $this->is_active,
                'observations' => $this->observations,
            ]
        );

        session()->flash('message', $this->isEditing ? 'Datos actualizados correctamente.' : '¡Paciente registrado con éxito!');
        $this->closeModal();
        $this->loadPatients();
    }

    public function delete($id)
    {
        $p = Patient::where('user_id', Auth::id())->find($id);
        if ($p) {
            $p->delete();
            session()->flash('message', 'Paciente eliminado correctamente.');
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
        $this->reset([
            'patient_id', 'name', 'dni', 'birth_date', 'sex', 'place_of_birth',
            'health_insurance_id', 'affiliate_number',
            'blood_type', 'height_cm', 'current_weight', 'vaccination_complete',
            'medical_alerts', 'allergies', 'background_diseases', 'current_medication',
            'pregnancy_type', 'birth_type', 'gestational_age', 'birth_weight',
            'clinical_history_number', 'discharge_date', 'is_active', 'observations'
        ]);
        $this->activeTab = 'basic';
        $this->is_active = true;
        $this->vaccination_complete = false;
    }

    // Cambiar Pestaña
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }
}