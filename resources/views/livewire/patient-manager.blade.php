<div class="min-h-screen bg-gray-50 py-8 sm:py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- HEADER --}}
        <div class="sm:flex sm:items-center sm:justify-between border-b border-gray-200 pb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Mis Hijos / Pacientes</h1>
                <p class="mt-2 text-sm text-gray-600">Administra la ficha médica y datos personales de tus hijos.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button wire:click="create"
                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-all hover:-translate-y-0.5">
                    <svg class="-ml-0.5 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Nuevo Paciente
                </button>
            </div>
        </div>

        {{-- MENSAJE DE ÉXITO --}}
        @if (session()->has('message'))
            <div class="rounded-xl bg-green-50 p-4 border-l-4 border-green-500 animate-fade-in-up">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- LISTADO DE TARJETAS --}}
        @if ($patients->count() > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($patients as $p)
                    <div class="group relative flex flex-col bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-200">
                        <div class="p-6 flex-1">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="h-12 w-12 rounded-full {{ $p->sex == 'F' ? 'bg-pink-100 text-pink-600' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center font-bold text-xl">
                                    {{ substr($p->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $p->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $p->age_string }} 
                                        @if($p->blood_type) <span class="mx-1">•</span> {{ $p->blood_type }} @endif
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">DNI:</span>
                                    <span class="font-medium text-gray-900">{{ $p->dni ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Obra Social:</span>
                                    <span class="font-medium text-gray-900">{{ $p->healthInsurance->name ?? 'Particular' }}</span>
                                </div>
                            </div>

                            @if ($p->medical_alerts)
                                <div class="mt-4 p-2 bg-red-50 border border-red-100 rounded text-xs text-red-700 font-medium">
                                    ⚠️ {{ Str::limit($p->medical_alerts, 50) }}
                                </div>
                            @endif
                        </div>

                        <div class="flex border-t border-gray-100 divide-x divide-gray-100 bg-gray-50">
                            <button wire:click="edit({{ $p->id }})" class="flex-1 py-3 text-sm font-medium text-gray-700 hover:bg-white hover:text-blue-600 transition-colors">
                                Ver Ficha / Editar
                            </button>
                            <button wire:click="delete({{ $p->id }})" wire:confirm="¿Estás seguro?" class="flex-1 py-3 text-sm font-medium text-gray-700 hover:bg-white hover:text-red-600 transition-colors">
                                Eliminar
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Aún no hay pacientes registrados</h3>
                <button wire:click="create" class="mt-6 inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                    Comenzar
                </button>
            </div>
        @endif

        {{-- ================= MODAL ================= --}}
        @if ($isModalOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm px-4 py-6 overflow-y-auto">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[90vh] animate-fade-in-up">
                    
                    {{-- CABECERA MODAL --}}
                    <div class="bg-gray-900 px-6 py-4 flex justify-between items-center shrink-0 rounded-t-2xl">
                        <h3 class="text-white font-bold text-lg">
                            {{ $isEditing ? 'Ficha Médica: ' . $name : 'Nuevo Registro' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    {{-- NAV TABS (PESTAÑAS) --}}
                    <div class="flex border-b border-gray-200 bg-gray-50 overflow-x-auto shrink-0">
                        <button wire:click="setTab('basic')" 
                            class="px-6 py-3 text-sm font-bold whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'basic' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            📝 Datos Básicos
                        </button>
                        <button wire:click="setTab('health')" 
                            class="px-6 py-3 text-sm font-bold whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'health' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            🩺 Salud & Físico
                        </button>
                        <button wire:click="setTab('birth')" 
                            class="px-6 py-3 text-sm font-bold whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'birth' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            👶 Nacimiento
                        </button>
                        <button wire:click="setTab('admin')" 
                            class="px-6 py-3 text-sm font-bold whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'admin' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            📂 Administrativo
                        </button>
                    </div>

                    {{-- CUERPO FORMULARIO (SCROLLABLE) --}}
                    <div class="overflow-y-auto p-6 lg:p-8">
                        <form wire:submit.prevent="save" id="patientForm">
                            
                            {{-- PESTAÑA 1: BÁSICOS --}}
                            @if($activeTab === 'basic')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in-up">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre Completo *</label>
                                        <input type="text" wire:model="name" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">DNI</label>
                                        <input type="text" wire:model="dni" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                        @error('dni') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Sexo Biológico</label>
                                        <select wire:model="sex" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Seleccione...</option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Femenino</option>
                                            <option value="X">Otro / Indistinto</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Fecha Nacimiento *</label>
                                        <input type="date" wire:model.live="birth_date" max="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                        @if($this->calculatedAge) <p class="text-xs text-blue-600 mt-1 font-bold">Edad: {{ $this->calculatedAge }}</p> @endif
                                        @error('birth_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Lugar de Nacimiento</label>
                                        <input type="text" wire:model="place_of_birth" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Ciudad, Provincia">
                                    </div>

                                    <div class="col-span-2 border-t border-gray-100 mt-2 pt-4">
                                        <h4 class="font-bold text-gray-800 mb-3">Cobertura Médica</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Obra Social</label>
                                                <select wire:model="health_insurance_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                                    <option value="">Particular / Ninguna</option>
                                                    @foreach ($insurances as $os)
                                                        <option value="{{ $os->id }}">{{ $os->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nº Afiliado</label>
                                                <input type="text" wire:model="affiliate_number" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- PESTAÑA 2: SALUD --}}
                            @if($activeTab === 'health')
                                <div class="space-y-6 animate-fade-in-up">
                                    
                                    {{-- Datos Físicos --}}
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Grupo Sangre</label>
                                            <select wire:model="blood_type" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                <option value="">-</option>
                                                <option value="A+">A+</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B-">B-</option>
                                                <option value="AB+">AB+</option>
                                                <option value="AB-">AB-</option>
                                                <option value="0+">0+</option>
                                                <option value="0-">0-</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Peso (kg)</label>
                                            <input type="number" step="0.01" wire:model="current_weight" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Altura (cm)</label>
                                            <input type="number" wire:model="height_cm" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        </div>
                                        <div class="flex items-end pb-2">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" wire:model="vaccination_complete" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-5 w-5">
                                                <span class="ml-2 text-sm text-gray-700 font-bold">Vacunas al día</span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Alertas y Antecedentes --}}
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-red-600 mb-1">⚠️ Alertas Médicas / Alergias Importantes</label>
                                            <textarea wire:model="medical_alerts" rows="2" class="w-full rounded-lg border-red-200 bg-red-50 focus:border-red-500 focus:ring-red-500 placeholder-red-300" placeholder="Ej: Alérgico a penicilina, Diabetes tipo 1..."></textarea>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-1">Alergias (Detalle)</label>
                                                <textarea wire:model="allergies" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-1">Enfermedades Previas</label>
                                                <textarea wire:model="background_diseases" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Medicación Actual</label>
                                            <input type="text" wire:model="current_medication" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre de medicamentos y dosis...">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- PESTAÑA 3: NACIMIENTO --}}
                            @if($activeTab === 'birth')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in-up">
                                    <div class="md:col-span-2 bg-blue-50 p-4 rounded-lg text-sm text-blue-800 mb-2">
                                        <span class="font-bold">Nota:</span> Estos datos son útiles para el seguimiento pediátrico, pero opcionales.
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Tipo de Parto</label>
                                        <select wire:model="birth_type" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Seleccione...</option>
                                            <option value="Normal">Parto Normal / Vaginal</option>
                                            <option value="Cesarea">Cesárea</option>
                                            <option value="Forceps">Fórceps</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Tipo de Embarazo</label>
                                        <select wire:model="pregnancy_type" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Seleccione...</option>
                                            <option value="Normal">Normal</option>
                                            <option value="Alto Riesgo">Alto Riesgo</option>
                                            <option value="Multiple">Múltiple</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Peso al Nacer (kg)</label>
                                        <input type="number" step="0.001" wire:model="birth_weight" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: 3.450">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Edad Gestacional</label>
                                        <input type="text" wire:model="gestational_age" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: 39 semanas">
                                    </div>
                                </div>
                            @endif

                            {{-- PESTAÑA 4: ADMIN --}}
                            @if($activeTab === 'admin')
                                <div class="space-y-6 animate-fade-in-up">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Nº Historia Clínica</label>
                                            <input type="text" wire:model="clinical_history_number" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Fecha de Alta</label>
                                            <input type="date" wire:model="discharge_date" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Observaciones Generales</label>
                                        <textarea wire:model="observations" rows="4" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    </div>

                                    <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <div class="relative">
                                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            </div>
                                            <span class="ml-3 text-sm font-medium text-gray-900">Paciente Activo</span>
                                        </label>
                                    </div>
                                </div>
                            @endif

                        </form>
                    </div>

                    {{-- FOOTER MODAL (BOTÓN GUARDAR) --}}
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3 shrink-0 rounded-b-2xl">
                        <button wire:click="closeModal" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-bold hover:bg-white transition">
                            Cancelar
                        </button>
                        <button wire:click="save" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-500 shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                            Guardar Ficha
                        </button>
                    </div>
                    
                </div>
            </div>
        @endif

    </div>

    <style>
        .animate-fade-in-up { animation: fadeInUp 0.3s ease-out forwards; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</div>