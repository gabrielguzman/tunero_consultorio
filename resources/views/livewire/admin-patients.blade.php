<div class="py-12 bg-gray-50 min-h-screen font-sans">

    {{-- ======================================================================== --}}
    {{-- 1. TABLA PRINCIPAL (Igual que antes, funciona bien) --}}
    {{-- ======================================================================== --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Directorio de Pacientes</h2>
                <p class="text-sm text-gray-500">Gestión integral de fichas médicas.</p>
            </div>

            <div class="relative w-full sm:w-96 group">

                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full py-2.5 pl-10 pr-10 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 shadow-sm transition-all duration-300 placeholder-gray-400"
                    placeholder="Buscar por nombre, DNI...">

                @if ($search)
                    <button wire:click="$set('search', '')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer focus:outline-none">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
        <hr>

        @if (session()->has('message_list'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                {{ session('message_list') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 font-bold">Paciente</th>
                            <th class="px-6 py-4 font-bold">Cobertura</th>
                            <th class="px-6 py-4 font-bold">Contacto</th>
                            <th class="px-6 py-4 text-right font-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($patients as $p)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm shadow-sm border border-blue-200">
                                            {{ substr($p->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $p->name }}</div>
                                            <div class="text-xs text-gray-500">DNI: {{ $p->dni ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    @if ($p->medical_alerts)
                                        <div
                                            class="mt-2 inline-flex items-center gap-1 bg-red-50 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full border border-red-100">
                                            {{ Str::limit($p->medical_alerts, 20) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 bg-gray-100 rounded-lg text-gray-700 font-bold text-xs border border-gray-200 inline-block">
                                        {{ $p->healthInsurance->name ?? 'Particular' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($p->user)
                                        <div class="text-gray-900 font-medium text-xs">{{ $p->user->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $p->user->phone }}</div>
                                    @else
                                        <span class="text-xs italic text-gray-400">Sin usuario</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="showHistory({{ $p->id }})"
                                        class="text-blue-600 hover:text-blue-800 font-bold text-sm hover:underline">
                                        Abrir Ficha
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">No se encontraron
                                    pacientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $patients->links() }}
            </div>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- 2. FICHA EXTENDIDA (SLIDE OVER) --}}
    {{-- ======================================================================== --}}
    @if ($selectedPatient)
        <div class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog"
            aria-modal="true">
            <div class="absolute inset-0 overflow-hidden">

                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                    wire:click="closeHistory"></div>

                {{-- Ajuste clave responsive:
                 - En mobile: panel full width (sin padding izquierdo) y max-w-full
                 - En desktop: se mantiene igual (pl-10 / sm:pl-16 + max-w-6xl) --}}
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-0 sm:pl-16">
                    <div
                        class="pointer-events-auto w-screen max-w-full sm:max-w-6xl transform transition ease-in-out duration-500 sm:duration-700 translate-x-0">
                        <div class="flex h-full flex-col bg-white shadow-2xl">

                            {{-- A. HEADER GRANDE --}}
                            <div class="bg-slate-900 px-4 sm:px-8 py-6 sm:py-8 relative overflow-hidden shrink-0">

                                <div class="absolute top-0 right-0 -mr-16 -mt-16 opacity-20 pointer-events-none">
                                    <div class="w-80 h-80 bg-blue-500 rounded-full blur-3xl"></div>
                                </div>

                                <div class="relative z-20 flex justify-between items-start gap-4">
                                    <div class="flex gap-4 sm:gap-6 items-center min-w-0">

                                        <div
                                            class="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-black flex items-center justify-center shadow-2xl ring-4 ring-slate-800 z-30 shrink-0">
                                            <span class="font-black text-3xl sm:text-4xl drop-shadow-md select-none">
                                                {{ strtoupper(substr($selectedPatient->name, 0, 1)) }}
                                            </span>
                                        </div>

                                        <div class="min-w-0">
                                            <h2
                                                class="text-2xl sm:text-3xl font-black text-black tracking-tight leading-tight truncate">
                                                {{ $selectedPatient->name }}
                                            </h2>
                                            <div
                                                class="text-blue-200 text-sm sm:text-base mt-2 flex flex-wrap items-center gap-2 sm:gap-3 font-medium">
                                                <span
                                                    class="bg-slate-800 px-2 py-0.5 rounded text-xs sm:text-sm border border-slate-700">{{ $selectedPatient->age_string }}</span>
                                                <span class="text-blue-200/80">•</span>
                                                <span
                                                    class="text-black truncate">{{ $selectedPatient->healthInsurance->name ?? 'Particular' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <button wire:click="closeHistory"
                                        class="text-gray-300 hover:text-white bg-white/10 hover:bg-white/20 p-2 rounded-full transition backdrop-blur-md shrink-0">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="mt-6 sm:mt-8 flex flex-wrap gap-3 relative z-20">
                                    @if ($selectedPatient->user && $selectedPatient->user->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $selectedPatient->user->phone) }}"
                                            target="_blank"
                                            class="flex items-center gap-2 bg-[#25D366] hover:bg-[#20bd5a] text-black px-4 sm:px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-lg hover:-translate-y-0.5 transform">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                            </svg>
                                            WhatsApp
                                        </a>
                                    @endif

                                    <button wire:click="setTab('edit')"
                                        class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-black border border-white/20 px-4 sm:px-5 py-2.5 rounded-xl text-sm font-bold transition shadow hover:shadow-lg backdrop-blur-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Editar Datos
                                    </button>
                                </div>
                            </div>

                            {{-- B. TABS DE NAVEGACIÓN (TAMAÑO IGUAL) --}}
                            {{-- Mejora mobile: sticky + padding y texto más cómodo --}}
                            <div class="border-b border-gray-200 bg-white sticky top-0 z-30">
                                <nav class="flex w-full">
                                    <button wire:click="setTab('overview')"
                                        class="flex-1 py-3 sm:py-4 text-center text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                        Resumen
                                    </button>
                                    <button wire:click="setTab('history')"
                                        class="flex-1 py-3 sm:py-4 text-center text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'history' ? 'border-blue-500 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                        Historial
                                    </button>
                                    <button wire:click="setTab('files')"
                                        class="flex-1 py-3 sm:py-4 text-center text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'files' ? 'border-blue-500 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                        Archivos <span
                                            class="ml-1 bg-gray-200 text-gray-600 py-0.5 px-1.5 rounded-full text-[10px]">{{ count($patientFiles) }}</span>
                                    </button>
                                    <button wire:click="setTab('edit')"
                                        class="flex-1 py-3 sm:py-4 text-center text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'edit' ? 'border-blue-500 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                        Editar
                                    </button>
                                </nav>
                            </div>

                            {{-- C. CONTENIDO PRINCIPAL --}}
                            {{-- Ajuste clave: padding responsive + extra padding inferior para que NO “se corte” en mobile --}}
                            <div class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-8 pb-28 sm:pb-8">

                                {{-- TAB: RESUMEN --}}
                                @if ($activeTab === 'overview')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">

                                        {{-- Ajuste clave: en mobile NO 3 columnas, pasa a 1 y luego a 3 en sm --}}
                                        <div
                                            class="col-span-1 md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                                            <div
                                                class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-sm text-center">
                                                <span
                                                    class="block text-2xl sm:text-3xl font-black text-gray-800">{{ $stats['total'] }}</span>
                                                <span
                                                    class="text-xs font-bold text-gray-400 uppercase tracking-widest">Visitas</span>
                                            </div>
                                            <div
                                                class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-sm text-center">
                                                <span
                                                    class="block text-2xl sm:text-3xl font-black text-green-600">{{ $stats['rate'] }}%</span>
                                                <span
                                                    class="text-xs font-bold text-gray-400 uppercase tracking-widest">Asistencia</span>
                                            </div>
                                            <div
                                                class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-sm text-center">
                                                <span
                                                    class="block text-2xl sm:text-3xl font-black text-red-500">{{ $stats['cancelled'] }}</span>
                                                <span
                                                    class="text-xs font-bold text-gray-400 uppercase tracking-widest">Cancelados</span>
                                            </div>
                                        </div>

                                        <div class="bg-white p-5 sm:p-6 rounded-xl border border-gray-200 shadow-sm">
                                            <h4 class="text-xs font-bold text-gray-400 uppercase mb-4 tracking-wider">
                                                Próxima Visita</h4>
                                            @if ($nextAppointment)
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="bg-blue-50 text-blue-600 p-3 rounded-xl font-bold text-center leading-tight border border-blue-100">
                                                        <span
                                                            class="block text-xs uppercase">{{ $nextAppointment->start_time->isoFormat('MMM') }}</span>
                                                        <span
                                                            class="block text-2xl">{{ $nextAppointment->start_time->format('d') }}</span>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p
                                                            class="text-base sm:text-lg font-bold text-gray-900 break-words">
                                                            {{ $nextAppointment->start_time->isoFormat('dddd H:i') }}
                                                            hs</p>
                                                        <span
                                                            class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded font-bold mt-1">
                                                            {{ $nextAppointment->type->name }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <div
                                                    class="text-center py-4 border-2 border-dashed border-gray-100 rounded-lg">
                                                    <p class="text-sm text-gray-400 italic">No hay turnos futuros.</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div
                                            class="bg-amber-50 p-5 sm:p-6 rounded-xl border border-amber-200 shadow-sm">
                                            <h4
                                                class="text-xs font-bold text-amber-600 uppercase mb-2 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Notas Internas
                                            </h4>
                                            <textarea wire:model="admin_notes" wire:blur="saveNote" rows="3"
                                                class="w-full text-sm bg-white border-amber-200 focus:ring-amber-400 focus:border-amber-400 rounded-lg placeholder-gray-400 text-gray-700 resize-none shadow-sm"
                                                placeholder="Notas privadas..."></textarea>
                                            <p class="text-[10px] text-amber-600 mt-2 font-medium">Se guarda
                                                automáticamente.</p>
                                        </div>

                                        @if ($selectedPatient->medical_alerts)
                                            <div
                                                class="col-span-1 md:col-span-2 bg-red-50 p-5 rounded-xl border border-red-200 shadow-sm flex gap-4 items-start">
                                                <div class="bg-red-100 p-2 rounded-full text-red-600">
                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-bold text-red-700 uppercase mb-1">Alerta
                                                        Médica</h4>
                                                    <p class="text-red-900 text-base font-medium">
                                                        {{ $selectedPatient->medical_alerts }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- TAB: HISTORIAL --}}
                                @if ($activeTab === 'history')
                                    <div class="space-y-8">
                                        @if (count($history) > 0)
                                            <div class="relative pl-6 border-l-2 border-gray-200 space-y-8 ml-2">
                                                @foreach ($history as $app)
                                                    <div class="relative group">
                                                        <div
                                                            class="absolute -left-[31px] top-4 h-4 w-4 rounded-full border-4 border-white shadow-sm
                                                        @if ($app->status == 'completed') bg-green-500
                                                        @elseif($app->status == 'cancelled') bg-red-500
                                                        @else bg-blue-500 @endif">
                                                        </div>

                                                        <div
                                                            class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition">
                                                            <div class="flex justify-between items-start mb-3 gap-3">
                                                                <div class="min-w-0">
                                                                    <h4
                                                                        class="font-bold text-gray-900 text-base sm:text-lg truncate">
                                                                        {{ $app->type->name }}</h4>
                                                                    <span
                                                                        class="text-sm text-gray-500 font-mono">{{ $app->start_time->locale('es')->isoFormat('D [de] MMMM, YYYY') }}</span>
                                                                </div>
                                                                <div
                                                                    class="text-xs font-bold uppercase tracking-wide px-2 py-1 rounded shrink-0
                                                                @if ($app->status == 'completed') bg-green-100 text-green-700
                                                                @elseif($app->status == 'cancelled') bg-red-100 text-red-700
                                                                @else bg-blue-100 text-blue-700 @endif">
                                                                    {{ $app->status }}
                                                                </div>
                                                            </div>

                                                            @if ($app->doctor_notes)
                                                                <div
                                                                    class="bg-gray-50 p-4 rounded-lg text-sm text-gray-800 leading-relaxed border border-gray-100 relative">
                                                                    <span
                                                                        class="absolute top-2 left-2 text-gray-300 text-4xl leading-none opacity-50">"</span>
                                                                    <p class="relative z-10 pl-2">
                                                                        {{ $app->doctor_notes }}</p>
                                                                </div>
                                                            @else
                                                                <p class="text-sm text-gray-400 italic pl-1">Sin notas
                                                                    médicas.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div
                                                class="text-center py-12 border-2 border-dashed border-gray-200 rounded-xl">
                                                <p class="text-gray-500">No hay historial de visitas.</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- TAB: ARCHIVOS --}}
                                @if ($activeTab === 'files')
                                    <div class="space-y-8">
                                        <div
                                            class="bg-white p-6 rounded-xl border-2 border-dashed border-gray-300 hover:border-blue-400 transition-colors">
                                            <h4 class="text-base font-bold text-gray-800 mb-4">Adjuntar documento nuevo
                                            </h4>
                                            <form wire:submit.prevent="uploadFile"
                                                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                                                <div class="flex-1">
                                                    <input type="file" wire:model="newFile"
                                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" />
                                                </div>
                                                <button type="submit" wire:loading.attr="disabled"
                                                    class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-blue-700 transition shadow">Subir</button>
                                            </form>
                                            <div wire:loading wire:target="newFile"
                                                class="text-xs text-blue-500 font-bold mt-2">Cargando...</div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            @forelse($patientFiles as $file)
                                                <div
                                                    class="flex items-center justify-between bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
                                                    <div class="flex items-center gap-4 overflow-hidden">
                                                        <div class="bg-blue-50 p-3 rounded-lg text-blue-600">
                                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                        </div>
                                                        <div class="truncate">
                                                            <a href="{{ Storage::url($file->file_path) }}"
                                                                target="_blank"
                                                                class="block truncate text-sm font-bold text-gray-800 hover:text-blue-600 hover:underline">{{ $file->original_name }}</a>
                                                            <span
                                                                class="text-xs text-gray-400 uppercase">{{ $file->created_at->format('d/m/Y') }}</span>
                                                        </div>
                                                    </div>
                                                    <button wire:click="deleteFile({{ $file->id }})"
                                                        class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition"><svg
                                                            class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg></button>
                                                </div>
                                            @empty
                                                <div class="col-span-2 text-center text-gray-400 text-sm py-8 italic">
                                                    No hay archivos.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endif

                                {{-- TAB: EDITAR --}}
                                @if ($activeTab === 'edit')
                                    <div
                                        class="bg-white p-6 sm:p-8 rounded-xl border border-gray-200 shadow-sm max-w-2xl mx-auto">
                                        <form wire:submit.prevent="updatePatient" class="space-y-6">
                                            <div><label
                                                    class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Nombre</label><input
                                                    type="text" wire:model="edit_name"
                                                    class="w-full text-base border-gray-300 rounded-xl p-3"></div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                                <div><label
                                                        class="block text-xs font-bold text-gray-500 uppercase mb-1.5">DNI</label><input
                                                        type="text" wire:model="edit_dni"
                                                        class="w-full text-base border-gray-300 rounded-xl p-3"></div>
                                                <div><label
                                                        class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Afiliado</label><input
                                                        type="text" wire:model="edit_affiliate"
                                                        class="w-full text-base border-gray-300 rounded-xl p-3"></div>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Obra
                                                    Social</label>
                                                <select wire:model="edit_insurance_id"
                                                    class="w-full text-base border-gray-300 rounded-xl p-3 bg-white">
                                                    <option value="">Particular</option>
                                                    @foreach ($insurances as $ins)
                                                        <option value="{{ $ins->id }}">{{ $ins->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div><label
                                                    class="block text-xs font-bold text-gray-500 uppercase mb-1.5 text-red-500">Alertas</label>
                                                <textarea wire:model="edit_alert" rows="3" class="w-full text-base border-gray-300 rounded-xl p-3"></textarea>
                                            </div>
                                            <button type="submit"
                                                class="w-full bg-blue-600 text-white py-3.5 rounded-xl font-bold text-base hover:bg-blue-700 shadow-lg">Guardar
                                                Cambios</button>
                                        </form>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
