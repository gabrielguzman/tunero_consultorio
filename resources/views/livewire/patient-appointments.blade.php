<div class="min-h-screen bg-gray-50 py-8 sm:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        <div class="sm:flex sm:items-center sm:justify-between border-b border-gray-200 pb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    Mis Turnos
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Gestiona las citas médicas de tus hijos.
                </p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <a href="{{ route('patient.booking') }}" 
                   class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-all hover:-translate-y-0.5">
                    <svg class="-ml-0.5 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                    Reservar Nuevo Turno
                </a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="rounded-xl bg-green-50 p-4 border-l-4 border-green-500 animate-fade-in-up shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <section>
            <div class="flex items-center gap-2 mb-6">
                <div class="h-8 w-1 bg-blue-600 rounded-full"></div>
                <h2 class="text-xl font-bold text-gray-900">Próximas Citas</h2>
            </div>

            @if($upcomingAppointments->count() > 0)
                <div class="space-y-4">
                    @foreach($upcomingAppointments as $app)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-200">
                            <div class="flex flex-col sm:flex-row">
                                <div class="sm:w-32 bg-blue-50/50 flex flex-col items-center justify-center p-4 border-b sm:border-b-0 sm:border-r border-gray-100">
                                    <span class="text-xs font-bold uppercase text-blue-600 tracking-wider">{{ $app->start_time->isoFormat('MMMM') }}</span>
                                    <span class="text-3xl font-extrabold text-gray-900">{{ $app->start_time->format('d') }}</span>
                                    <span class="text-sm font-medium text-gray-500">{{ $app->start_time->isoFormat('dddd') }}</span>
                                </div>
                                <div class="flex-1 p-5 flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                            {{ $app->patient->name }}
                                            <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-md">{{ $app->start_time->format('H:i') }} hs</span>
                                        </h3>
                                        <p class="text-gray-600 mt-1">{{ $app->type->name }}</p>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                                        <button wire:click="cancelAppointment({{ $app->id }})" 
                                                wire:confirm="¿Seguro deseas cancelar el turno?"
                                                class="text-sm text-red-600 hover:text-red-800 font-medium hover:underline">
                                            Cancelar Turno
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                    <p class="text-gray-500">No tienes turnos próximos agendados.</p>
                </div>
            @endif
        </section>

        @if($pastAppointments->count() > 0)
        <section class="pt-8 border-t border-gray-200">
            <h2 class="text-lg font-bold text-gray-700 mb-6">Historial</h2>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <ul class="divide-y divide-gray-100">
                    @foreach($pastAppointments as $app)
                        <li class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $app->patient->name }}</p>
                                <p class="text-sm text-gray-500">{{ $app->type->name }} • {{ $app->start_time->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                @if($app->status == 'completed')
                                    <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-bold rounded">Realizado</span>
                                @elseif($app->status == 'cancelled')
                                    <span class="px-2 py-1 bg-red-50 text-red-700 text-xs font-bold rounded">Cancelado</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded">Pasado</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
        @endif
    </div>
</div>