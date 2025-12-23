<x-app-layout>
   {{--  <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Control') }}
        </h2>
    </x-slot> --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- ========================================================== --}}
            {{-- VISTA DE LA DOCTORA (ADMIN) --}}
            {{-- ========================================================== --}}
            @if(Auth::user()->role === 'admin')
                
                {{-- 1. CONSULTA RÁPIDA (Directa en la vista) --}}
                @php
                    $todayCount = \App\Models\Appointment::whereDate('start_time', \Carbon\Carbon::today())
                        ->where('status', '!=', 'cancelled')
                        ->count();
                @endphp

                {{-- 2. TARJETA DE "AGENDA DE HOY" --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-5">
                        <div class="h-14 w-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>

                        <div>
                            <h2 class="text-lg font-bold text-gray-800 leading-tight">Agenda de Hoy</h2>
                            @if($todayCount > 0)
                                <p class="text-sm text-gray-500 mt-1">
                                    Tienes <span class="font-black text-gray-900">{{ $todayCount }}</span> pacientes en espera.
                                </p>
                            @else
                                <p class="text-sm text-gray-400 mt-1">Hoy no hay turnos programados.</p>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="block text-5xl font-black text-gray-900 tracking-tight leading-none">
                            {{ $todayCount }}
                        </span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1 block">
                            Turnos
                        </span>
                    </div>
                </div>

                {{-- 3. EL CALENDARIO (Tu componente existente) --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <livewire:calendar />
                </div>

            {{-- ========================================================== --}}
            {{-- VISTA DEL PACIENTE --}}
            {{-- ========================================================== --}}
            @else
                <livewire:patient-appointments />
            @endif

        </div>
    </div>
</x-app-layout>