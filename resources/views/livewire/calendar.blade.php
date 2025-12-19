<div class="p-6">
    
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4">
        <div id='calendar' wire:ignore></div>
    </div>

    @if($isCreateModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm px-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-white font-bold text-lg">Agendar Nuevo Turno</h3>
                <button wire:click="closeModals" class="text-white opacity-70 hover:opacity-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="saveNew" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fecha</label>
                            <input type="date" wire:model="newDate" class="w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Hora</label>
                            <input type="time" wire:model="newTime" class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Paciente</label>
                        <select wire:model="patient_id" class="w-full rounded-lg border-gray-300">
                            <option value="">Seleccionar...</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('patient_id') <span class="text-red-500 text-xs">Requerido</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Motivo</label>
                        <select wire:model="type_id" class="w-full rounded-lg border-gray-300">
                            @foreach($types as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition">
                        Guardar Turno
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($isEditModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm px-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh] animate-fade-in-up">
            
            <div class="bg-gray-800 px-6 py-4 flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Paciente</span>
                    @php $paciente = $patients->find($patient_id); @endphp
                    <h3 class="text-2xl font-bold text-white leading-tight mt-1">{{ $paciente->name ?? '---' }}</h3>
                </div>
                <button wire:click="closeModals" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 flex-1">
                
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-3">Estado actual</label>
                    <div class="flex gap-2">
                        <button wire:click="$set('status', 'scheduled')" class="flex-1 py-2 text-sm font-bold rounded-md border transition {{ $status === 'scheduled' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 hover:bg-gray-100' }}">Pendiente</button>
                        <button wire:click="$set('status', 'completed')" class="flex-1 py-2 text-sm font-bold rounded-md border transition {{ $status === 'completed' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 hover:bg-gray-100' }}">Realizado</button>
                        <button wire:click="$set('status', 'cancelled')" class="flex-1 py-2 text-sm font-bold rounded-md border transition {{ $status === 'cancelled' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-600 hover:bg-gray-100' }}">Cancelado</button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 uppercase mb-2">Evolución / Diagnóstico</label>
                    <textarea wire:model="doctor_notes" rows="6" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Escribe aquí los detalles de la consulta..."></textarea>
                </div>

                <details class="group border border-gray-200 rounded-lg p-3">
                    <summary class="flex justify-between items-center font-medium cursor-pointer list-none text-sm text-gray-600">
                        <span>Opciones Avanzadas (Fecha/Hora)</span>
                        <span class="transition group-open:rotate-180">
                            <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                        </span>
                    </summary>
                    <div class="text-neutral-600 mt-3 group-open:animate-fadeIn grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Fecha</label>
                            <input type="date" wire:model="newDate" class="w-full rounded border-gray-300 text-sm mt-1">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Hora</label>
                            <input type="time" wire:model="newTime" class="w-full rounded border-gray-300 text-sm mt-1">
                        </div>
                    </div>
                </details>
            </div>

            <div class="bg-gray-100 px-6 py-4 flex justify-between items-center border-t border-gray-200">
                <button wire:click="deleteAppointment" wire:confirm="¿Borrar este turno?" class="text-red-600 text-sm font-bold hover:underline">Eliminar Turno</button>
                <button wire:click="saveEdit" class="bg-gray-900 text-white px-6 py-2.5 rounded-lg font-bold shadow hover:bg-black transition">Guardar Cambios</button>
            </div>
        </div>
    </div>
    @endif

    <style>
        .fc-toolbar-title { font-size: 1.5rem !important; text-transform: capitalize; }
        .fc-button { background-color: #2563EB !important; border: none !important; }
        .animate-fade-in-up { animation: fadeInUp 0.3s ease-out; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- SOLUCIÓN DEL ERROR ---
            // 1. Guardamos los datos de PHP en una variable JS primero.
            // Usamos json_encode sin la coma final para que Blade no se confunda.
            const appointments = {!! json_encode($events) !!};

            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                // Configuración visual
                initialView: 'timeGridWeek',
                locale: 'es',
                firstDay: 1,
                slotMinTime: '08:00:00',
                slotMaxTime: '21:00:00',
                allDaySlot: false,
                height: 'auto',
                contentHeight: 700,
                expandRows: true,
                nowIndicator: true,
                
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },

                // 2. Usamos la variable JS limpia
                events: appointments,

                // Eventos de Click
                dateClick: function(info) {
                    // @this es el proxy de Livewire para llamar funciones PHP
                    @this.openCreateModal(info.dateStr);
                },

                eventClick: function(info) {
                    @this.openEditModal(info.event.id);
                }
            });

            calendar.render();

            // Recargar página cuando se guarde un turno
            Livewire.on('refresh-calendar', () => {
                window.location.reload();
            });
        });
    </script>
</div>
</div>