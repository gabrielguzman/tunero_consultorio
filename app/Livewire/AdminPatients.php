<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // <--- IMPORTANTE
use App\Models\Patient;
use App\Models\PatientFile;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class AdminPatients extends Component
{
    use WithPagination;
    use WithFileUploads; // <--- IMPORTANTE

    public $search = '';
    public $selectedPatient = null;
    public $history = [];
    public $patientFiles = []; // Lista de archivos

    // Variable para el archivo nuevo
    public $newFile; 

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function showHistory($patientId)
    {
        $this->selectedPatient = Patient::with(['user', 'healthInsurance'])->find($patientId);
        
        // 1. Cargar Turnos
        $this->history = $this->selectedPatient->appointments()
            ->with('type')
            ->orderBy('start_time', 'desc')
            ->get();

        // 2. Cargar Archivos
        $this->loadFiles();
    }

    public function loadFiles()
    {
        if ($this->selectedPatient) {
            $this->patientFiles = $this->selectedPatient->files()->orderBy('created_at', 'desc')->get();
        }
    }

    // --- FUNCIÓN PARA SUBIR ARCHIVO ---
    public function uploadFile()
    {
        $this->validate([
            'newFile' => 'required|file|max:10240', // Máx 10MB
        ]);

        // Guardar en disco 'public' dentro de la carpeta 'patient_files'
        $path = $this->newFile->store('patient_files', 'public');

        PatientFile::create([
            'patient_id' => $this->selectedPatient->id,
            'file_path' => $path,
            'original_name' => $this->newFile->getClientOriginalName(),
            'file_type' => $this->newFile->extension(),
        ]);

        $this->newFile = null; // Limpiar input
        session()->flash('message_file', 'Archivo subido correctamente.');
        $this->loadFiles(); // Refrescar lista
    }

    // --- FUNCIÓN PARA BORRAR ARCHIVO ---
    public function deleteFile($fileId)
    {
        $file = PatientFile::find($fileId);
        
        if ($file && $file->patient_id == $this->selectedPatient->id) {
            // Borrar del disco
            Storage::disk('public')->delete($file->file_path);
            // Borrar de BD
            $file->delete();
            
            $this->loadFiles();
        }
    }

    public function closeHistory()
    {
        $this->selectedPatient = null;
        $this->history = [];
        $this->patientFiles = [];
        $this->newFile = null;
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
            'patients' => $patients
        ]);
    }
}