<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-xl font-bold text-gray-800">Identidad del Consultorio</h2>
                <p class="text-gray-500 text-sm">Estos datos aparecerán en la web pública de reservas.</p>
            </div>

            @if (session()->has('message_settings'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    {{ session('message_settings') }}
                </div>
            @endif

            <form wire:submit.prevent="saveBusinessSettings">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700">Nombre del Consultorio</label>
                        <input type="text" wire:model="business_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('business_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Teléfono / WhatsApp</label>
                        <input type="text" wire:model="contact_phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Email de Contacto</label>
                        <input type="email" wire:model="contact_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Límite de turnos diarios (Web)</label>
                        <p class="text-xs text-gray-500 mb-2">0 = Ilimitado.</p>
                        <input type="number" wire:model="max_turnos_por_dia" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Permitir Sobreturnos</label>
                            <p class="text-xs text-gray-500">Habilita cargar turnos superpuestos.</p>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="allow_overbooking" class="w-6 h-6 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-sm font-bold text-gray-700">
                                {{ $allow_overbooking ? 'Activado' : 'Desactivado' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled" class="bg-blue-600 text-white px-6 py-2 rounded-md font-bold hover:bg-blue-700 shadow transition flex items-center gap-2">
                        <span wire:loading.remove wire:target="saveBusinessSettings">Guardar Identidad</span>
                        <span wire:loading wire:target="saveBusinessSettings">Guardando...</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-xl font-bold text-gray-800">Agenda Semanal</h2>
                <p class="text-gray-500 text-sm">Horarios de apertura y cierre.</p>
            </div>

            <div class="space-y-4">
                @foreach ($scheduleData as $dayNum => $data)
                    <div class="flex items-center gap-4 p-4 rounded-lg border {{ $data['active'] ? 'bg-white border-gray-200' : 'bg-gray-50 border-gray-100 opacity-75' }}">
                        <div class="w-32 shrink-0">
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model.live="scheduleData.{{ $dayNum }}.active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 font-bold text-gray-700">
                                    {{ \Carbon\Carbon::create(2023, 1, 1)->addDays($dayNum)->locale('es')->isoFormat('dddd') }}
                                </span>
                            </label>
                        </div>

                        @if ($data['active'])
                            <div class="flex items-center gap-2">
                                <input type="time" wire:model.live="scheduleData.{{ $dayNum }}.start" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="text-gray-500">a</span>
                                <input type="time" wire:model.live="scheduleData.{{ $dayNum }}.end" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        @else
                            <span class="text-sm text-gray-400 italic">Cerrado</span>
                        @endif

                        <div class="flex-1 flex justify-end items-center gap-3">
                            @if (session()->has('message_' . $dayNum))
                                <span class="text-green-600 text-sm font-bold">{{ session('message_' . $dayNum) }}</span>
                            @endif

                            @if ($data['is_dirty'])
                                <button wire:click="resetDay({{ $dayNum }})" class="text-gray-400 hover:text-red-500 text-sm underline">Cancelar</button>
                                <button wire:click="saveDay({{ $dayNum }})" class="bg-green-600 text-white px-3 py-1 rounded shadow hover:bg-green-700 text-sm font-bold">Guardar</button>
                            @endif
                        </div>
                    </div>
                    @error("scheduleData.$dayNum.end") <span class="text-red-500 text-xs block pl-36">{{ $message }}</span> @enderror
                @endforeach
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-xl font-bold text-gray-800">Días Bloqueados</h2>
                <p class="text-gray-500 text-sm">Feriados o días sin atención.</p>
            </div>

            <div class="flex gap-4 items-end mb-8 bg-gray-50 p-4 rounded-lg">
                <div>
                    <label class="block text-sm font-bold text-gray-700">Fecha</label>
                    <input type="date" wire:model="blockDate" min="{{ date('Y-m-d') }}" class="mt-1 rounded-md border-gray-300 shadow-sm">
                    @error('blockDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-bold text-gray-700">Motivo</label>
                    <input type="text" wire:model="blockReason" placeholder="Ej: Feriado" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                    @error('blockReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <button wire:click="blockNewDate" class="bg-red-600 text-white px-4 py-2 rounded-md font-bold hover:bg-red-700 h-10">Bloquear</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($blockedDays as $day)
                    <div class="flex justify-between items-center bg-white border border-red-100 p-3 rounded-lg shadow-sm">
                        <div>
                            <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-500">{{ $day->reason }}</p>
                        </div>
                        <button wire:click="unblockDate({{ $day->id }})" class="text-red-400 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm col-span-3 text-center">No hay días bloqueados.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>