<div class="min-h-screen bg-gray-50 py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Configuración de Agenda
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Define tus horarios habituales de trabajo y gestiona tus días libres.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Horario Semanal Base</h3>
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            Lunes a Sábado
                        </span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @php $dias = [1=>'Lunes', 2=>'Martes', 3=>'Miércoles', 4=>'Jueves', 5=>'Viernes', 6=>'Sábado']; @endphp

                        @foreach($dias as $dayNum => $dayName)
                            <div wire:key="day-{{ $dayNum }}" 
                                 class="group flex flex-col sm:flex-row items-center justify-between p-5 hover:bg-gray-50 transition-colors duration-200 
                                 {{ $scheduleData[$dayNum]['is_dirty'] ? 'bg-blue-50/60' : '' }}">
                                
                                <div class="flex items-center gap-4 w-full sm:w-1/3 mb-4 sm:mb-0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model.live="scheduleData.{{ $dayNum }}.active" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                    
                                    <span class="text-sm font-medium {{ $scheduleData[$dayNum]['active'] ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ $dayName }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-center w-full sm:w-1/3 gap-3">
                                    @if($scheduleData[$dayNum]['active'])
                                        <div class="relative">
                                            <input type="time" 
                                                   wire:model.live="scheduleData.{{ $dayNum }}.start"
                                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                        </div>
                                        <span class="text-gray-400">a</span>
                                        <div class="relative">
                                            <input type="time" 
                                                   wire:model.live="scheduleData.{{ $dayNum }}.end"
                                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                        </div>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-500">
                                            No laborable
                                        </span>
                                    @endif
                                </div>

                                <div class="flex justify-end items-center w-full sm:w-1/3 mt-4 sm:mt-0 gap-3">
                                    
                                    {{-- Mensaje de éxito --}}
                                    @if (session()->has('message_' . $dayNum))
                                        <span class="text-green-600 text-xs font-bold animate-fade-in-out">
                                            ¡Guardado!
                                        </span>
                                    @endif

                                    {{-- Botón Guardar Dinámico --}}
                                    <button wire:click="saveDay({{ $dayNum }})" 
                                            class="inline-flex items-center justify-center rounded-lg px-3 py-2 text-sm font-semibold shadow-sm transition-all duration-200
                                            {{ $scheduleData[$dayNum]['is_dirty'] 
                                                ? 'bg-blue-600 text-white hover:bg-blue-500 hover:shadow-md ring-2 ring-blue-600 ring-offset-2' 
                                                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' 
                                            }}">
                                        @if($scheduleData[$dayNum]['is_dirty'])
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Guardar
                                        @else
                                            <span class="text-gray-400 font-normal">Al día</span>
                                        @endif
                                    </button>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-red-100 rounded-lg text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Bloquear Fecha</h3>
                    </div>
                    
                    <p class="text-sm text-gray-500 mb-4">
                        Útil para feriados, vacaciones o días que no atenderás.
                    </p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Fecha</label>
                            <input type="date" wire:model="blockDate" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-red-600 sm:text-sm sm:leading-6">
                            @error('blockDate') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Motivo</label>
                            <input type="text" wire:model="blockReason" placeholder="Ej: Congreso Médico" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-red-600 sm:text-sm sm:leading-6">
                            @error('blockReason') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <button wire:click="blockNewDate" class="w-full rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 transition-colors">
                            Bloquear Agenda
                        </button>
                    </div>
                </div>

                @if($blockedDays->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                        <h4 class="text-sm font-semibold text-gray-900">Días Cerrados</h4>
                    </div>
                    <ul role="list" class="divide-y divide-gray-100">
                        @foreach($blockedDays as $block)
                            <li class="px-4 py-4 flex justify-between gap-x-6">
                                <div class="min-w-0 flex-auto">
                                    <p class="text-sm font-semibold leading-6 text-gray-900">
                                        {{ \Carbon\Carbon::parse($block->date)->isoFormat('D [de] MMMM') }}
                                    </p>
                                    <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ $block->reason }}</p>
                                </div>
                                <div class="shrink-0 flex flex-col items-end">
                                    <button wire:click="unblockDate({{ $block->id }})" class="text-xs text-red-600 hover:text-red-800 font-medium hover:underline">
                                        Desbloquear
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>