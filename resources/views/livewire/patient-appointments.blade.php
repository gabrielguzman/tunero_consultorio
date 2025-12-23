<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Header / acción principal --}}
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">
           <div class="flex flex-col items-center text-center gap-4 sm:gap-5">

    <div>
        <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">
            Mis Turnos
        </h2>
        <p class="text-base sm:text-lg text-gray-500 mt-2">
            Historial y próximas visitas
        </p>
    </div>

    <a href="{{ route('guest.booking') }}"
       class="inline-flex items-center justify-center gap-2
              px-8 py-3 rounded-2xl
              bg-blue-600 text-white text-base font-bold
              shadow-md hover:bg-blue-700
              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
              transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Turno
    </a>

</div>

            {{-- Flash message --}}
            @if (session()->has('message'))
                <div class="mt-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 shrink-0">
                            <svg class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-green-800">{{ session('message') }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Listado en cards --}}
        <div class="grid grid-cols-1 gap-4">
            @forelse($appointments as $app)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden">
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-5">

                            {{-- Bloque hora / fecha --}}
                            <div class="flex items-center gap-4 lg:min-w-[240px]">
                                <div class="h-12 w-12 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-sm flex-shrink-0"
                                     style="background-color: {{ $app->type->color ?? '#3b82f6' }}">
                                    {{ substr($app->type->name, 0, 1) }}
                                </div>

                                <div class="leading-tight">
                                    <p class="text-2xl font-black text-gray-900">
                                        {{ \Carbon\Carbon::parse($app->start_time)->format('H:i') }}
                                        <span class="text-xs text-gray-500 font-semibold">hs</span>
                                    </p>
                                    <p class="text-sm text-gray-500 capitalize mt-1">
                                        {{ \Carbon\Carbon::parse($app->start_time)->locale('es')->isoFormat('dddd D/MM') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1">
                                <h4 class="text-lg font-extrabold text-gray-900">
                                    {{ $app->type->name }}
                                </h4>

                                <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                    <div class="inline-flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Paciente: <strong class="text-gray-800">{{ $app->patient->name }}</strong></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Estado + acción --}}
                            <div class="flex items-center justify-between lg:justify-end gap-3 w-full lg:w-auto">
                                @if($app->status == 'confirmed')
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-extrabold
                                                 bg-green-50 text-green-800 border border-green-200">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        Confirmado
                                    </span>
                                @elseif($app->status == 'cancelled')
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-extrabold
                                                 bg-red-50 text-red-800 border border-red-200">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        Cancelado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-extrabold
                                                 bg-gray-50 text-gray-800 border border-gray-200">
                                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                        {{ ucfirst($app->status) }}
                                    </span>
                                @endif

                                @if($app->status !== 'cancelled')
                                    <button wire:click="cancel({{ $app->id }})"
                                            wire:confirm="¿Seguro que deseas cancelar este turno?"
                                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-bold
                                                   bg-white text-red-700 border border-red-200
                                                   hover:bg-red-50 hover:border-red-300
                                                   focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                        Cancelar
                                    </button>
                                @endif
                            </div>

                        </div>
                    </div>

                    {{-- Barra inferior sutil (detalle visual) --}}
                    <div class="h-1 w-full bg-gray-100">
                        <div class="h-1"
                             style="width: 30%; background-color: {{ $app->type->color ?? '#3b82f6' }};">
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-3 text-base font-extrabold text-gray-900">No hay turnos</h3>
                    <p class="mt-1 text-sm text-gray-500">Comienza reservando tu primera visita.</p>

                    <div class="mt-6">
                        <a href="{{ route('guest.booking') }}"
                           class="inline-flex items-center justify-center rounded-xl px-5 py-2.5
                                  bg-blue-600 text-white text-sm font-bold shadow-sm
                                  hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Reservar Ahora
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>
