<div class="space-y-6 font-sans">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="bg-blue-600 text-white p-2 rounded-lg shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h2 class="font-bold text-gray-800 text-lg">Reporte Mensual</h2>
        </div>

        <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-lg border border-gray-200">
            <select wire:model.live="month" class="bg-transparent border-none text-sm font-bold text-gray-700 focus:ring-0 cursor-pointer py-1">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}">{{ ucfirst(\Carbon\Carbon::create()->month($m)->locale('es')->monthName) }}</option>
                @endforeach
            </select>
            <span class="text-gray-300">|</span>
            <select wire:model.live="year" class="bg-transparent border-none text-sm font-bold text-gray-700 focus:ring-0 cursor-pointer py-1">
                @foreach(range(\Carbon\Carbon::now()->year, \Carbon\Carbon::now()->year - 2) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-green-500"></div>
            
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Facturación</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-2">${{ number_format($income, 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-green-50 rounded-full text-green-600 group-hover:bg-green-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-4 font-medium">Turnos completados</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-blue-500"></div>
            
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Turnos Totales</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-2">{{ $totalAppointments }}</h3>
                </div>
                <div class="p-2 bg-blue-50 rounded-full text-blue-600 group-hover:bg-blue-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-4 font-medium">En este periodo</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-purple-500"></div>
            
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pacientes Nuevos</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-2">{{ $newPatients }}</h3>
                </div>
                <div class="p-2 bg-purple-50 rounded-full text-purple-600 group-hover:bg-purple-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-4 font-medium">Registrados este mes</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800 text-sm">Obras Sociales</h3>
                <span class="text-xs bg-gray-100 text-gray-500 py-1 px-2 rounded">Top 5</span>
            </div>
            
            <hr class="border-gray-100 mb-6"> @if(count($byInsurance) > 0)
                <div class="space-y-5">
                    @foreach($byInsurance as $os)
                        @php $percent = ($totalAppointments > 0) ? ($os->total / $totalAppointments) * 100 : 0; @endphp
                        <div>
                            <div class="flex justify-between items-end mb-1">
                                <span class="text-xs font-bold text-gray-600">{{ $os->name }}</span>
                                <span class="text-xs font-bold text-gray-900">{{ $os->total }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-400 text-xs italic py-4">Sin datos suficientes.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800 text-sm">Tratamientos más pedidos</h3>
                <span class="text-xs bg-gray-100 text-gray-500 py-1 px-2 rounded">Top 5</span>
            </div>

            <hr class="border-gray-100 mb-6"> @if(count($topServices) > 0)
                <div class="space-y-5">
                    @foreach($topServices as $service)
                        @php $percent = ($totalAppointments > 0) ? ($service->total / $totalAppointments) * 100 : 0; @endphp
                        <div>
                            <div class="flex justify-between items-end mb-1">
                                <span class="text-xs font-bold text-gray-600">{{ $service->name }}</span>
                                <span class="text-xs font-bold text-gray-900">{{ $service->total }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-400 text-xs italic py-4">Sin datos suficientes.</p>
            @endif
        </div>
    </div>

</div>
