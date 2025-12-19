<div class="min-h-screen bg-gray-50 py-8 sm:py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <div class="sm:flex sm:items-center sm:justify-between border-b border-gray-200 pb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Mis Hijos</h1>
                <p class="mt-2 text-sm text-gray-600">Registra a tus hijos para poder sacarles turno.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button wire:click="create"
                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all hover:-translate-y-0.5">
                    <svg class="-ml-0.5 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Agregar Nuevo
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="rounded-xl bg-green-50 p-4 border-l-4 border-green-500 animate-fade-in-up">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($patients->count() > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($patients as $p)
                    <div
                        class="group relative flex flex-col bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-200">

                        <div class="p-6 flex-1">
                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="h-12 w-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl">
                                    {{ substr($p->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $p->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $p->age_string }}</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Obra Social:</span>
                                    <span
                                        class="font-medium text-gray-900">{{ $p->healthInsurance->name ?? 'Particular' }}</span>
                                </div>
                                @if ($p->affiliate_number)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500">Nº Afiliado:</span>
                                        <span
                                            class="font-mono text-gray-700 bg-gray-50 px-1.5 rounded">{{ $p->affiliate_number }}</span>
                                    </div>
                                @endif
                            </div>

                            @if ($p->medical_alerts)
                                <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-lg">
                                    <p class="text-xs font-bold text-red-800 uppercase mb-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                        Alerta Médica
                                    </p>
                                    <p class="text-xs text-red-700 leading-relaxed">{{ $p->medical_alerts }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="flex border-t border-gray-100 divide-x divide-gray-100 bg-gray-50">
                            <button wire:click="edit({{ $p->id }})"
                                class="flex-1 py-3 text-sm font-medium text-gray-700 hover:bg-white hover:text-blue-600 transition-colors">
                                Editar
                            </button>
                            <button wire:click="delete({{ $p->id }})"
                                wire:confirm="¿Estás seguro de eliminar a {{ $p->name }}?"
                                class="flex-1 py-3 text-sm font-medium text-gray-700 hover:bg-white hover:text-red-600 transition-colors">
                                Eliminar
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Aún no has registrado a tus hijos</h3>
                <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">
                    Carga los datos de tus hijos para comenzar a reservar turnos con la Doctora.
                </p>
                <div class="mt-6">
                    <button wire:click="create"
                        class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                        Agregar el primero
                    </button>
                </div>
            </div>
        @endif

        @if ($isModalOpen)
            <div
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm px-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
                    <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-white font-bold text-lg">
                            {{ $isEditing ? 'Editar Datos' : 'Registrar Hijo/a' }}
                        </h3>
                        <button wire:click="closeModal" class="text-blue-100 hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="p-6 space-y-5">

                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nombre Completo</label>
                                <input type="text" wire:model="name"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Ej: Pedrito Lopez">
                                @error('name')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1">DNI (Sin puntos)</label>
                                <input type="text" wire:model="dni"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="12345678">
                                @error('dni')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 flex justify-between items-center">
                                Fecha de Nacimiento
                                @if ($this->calculatedAge)
                                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                                        Edad actual: {{ $this->calculatedAge }}
                                    </span>
                                @endif
                            </label>
                            <input type="date" wire:model.live="birth_date" max="{{ date('Y-m-d') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('birth_date')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Obra Social</label>
                                <select wire:model="health_insurance_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Particular / Ninguna</option>
                                    @foreach ($insurances as $os)
                                        <option value="{{ $os->id }}">{{ $os->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nº Afiliado
                                    (Opcional)</label>
                                <input type="text" wire:model="affiliate_number"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="xxx-xxx">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 flex justify-between">
                                Alertas Médicas
                                <span class="text-xs font-normal text-gray-400">Opcional</span>
                            </label>
                            <textarea wire:model="medical_alerts" rows="2"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 placeholder:text-gray-400"
                                placeholder="Ej: Alérgico a la penicilina, Asmático..."></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                                {{ $isEditing ? 'Guardar Cambios' : 'Registrar Ahora' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</div>
