<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class PatientDocuments extends Component
{
    public $patients;

    public function mount()
    {
        // Traemos los hijos del usuario y sus archivos (ordenados por fecha)
        $this->patients = Patient::where('user_id', Auth::id())
            ->with(['files' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get();
    }

    public function render()
    {
        return view('livewire.patient-documents');
    }
}