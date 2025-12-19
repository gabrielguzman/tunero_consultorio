<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Directorio de Pacientes</h2>
                <p class="text-sm text-gray-500">Administra los datos y revisa el historial médico.</p>
            </div>
            
            <div class="relative w-full sm:w-96">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       class="block w-full p-3 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500 shadow-sm transition" 
                       placeholder="Buscar por nombre, apellido o DNI...">
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-4">Paciente</th>
                            <th class="px-6 py-4">Edad</th>
                            <th class="px-6 py-4">Obra Social</th>
                            <th class="px-6 py-4">Contacto (Padres)</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($patients as $p)
                        <tr class="hover:bg-blue-50/50 transition-colors group">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                        {{ substr($p->name, 0, 1) }}
                                    </div>
                                    <span>{{ $p->name }}</span>
                                </div>
                                @if($p->medical_alerts)
                                    <span class="mt-1 inline-flex bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide">Alerta Médica</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ $p->age_string }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 rounded text-gray-700 font-medium text-xs border border-gray-200">
                                    {{ $p->healthInsurance->name ?? 'Particular' }}
                                </span>
                                <div class="text-xs text-gray-400 mt-1 font-mono tracking-tighter">{{ $p->affiliate_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 font-medium">{{ $p->user->name ?? 'Sin Asignar' }}</div>
                                <div class="text-xs text-gray-500">{{ $p->user->email ?? '' }}</div>
                                <div class="text-xs text-gray-400">{{ $p->user->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="showHistory({{ $p->id }})" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center justify-end gap-1 w-full transition-colors">
                                    Historia Clínica
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $patients->links() }}
            </div>
        </div>
    </div>

    @if($selectedPatient)
    <div class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            
            <div class="absolute inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" wire:click="closeHistory"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                <div class="pointer-events-auto w-screen max-w-md transform transition ease-in-out duration-500 sm:duration-700 translate-x-0">
                    <div class="flex h-full flex-col bg-white shadow-2xl">
                        
                        <div class="bg-blue-600 px-4 py-6 sm:px-6 relative overflow-hidden">
                            <div class="bg-gray-50 border-b border-gray-200 p-4 sm:px-6">
                            <h3 class="text-sm font-bold text-gray-700 uppercase mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Estudios y Adjuntos
                            </h3>

                            <form wire:submit.prevent="uploadFile" class="flex items-start gap-2 mb-4">
                                <div class="flex-1">
                                    <input type="file" wire:model="newFile" class="block w-full text-xs text-gray-500
                                        file:mr-2 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-xs file:font-semibold
                                        file:bg-blue-50 file:text-blue-700
                                        hover:file:bg-blue-100
                                    "/>
                                    @error('newFile') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    
                                    <div wire:loading wire:target="newFile" class="text-xs text-blue-500 mt-1 font-bold">
                                        Subiendo archivo... espere.
                                    </div>
                                </div>
                                <button type="submit" class="bg-blue-600 text-white p-2 rounded-lg shadow hover:bg-blue-700 disabled:opacity-50" wire:loading.attr="disabled">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </button>
                            </form>

                            @if(session()->has('message_file'))
                                <span class="text-green-600 text-xs font-bold block mb-2">{{ session('message_file') }}</span>
                            @endif

                            @if(count($patientFiles) > 0)
                                <ul class="space-y-2 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach($patientFiles as $file)
                                        <li class="flex items-center justify-between bg-white p-2 rounded border border-gray-200 text-sm">
                                            <div class="flex items-center gap-2 overflow-hidden">
                                                @if(in_array($file->file_type, ['jpg','jpeg','png','webp']))
                                                    <span class="text-purple-500 font-bold text-xs">[IMG]</span>
                                                @elseif($file->file_type == 'pdf')
                                                    <span class="text-red-500 font-bold text-xs">[PDF]</span>
                                                @else
                                                    <span class="text-gray-500 font-bold text-xs">[FILE]</span>
                                                @endif
                                                
                                                <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="truncate hover:text-blue-600 hover:underline text-gray-700" title="{{ $file->original_name }}">
                                                    {{ $file->original_name }}
                                                </a>
                                            </div>
                                            <button wire:click="deleteFile({{ $file->id }})" wire:confirm="¿Borrar archivo?" class="text-gray-400 hover:text-red-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-xs text-gray-400 italic">No hay archivos adjuntos.</p>
                            @endif
                        </div>
                            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-10">
                                <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/></svg>
                            </div>

                            <div class="flex items-start justify-between relative z-10">
                                <h2 class="text-xl font-bold leading-6 text-white" id="slide-over-title">
                                    {{ $selectedPatient->name }}
                                </h2>
                                <button wire:click="closeHistory" class="text-blue-200 hover:text-white transition-colors focus:outline-none">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <div class="mt-2 relative z-10">
                                <p class="text-blue-100 text-sm font-medium">
                                    {{ $selectedPatient->age_string }} • {{ $selectedPatient->healthInsurance->name ?? 'Particular' }}
                                </p>
                                @if($selectedPatient->medical_alerts)
                                    <div class="mt-3 inline-flex items-center gap-1.5 bg-red-500/20 border border-red-400/30 px-3 py-1 rounded-full">
                                        <svg class="w-3 h-3 text-red-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span class="text-xs font-bold text-white uppercase tracking-wide">{{ $selectedPatient->medical_alerts }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="relative flex-1 bg-gray-100 overflow-y-auto">
                            <div class="px-4 py-6 sm:px-6 space-y-4">
                                
                                @if(count($history) > 0)
                                    @foreach($history as $app)
                                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 relative overflow-hidden hover:shadow-md transition-shadow">
                                            
                                            <div class="absolute left-0 top-0 bottom-0 w-1.5 
                                                @if($app->status == 'completed') bg-green-500 
                                                @elseif($app->status == 'cancelled') bg-red-500 
                                                @else bg-blue-500 @endif">
                                            </div>

                                            <div class="pl-3">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div>
                                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                                            {{ $app->start_time->isoFormat('D [de] MMMM, YYYY') }}
                                                        </p>
                                                        <h3 class="text-base font-bold text-gray-800 leading-tight">
                                                            {{ $app->type->name }}
                                                        </h3>
                                                    </div>
                                                    @if($app->status == 'completed')
                                                        <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold rounded border border-green-100 uppercase">Realizado</span>
                                                    @elseif($app->status == 'cancelled')
                                                        <span class="px-2 py-0.5 bg-red-50 text-red-700 text-[10px] font-bold rounded border border-red-100 uppercase">Cancelado</span>
                                                    @else
                                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded border border-blue-100 uppercase">Pendiente</span>
                                                    @endif
                                                </div>

                                                @if($app->doctor_notes)
                                                    <div class="mt-3 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                                        <p class="text-[10px] font-bold text-gray-500 mb-1 uppercase">Evolución Médica:</p>
                                                        <p class="text-sm text-gray-800 whitespace-pre-line leading-relaxed">{{ $app->doctor_notes }}</p>
                                                    </div>
                                                @else
                                                    @if($app->status == 'completed')
                                                        <p class="mt-2 text-xs text-gray-400 italic">El médico no registró notas.</p>
                                                    @endif
                                                @endif

                                                @if($app->patient_notes)
                                                    <div class="mt-3 pt-3 border-t border-gray-100 flex gap-2 items-start">
                                                        <span class="text-[10px] font-bold text-gray-400 uppercase shrink-0 mt-0.5">Motivo (Padre):</span>
                                                        <span class="text-xs text-gray-600 italic">{{ $app->patient_notes }}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="mt-2 flex items-center gap-1 text-[10px] text-gray-400 justify-end">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ $app->start_time->format('H:i') }} hs
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex flex-col items-center justify-center py-12 text-center bg-white rounded-xl border border-dashed border-gray-300 mx-4">
                                        <div class="bg-gray-100 p-3 rounded-full mb-3">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900">Historia clínica vacía</h3>
                                        <p class="text-xs text-gray-500 mt-1 max-w-xs">Este paciente aún no tiene turnos registrados o finalizados.</p>
                                    </div>
                                @endif
                                
                                <div class="h-6"></div> </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 border-t border-gray-200">
                             <button wire:click="closeHistory" class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                Cerrar Ficha
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>