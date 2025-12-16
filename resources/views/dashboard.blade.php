<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Consultorio Pediátrico — Panel
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Resumen del día, agenda y accesos rápidos.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200">
                    Hoy: {{ now()->format('d/m/Y') }}
                </span>
                <a href="#"
                   class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium shadow-sm">
                    + Nuevo turno
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- TOP: Summary cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Turnos hoy</h3>
                            <p class="mt-2 text-3xl font-semibold text-indigo-600">12</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200">
                            Agenda
                        </span>
                    </div>
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Pacientes agendados para hoy.</p>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pacientes nuevos (mes)</h3>
                            <p class="mt-2 text-3xl font-semibold text-green-600">8</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-200">
                            Altas
                        </span>
                    </div>
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Últimos 30 días.</p>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Urgencias</h3>
                            <p class="mt-2 text-3xl font-semibold text-red-600">1</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-200">
                            Prioridad
                        </span>
                    </div>
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Casos marcados como urgentes.</p>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Vacunas pendientes</h3>
                            <p class="mt-2 text-3xl font-semibold text-amber-600">3</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
                            Control
                        </span>
                    </div>
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Pendientes a revisar hoy.</p>
                </div>

            </div>

            {{-- MAIN: Agenda + Sidebar --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Agenda --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-100 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Agenda de hoy</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Turnos próximos y estado.</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button class="px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200">
                                Día
                            </button>
                            <button class="px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200">
                                Semana
                            </button>
                            <button class="px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200">
                                Mes
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        {{-- Lista base (después la reemplazás por foreach con turnos reales) --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-start gap-4">
                                    <div class="text-sm">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">09:00</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">30 min</p>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">Ana García <span class="text-gray-500 dark:text-gray-400">(2 años)</span></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Control sano · Peso/Talla</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-200">
                                    Confirmado
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-start gap-4">
                                    <div class="text-sm">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">09:30</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">30 min</p>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">Mateo Pérez <span class="text-gray-500 dark:text-gray-400">(6 meses)</span></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Vacunas · Calendario</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
                                    Pendiente
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-start gap-4">
                                    <div class="text-sm">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">10:00</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">30 min</p>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">Lucía Torres <span class="text-gray-500 dark:text-gray-400">(4 años)</span></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Fiebre · Consulta</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-200">
                                    Urgente
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-2">
                            <a href="#" class="px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200">
                                Ver agenda completa
                            </a>
                            <a href="#" class="px-3 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-sm text-white shadow-sm">
                                Registrar atención
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">

                    {{-- Quick actions --}}
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Acciones rápidas</h3>
                        <div class="mt-4 grid grid-cols-1 gap-3">
                            <a href="#" class="px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white text-center text-sm font-medium">
                                Registrar paciente
                            </a>
                            <a href="#" class="px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white text-center text-sm font-medium">
                                Agregar turno
                            </a>
                            <a href="#" class="px-4 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 text-center text-sm font-medium">
                                Buscar paciente
                            </a>
                        </div>
                    </div>

                    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <livewire:calendar />

                </div>
            </div>
        </div>
    </div>

                    {{-- Últimos pacientes --}}
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Últimos pacientes</h3>
                            <a href="#" class="text-sm text-indigo-600 hover:underline">Ver todos</a>
                        </div>

                        <ul class="mt-4 space-y-3 text-sm">
                            <li class="flex items-center justify-between">
                                <span class="text-gray-900 dark:text-gray-100">Ana García (2 años)</span>
                                <span class="text-gray-500 dark:text-gray-400">09:00</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span class="text-gray-900 dark:text-gray-100">Mateo Pérez (6 meses)</span>
                                <span class="text-gray-500 dark:text-gray-400">09:30</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span class="text-gray-900 dark:text-gray-100">Lucía Torres (4 años)</span>
                                <span class="text-gray-500 dark:text-gray-400">10:00</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Contacto --}}
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Contacto</h3>
                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-300 space-y-1">
                            <p><span class="font-medium text-gray-800 dark:text-gray-200">Tel:</span> +54 9 11 1234-5678</p>
                            <p><span class="font-medium text-gray-800 dark:text-gray-200">Dirección:</span> Av. Ejemplo 123, Ciudad</p>
                            <p><span class="font-medium text-gray-800 dark:text-gray-200">Horario:</span> Lun a Vie · 08:00 a 18:00</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Avisos / Tips --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Avisos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Recordatorios internos del consultorio.</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        Actualizado hoy
                    </span>
                </div>

                <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <li class="flex gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-amber-500"></span>
                        <span>Recordar vacunas pendientes según edad (ver ficha del paciente).</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500"></span>
                        <span>Control de crecimiento disponible: percentiles en la historia clínica.</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-red-500"></span>
                        <span>Urgencias pediátricas: +54 9 11 8765-4321.</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>
