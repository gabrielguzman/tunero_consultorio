<div class="py-12 bg-gray-50 min-h-screen font-sans">
    <style>
        /* Resaltar el total del día */
        .totaldia {
            color: black
        }
    </style>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Caja Diaria</h2>
                <p class="text-sm text-gray-500">Control de ingresos y métodos de pago.</p>
            </div>

            <div class="flex items-center gap-2 bg-white p-2 rounded-2xl border border-gray-200 shadow-sm">
                <span class="text-xs font-bold text-gray-500 uppercase pl-2">Fecha:</span>
                <input type="date" wire:model.live="date_filter"
                    class="border-none bg-transparent text-sm font-semibold text-gray-800 focus:ring-0 rounded-xl cursor-pointer px-2 py-1">
            </div>
        </div>

        {{-- RESUMEN --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- TOTAL --}}
            <div
                class="bg-gray-900 p-5 rounded-2xl shadow-lg flex flex-col justify-between text-white relative overflow-hidden">
                <div class="relative z-10">
                    {{-- antes decía text-black-800 (no existe) y quedaba raro --}}
                    <p class="text-xs font-bold totaldia text-gray-200 uppercase tracking-widest">Total del Día</p>
                    {{-- antes tenías text-black en fondo oscuro (ilegible) --}}
                    <h3 class="text-3xl totaldia text-white font-black mt-1">
                        ${{ number_format($summary['total'], 0, ',', '.') }}
                    </h3>
                </div>

                <div class="absolute right-0 top-0 p-4 opacity-10">
                    {{-- ícono con currentColor: acá conviene blanco para que se vea suave --}}
                    <svg class="w-16 h-16 totaldia text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            {{-- EFECTIVO --}}
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-green-50 text-green-700 rounded-lg border border-green-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Efectivo</p>
                </div>
                <h3 class="text-2xl font-black text-gray-900">${{ number_format($summary['cash'], 0, ',', '.') }}</h3>
            </div>

            {{-- TRANSFERENCIA --}}
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-blue-50 text-blue-700 rounded-lg border border-blue-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Transferencia</p>
                </div>
                <h3 class="text-2xl font-black text-gray-900">${{ number_format($summary['transfer'], 0, ',', '.') }}
                </h3>
            </div>

            {{-- OBRA SOCIAL --}}
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-purple-50 text-purple-700 rounded-lg border border-purple-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Obra Social</p>
                </div>
                <h3 class="text-2xl font-black text-gray-900">${{ number_format($summary['insurance'], 0, ',', '.') }}
                </h3>
            </div>
        </div>

        {{-- TABLA --}}
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 font-bold">Hora</th>
                            <th class="px-6 py-4 font-bold">Paciente</th>
                            <th class="px-6 py-4 font-bold">Tratamiento</th>
                            <th class="px-6 py-4 font-bold">Estado Pago</th>
                            <th class="px-6 py-4 text-right font-bold">Monto</th>
                            <th class="px-6 py-4 text-right font-bold">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($appointments as $app)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 font-mono text-gray-600">
                                    {{ $app->start_time->format('H:i') }}
                                </td>

                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    {{ $app->patient->name }}
                                </td>

                                <td class="px-6 py-4 text-gray-700">
                                    {{ $app->type->name }}
                                </td>

                                <td class="px-6 py-4">
                                    @if ($app->paid_amount > 0)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-800 border border-green-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                            {{ ucfirst($app->payment_method) }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                            Pendiente
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right font-mono font-bold text-gray-900">
                                    @if ($app->paid_amount)
                                        ${{ number_format($app->paid_amount, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    {{-- botón link con buen contraste y focus --}}
                                    <button wire:click="openPaymentModal({{ $app->id }})"
                                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg font-bold
                                               text-blue-700 hover:text-blue-900 hover:bg-blue-50
                                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                               transition">
                                        {{ $app->paid_amount > 0 ? 'Editar' : 'Cobrar' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                    No hay turnos registrados en esta fecha.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- MODAL DE COBRO --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 bg-gray-900/50 transition-opacity" wire:click="$set('showModal', false)">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>

                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-black text-gray-900" id="modal-title">Registrar
                                    Cobro</h3>

                                <div class="mt-2">
                                    <p class="text-sm text-gray-600 mb-4">
                                        Paciente:
                                        <span class="font-bold text-gray-900">
                                            {{ $selectedAppointment->patient->name }}
                                        </span>
                                    </p>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Monto ($)</label>
                                            <input type="number" wire:model="amount"
                                                class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-lg font-bold text-gray-900"
                                                placeholder="0">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Método de
                                                Pago</label>
                                            <select wire:model="method"
                                                class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-gray-900">
                                                <option value="efectivo">Efectivo</option>
                                                <option value="transferencia">Transferencia</option>
                                                <option value="tarjeta">Tarjeta Débito/Crédito</option>
                                                <option value="obra_social">Obra Social (Bonos)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">

                        <button type="button" wire:click="savePayment"
                            class="w-full sm:flex-1 inline-flex justify-center rounded-xl px-4 py-2
               bg-blue-600 text-sm font-bold text-white
               hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Confirmar Cobro
                        </button>

                        <button type="button" wire:click="$set('showModal', false)"
                            class="w-full sm:flex-1 inline-flex justify-center rounded-xl px-4 py-2
               bg-red-600 text-sm font-bold text-white
               hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Cancelar
                        </button>

                    </div>
                </div>

            </div>
        </div>
</div>
@endif
</div>
