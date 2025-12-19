<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Reporte Mensual</h2>
                <p class="text-sm text-gray-500">Resumen de actividad y facturación estimada.</p>
            </div>
            
            <div class="flex gap-2 bg-white p-1 rounded-lg shadow-sm border border-gray-200">
                <select wire:model.live="month" class="border-none text-sm font-bold text-gray-700 focus:ring-0 rounded cursor-pointer bg-transparent">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->locale('es')->monthName }}</option>
                    @endforeach
                </select>
                
                <select wire:model.live="year" class="border-none text-sm font-bold text-gray-700 focus:ring-0 rounded cursor-pointer bg-transparent border-l border-gray-200">
                    {{-- AQUÍ ESTABA EL ERROR: Agregamos \Carbon\Carbon --}}
                    @foreach(range(\Carbon\Carbon::now()->year, \Carbon\Carbon::now()->year - 2) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Facturación Realizada</p>
                    <p class="text-4xl font-extrabold text-gray-900 mt-2">
                        ${{ number_format($income, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-green-600 font-bold mt-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Solo turnos completados
                    </p>
                </div>
                <div class="h-16 w-16 bg-green-50 rounded-full flex items-center justify-center text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Turnos Totales</p>
                    <p class="text-4xl font-extrabold text-gray-900 mt-2">
                        {{ $totalAppointments }}
                    </p>
                    <p class="text-xs text-blue-600 font-bold mt-2">
                        Agendados en el mes
                    </p>
                </div>
                <div class="h-16 w-16 bg-blue-50 rounded-full flex items-center justify-center text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-800">Pacientes por Obra Social</h3>
            </div>
            <div class="p-6">
                @if(count($byInsurance) > 0)
                    <div class="space-y-4">
                        @foreach($byInsurance as $os)
                            @php 
                                // Calculamos porcentaje simple para la barra de progreso
                                $percent = ($totalAppointments > 0) ? ($os->total / $totalAppointments) * 100 : 0; 
                            @endphp
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-sm font-bold text-gray-700">{{ $os->name }}</span>
                                    <span class="text-xs font-bold text-gray-500">{{ $os->total }} pacientes ({{ round($percent) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-400 text-sm py-4">No hay datos suficientes este mes.</p>
                @endif
            </div>
        </div>

    </div>
</div>