<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
            
            <div class="border-b border-gray-100 p-6 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Mis Turnos</h2>
                    <p class="text-gray-500 text-sm mt-1">Historial y próximas visitas.</p>
                </div>
                
                <a href="{{ route('guest.booking') }}" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-blue-700 text-sm shadow transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nuevo Turno
                </a>
            </div>

            @if (session()->has('message'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 m-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-bold">{{ session('message') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="divide-y divide-gray-100">
                @forelse($appointments as $app)
                    <div class="p-6 flex flex-col sm:flex-row sm:items-center hover:bg-gray-50 transition duration-150 ease-in-out gap-6">
                        
                        <div class="flex items-center gap-4 min-w-[180px]">
                            <div class="h-12 w-12 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-sm flex-shrink-0" 
                                 style="background-color: {{ $app->type->color ?? '#3b82f6' }}">
                                {{ substr($app->type->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xl font-bold text-gray-800 leading-none">
                                    {{ \Carbon\Carbon::parse($app->start_time)->format('H:i') }} <span class="text-xs text-gray-400 font-normal">hs</span>
                                </p>
                                <p class="text-sm text-gray-500 capitalize mt-1">
                                    {{ \Carbon\Carbon::parse($app->start_time)->locale('es')->isoFormat('dddd D/MM') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex-1 sm:border-l sm:border-gray-100 sm:pl-6">
                            <h4 class="text-lg font-bold text-gray-800">{{ $app->type->name }}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="text-sm text-gray-600">Paciente: <strong>{{ $app->patient->name }}</strong></span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto mt-2 sm:mt-0">
                            
                            @if($app->status == 'confirmed')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    Confirmado
                                </span>
                            @elseif($app->status == 'cancelled')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                    Cancelado
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                    {{ ucfirst($app->status) }}
                                </span>
                            @endif

                            @if($app->status !== 'cancelled')
                                <button wire:click="cancel({{ $app->id }})" 
                                        wire:confirm="¿Seguro que deseas cancelar este turno?"
                                        class="text-gray-400 hover:text-red-600 text-sm font-medium underline decoration-2 decoration-transparent hover:decoration-red-600 transition ml-2">
                                    Cancelar
                                </button>
                            @endif
                        </div>

                    </div>
                @empty
                    <div class="text-center py-16">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay turnos</h3>
                        <p class="mt-1 text-sm text-gray-500">Comienza reservando tu primera visita.</p>
                        <div class="mt-6">
                            <a href="{{ route('guest.booking') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Reservar Ahora
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
            
        </div>
    </div>
</div>