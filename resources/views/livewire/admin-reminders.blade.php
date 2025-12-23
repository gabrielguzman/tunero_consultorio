<div class="py-12 bg-gray-50 min-h-screen">
    <style>
        .enviar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .enviar{
            color: black
        }
    </style>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Recordatorios WhatsApp</h2>
                <p class="text-sm text-gray-500">Turnos para mañana: <strong>{{ $date->format('d/m/Y') }}</strong></p>
            </div>
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg text-sm font-bold">
                {{ count($appointments) }} pacientes por avisar
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                        <tr>
                            <th class="px-6 py-4">Hora</th>
                            <th class="px-6 py-4">Paciente</th>
                            <th class="px-6 py-4">Teléfono</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($appointments as $app)
                            @php
                                // 1. Obtener teléfono y limpiar caracteres raros
                                $phone = $app->patient->user->phone ?? $app->patient->phone;
                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                
                                // 2. Crear mensaje bonito
                                $msg = "Hola *" . $app->patient->name . "*! \n";
                                $msg .= "Te recuerdo tu turno para mañana *" . $app->start_time->format('d/m') . "* a las *" . $app->start_time->format('H:i') . "hs*.\n";
                                $msg .= "Especialidad: " . $app->type->name . ".\n\n";
                                $msg .= "Av. Siempre Viva 123.\n";
                                $msg .= "¿Confirmas asistencia?";
                                
                                // 3. Codificar para URL
                                $whatsappUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($msg);
                            @endphp

                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    {{ $app->start_time->format('H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $app->patient->name }}
                                    <div class="text-xs text-gray-400">{{ $app->type->name }}</div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs">
                                    {{ $phone ?? 'Sin número' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($cleanPhone)
                                        <a href="{{ $whatsappUrl }}" 
                                           target="_blank" 
                                           onclick="this.classList.add('opacity-50', 'cursor-not-allowed')"
                                           class="inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#20bd5a] text-white px-4 py-2 enviar rounded-lg font-bold transition shadow-sm hover:-translate-y-0.5">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            Enviar
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Sin teléfono</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                    No hay turnos programados para mañana.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>