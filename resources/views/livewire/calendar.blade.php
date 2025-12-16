<div class="p-6">
    <div class="bg-white rounded-lg shadow-lg p-4">
        
        <div id='calendar' wire:ignore></div>

    </div>

    <style>
        .fc-toolbar-title { font-size: 1.5rem !important; text-transform: capitalize; }
        .fc-button { background-color: #3788d8 !important; border: none !important; }
        .fc-event { cursor: pointer; }
    </style>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                // Configuración Visual
                initialView: 'timeGridWeek', // Vista semanal
                locale: 'es', // Español
                firstDay: 1,  // La semana arranca el Lunes
                slotMinTime: '08:00:00', // Hora visual de inicio
                slotMaxTime: '21:00:00', // Hora visual de fin
                allDaySlot: false, // Ocultar fila "Todo el día"
                height: 'auto', // Altura automática
                
                // Botonera superior
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                
                // --- CONEXIÓN CON LARAVEL ---
                // Aquí "imprimimos" el JSON que mandamos desde el componente PHP
                events: {!! $events !!},

                // --- INTERACTIVIDAD ---
                
                // Al hacer click en un turno existente
                eventClick: function(info) {
                    // Por ahora solo una alerta, luego haremos el modal de edición
                    alert('Paciente: ' + info.event.title + '\nNotas: ' + info.event.extendedProps.notes);
                },

                // Al hacer click en un espacio vacío (Para crear nuevo turno)
                dateClick: function(info) {
                    // Por ahora alerta, luego abriremos modal de creación
                    alert('¿Crear turno el ' + info.dateStr + '?');
                }
            });

            calendar.render();
        });
    </script>
</div>