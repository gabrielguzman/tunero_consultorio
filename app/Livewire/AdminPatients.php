<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Patient;
use App\Models\PatientFile;
use App\Models\HealthInsurance;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

#[Layout('layouts.app')]
class AdminPatients extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $selectedPatient = null;
    
    // Datos cargados
    public $history = [];
    public $patientFiles = [];
    public $stats = []; // Para guardar métricas
    public $nextAppointment = null;
    
    // Inputs
    public $newFile;
    public $admin_notes; // CAMPO NUEVO (Memo)

    // Pestañas
    public $activeTab = 'overview'; // Iniciamos en 'Resumen'

    // Formulario Edición
    public $edit_name, $edit_dni, $edit_insurance_id, $edit_affiliate, $edit_alert;

    public function updatedSearch() { $this->resetPage(); }

    public function showHistory($patientId)
    {
        $this->selectedPatient = Patient::with(['user', 'healthInsurance'])->find($patientId);
        $this->activeTab = 'overview'; // Siempre abrir en Resumen
        $this->loadData();
    }

    public function loadData()
    {
        if (!$this->selectedPatient) return;

        // 1. Historial Completo
        $this->history = $this->selectedPatient->appointments()
            ->with('type')
            ->orderBy('start_time', 'desc')
            ->get();

        // 2. Archivos
        $this->patientFiles = $this->selectedPatient->files()->orderBy('created_at', 'desc')->get();

        // 3. Estadísticas (NUEVO)
        $total = $this->history->count();
        $cancelled = $this->history->where('status', 'cancelled')->count();
        $completed = $this->history->where('status', 'completed')->count();
        
        $this->stats = [
            'total' => $total,
            'cancelled' => $cancelled,
            'rate' => $total > 0 ? round(($completed / $total) * 100) : 0
        ];

        // 4. Próximo Turno (NUEVO)
        $this->nextAppointment = $this->selectedPatient->appointments()
            ->where('start_time', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time', 'asc')
            ->first();

        // 5. Cargar datos editables
        $this->admin_notes = $this->selectedPatient->admin_notes; // Memo
        $this->edit_name = $this->selectedPatient->name;
        $this->edit_dni = $this->selectedPatient->dni;
        $this->edit_insurance_id = $this->selectedPatient->health_insurance_id;
        $this->edit_affiliate = $this->selectedPatient->affiliate_number;
        $this->edit_alert = $this->selectedPatient->medical_alerts;
    }

    // Guardado automático del Memo cuando escribes (blur)
    public function saveNote()
    {
        if($this->selectedPatient){
            $this->selectedPatient->update(['admin_notes' => $this->admin_notes]);
            // No enviamos flash message para no molestar, es guardado silencioso
        }
    }

    public function setTab($tab) { $this->activeTab = $tab; }

    // ... (El resto de funciones updatePatient, deletePatient, uploadFile, deleteFile IGUAL QUE ANTES) ...
    // Solo copia las funciones del código anterior aquí abajo para no repetir todo el bloque gigante.
    
    // --- REPONER AQUÍ LAS FUNCIONES DE SUBIDA Y EDICIÓN DEL MENSAJE ANTERIOR ---
    public function updatePatient()
    {
        $this->validate([
            'edit_name' => 'required|string|min:3',
            'edit_dni' => 'nullable|string',
            'edit_insurance_id' => 'nullable|exists:health_insurances,id',
            'edit_alert' => 'nullable|string|max:255',
        ]);

        $this->selectedPatient->update([
            'name' => $this->edit_name,
            'dni' => $this->edit_dni,
            'health_insurance_id' => $this->edit_insurance_id,
            'affiliate_number' => $this->edit_affiliate,
            'medical_alerts' => $this->edit_alert,
        ]);

        session()->flash('message_edit', 'Datos actualizados correctamente.');
        $this->loadData(); 
    }

    public function deletePatient($id)
    {
        $patient = Patient::find($id);
        if ($patient) {
            $patient->delete();
            session()->flash('message_list', 'Paciente eliminado.');
        }
    }

    public function uploadFile()
    {
        $this->validate(['newFile' => 'required|file|max:10240']);
        $path = $this->newFile->store('patient_files', 'public');
        PatientFile::create([
            'patient_id' => $this->selectedPatient->id,
            'file_path' => $path,
            'original_name' => $this->newFile->getClientOriginalName(),
            'file_type' => $this->newFile->extension(),
        ]);
        $this->newFile = null;
        session()->flash('message_file', 'Archivo subido.');
        $this->loadData();
    }

    public function deleteFile($fileId)
    {
        $file = PatientFile::find($fileId);
        if ($file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
            $this->loadData();
        }
    }

    public function closeHistory()
    {
        $this->selectedPatient = null;
        $this->history = [];
        $this->patientFiles = [];
        $this->reset(['edit_name', 'edit_dni', 'edit_insurance_id', 'edit_affiliate', 'edit_alert', 'admin_notes']);
    }

    public function render()
    {
        $patients = Patient::query()
            ->with(['healthInsurance', 'user'])
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('dni', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin-patients', [
            'patients' => $patients,
            'insurances' => HealthInsurance::orderBy('name')->get()
        ]);
    }
}