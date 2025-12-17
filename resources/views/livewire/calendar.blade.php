<div class="p-6">
    <div class="bg-white rounded-lg shadow-lg p-4">
        <div id='calendar' wire:ignore></div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 transform transition-all scale-100">
            
            <div class="flex justify-between items-center mb-5 border-b pb-3">
                <h2 class="text-xl font-bold text-gray-800">Nuevo Turno (Admin)</h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
            
            <form wire:submit.prevent="saveAppointment">
                
                <div class="grid grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha</label>
                        <input type="date" wire:model="newDate" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('newDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Hora Inicio</label>
                        <input type="time" wire:model="newTime" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('newTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Paciente</label>
                    <select wire:model="patient_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccione un paciente...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">
                                {{ $patient->name }} ({{ $patient->age_string }}) - {{ $patient->healthInsurance->name ?? 'Part.' }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Motivo de Consulta</label>
                    <select wire:model="type_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($types as $type)
                            <option value="{{ $type->id }}">
                                {{ $type->name }} ({{ $type->duration_minutes }} min)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Notas Internas (Opcional)</label>
                    <textarea wire:model="notes" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej: Trae estudios anteriores..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="closeModal" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-md transition-colors">
                        Guardar Turno
                    </button>
                </div>
            </form>

        </div>
    </div>
    @endif

    <style>
        /* Ajustes visuales para FullCalendar */
        .fc-toolbar-title { font-size: 1.5rem !important; text-transform: capitalize; font-weight: 600; color: #1f2937; }
        .fc-button-primary { background-color: #3b82f6 !important; border-color: #3b82f6 !important; }
        .fc-button-primary:hover { background-color: #2563eb !important; border-color: #2563eb !important; }
        .fc-event { cursor: pointer; border: none !important; padding: 2px; font-size: 0.85rem; }
        .fc-timegrid-event { border-radius: 4px; }
    </style>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            var calendar = new FullCalendar.Calendar(calendarEl, {
                // Configuración Base
                initialView: 'timeGridWeek',
                locale: 'es',
                firstDay: 1, // Lunes
                slotMinTime: '08:00:00',
                slotMaxTime: '21:00:00',
                allDaySlot: false,
                height: 'auto',
                expandRows: true,
                nowIndicator: true, // Muestra la línea roja de la hora actual
                
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                
                // DATA: Leemos el JSON que viene de Livewire
                // Usamos @this.events porque es una propiedad pública del componente
                events: {!! $events !!},

                // EVENTO 1: Click en hueco vacío -> ABRIR MODAL
                dateClick: function(info) {
                    // Llamamos al método PHP
                    @this.openModal(info.dateStr);
                },

                // EVENTO 2: Click en turno existente
                eventClick: function(info) {
                    // Por ahora alerta, luego haremos edición
                    alert('Paciente: ' + info.event.title + '\nNotas: ' + info.event.extendedProps.notes);
                }
            });

            calendar.render();

            // Escuchar cuando Livewire guarda un turno para refrescar la página
            Livewire.on('appointment-saved', () => {
                window.location.reload();
            });
        });
    </script>
</div>