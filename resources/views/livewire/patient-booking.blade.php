<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-3xl mx-auto text-center mb-10">
        <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
            Reserva tu Turno
        </h2>
        <p class="mt-4 text-lg text-gray-500">
            Agenda una cita para tus hijos de forma rápida y sencilla.
        </p>
    </div>

    @if (session()->has('success'))
        <div class="max-w-3xl mx-auto mb-6 bg-green-50 border-l-4 border-green-400 p-4 shadow-md rounded-r-lg animate-bounce">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-bold">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100">
                <div class="px-6 py-5 bg-blue-600 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        1. Paciente
                    </h3>
                </div>
                <div class="p-6 bg-white">
                    @if($userPatients->count() > 0)
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selecciona a tu hijo/a</label>
                        <select wire:model.live="patient_id" class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-xl shadow-sm">
                            @foreach($userPatients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                            @endforeach
                        </select>
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Motivo de Consulta</label>
                            <select wire:model.live="type_id" class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-xl shadow-sm">
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->duration_minutes }} min)</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm mb-4">No tienes pacientes registrados.</p>
                            <a href="#" class="text-blue-600 hover:text-blue-800 font-bold text-sm">Registrar hijo/a primero</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100">
                <div class="px-6 py-5 bg-indigo-600 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        2. Fecha
                    </h3>
                </div>
                <div class="p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Elige el día</label>
                    <input type="date" 
                           wire:model.live="selectedDate" 
                           min="{{ date('Y-m-d') }}"
                           class="block w-full px-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 text-lg">
                    <p class="text-xs text-gray-400 mt-2">Formato: Mes / Día / Año (según navegador)</p>
                </div>
            </div>

        </div>

        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100 min-h-[400px] flex flex-col">
                <div class="px-6 py-5 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-gray-800">
                        3. Horarios Disponibles
                    </h3>
                    <span class="text-sm font-medium text-gray-500 bg-white px-3 py-1 rounded-full border">
                        {{ \Carbon\Carbon::parse($selectedDate)->isoFormat('dddd D [de] MMMM') }}
                    </span>
                </div>
                
                <div class="p-6 flex-grow relative">
                    
                    <div wire:loading.flex wire:target="selectedDate, type_id, patient_id" class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center z-10 rounded-b-2xl">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="mt-3 text-blue-600 font-medium">Buscando huecos...</p>
                        </div>
                    </div>

                    @if(count($availableSlots) > 0)
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4">
    @foreach($availableSlots as $slot)
        <button 
            {{-- CORRECCIÓN CLAVE: Agregamos wire:key --}}
            wire:key="slot-{{ $slot }}" 
            wire:click="selectTime('{{ $slot }}')"
            type="button" 
            class="group relative w-full py-3 px-2 rounded-xl border-2 transition-all duration-200 ease-in-out
            {{ $selectedTime === $slot 
                ? 'bg-blue-600 border-blue-600 text-white shadow-lg transform scale-105 ring-2 ring-blue-300 ring-offset-1' 
                : 'bg-white border-gray-200 text-gray-700 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-600' 
            }}">
            
            <span class="block text-base font-bold">{{ $slot }}</span>
            
            @if($selectedTime === $slot)
                <span class="absolute -top-2 -right-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-0.5 rounded-full shadow-sm">
                    ✓
                </span>
            @endif
        </button>
    @endforeach
</div>
                    @else
                        <div class="flex flex-col items-center justify-center h-full py-12 text-center">
                            <div class="bg-gray-100 p-4 rounded-full mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">No hay turnos disponibles</h3>
                            <p class="text-gray-500 mt-1 max-w-sm">
                                El consultorio está lleno o cerrado en esta fecha. <br>
                                Por favor intenta seleccionando otro día en el calendario.
                            </p>
                        </div>
                    @endif
                </div>

                @if($selectedTime)
                    <div class="bg-blue-50 px-6 py-6 border-t border-blue-100 animate-fade-in-up">
                        <div class="flex flex-col md:flex-row gap-6 items-start">
                            <div class="flex-grow w-full">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nota para la Dra. (Opcional)</label>
                                <textarea wire:model="patient_notes" rows="2" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej: Tiene fiebre hace 2 días..."></textarea>
                            </div>
                            <div class="w-full md:w-auto flex-shrink-0 pt-2">
                                <button wire:click="saveAppointment" class="w-full md:w-auto px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white text-lg font-bold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                                    <span>Confirmar Turno</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </button>
                                <p class="text-xs text-center text-blue-400 mt-2">Se enviará confirmación al email</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <style>
        /* Animación suave para cuando aparece el footer */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out forwards;
        }
    </style>
</div>