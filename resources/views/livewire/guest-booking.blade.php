<div class="min-h-screen bg-gray-50 font-sans text-gray-900">

    <nav class="border-b shadow-sm sticky top-0 z-50" style="background-color:#1f1f21; border-color:#2867e5;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- LOGO / NOMBRE --}}
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl" style="background-color:#2563eb;">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="#ffffff" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 12.502l-3.623-1.137a.75.75 0 0 0-.806.24l-1.713 2.212a.75.75 0 0 1-.965.19
                                 c-1.992-1.022-3.619-2.65-4.64-4.642a.75.75 0 0 1 .19-.964l2.211-1.714a.749.749 0 0 0 .24-.805
                                 l-1.137-3.623a.75.75 0 0 0-.906-.524l-4.281.823A.75.75 0 0 0 3.75 3.5
                                 c0 10.355 8.395 18.75 18.75 18.75a.75.75 0 0 0 .743-.599l.823-4.281a.75.75 0 0 0-.523-.906
                                 l-3.623-1.137z" />
                        </svg>
                    </div>

                    <span class="font-bold text-xl" style="color:#cbcdd1;">
                        {{ $businessName ?? 'Consultorio' }}
                    </span>
                </div>

                {{-- ACCIONES --}}
                <div>
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border transition"
                            style="
                           background-color:#f9fafb;
                           color:#374151;
                           border-color:#d1d5db;
                       "
                            onmouseover="this.style.backgroundColor='#f3f4f6'"
                            onmouseout="this.style.backgroundColor='#f9fafb'">
                            <svg class="w-4 h-4" fill="none" stroke="#6b7280" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Volver a Mis Turnos
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-bold shadow transition"
                            style="background-color:#2563eb; color:#ffffff;"
                            onmouseover="this.style.backgroundColor='#1d4ed8'"
                            onmouseout="thisC10`this.style.backgroundColor='#2563eb'">
                            Ingresar
                        </a>
                    @endauth
                </div>

            </div>
        </div>
    </nav>


    <div class="max-w-5xl mx-auto px-4 mt-4 sm:px-6 lg:px-8 py-10">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-4">
                @if ($step == 1)
                    Encuentra tu horario
                @elseif($step == 2)
                    Completa tus datos
                @else
                    ¡Todo listo!
                @endif
            </h1>
            <div class="flex justify-center gap-2">
                <div class="h-1.5 w-12 rounded-full {{ $step >= 1 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                <div class="h-1.5 w-12 rounded-full {{ $step >= 2 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                <div class="h-1.5 w-12 rounded-full {{ $step >= 3 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">

            @if ($step == 1)
                <div class="grid grid-cols-1 lg:grid-cols-12 min-h-[450px]">
                    <div class="lg:col-span-4 bg-gray-50 p-6 border-r border-gray-200">
                        <h3 class="font-bold text-gray-700 mb-6">🔍 Búsqueda</h3>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-2">Especialidad</label>
                                <select wire:model.live="type_id"
                                    class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5">
                                    <option value="">Selecciona...</option>
                                    @foreach ($types as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($type_id)
                                <div>
                                    <label class="block text-sm font-bold text-gray-600 mb-2">Fecha</label>
                                    <input type="date" wire:model.live="selectedDate" min="{{ date('Y-m-d') }}"
                                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm py-2.5">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="lg:col-span-8 p-6 lg:p-8">
                        <h3 class="font-bold text-gray-800 mb-6">Horarios Disponibles</h3>
                        @if ($type_id && $selectedDate)
                            @if (count($availableSlots) > 0)
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                    @foreach ($availableSlots as $slot)
                                        <button wire:click="selectSlot('{{ $slot }}')"
                                            class="py-3 px-2 border rounded-lg text-sm font-bold transition hover:shadow-md
                                            {{ $selectedTime === $slot ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-200 hover:border-blue-500 hover:text-blue-600' }}">
                                            {{ $slot }}
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10 text-gray-500 border-2 border-dashed rounded-lg">No hay
                                    turnos disponibles.</div>
                            @endif
                        @else
                            <div class="text-center py-10 text-gray-400 border-2 border-dashed rounded-lg">Selecciona
                                especialidad y fecha.</div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($step == 2)
                <div>
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Reserva para el <span
                                class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($selectedDate)->format('d/m') }}</span>
                            a las <span class="font-bold text-gray-900">{{ $selectedTime }} hs</span>
                        </div>
                        <button wire:click="back"
                            class="text-sm font-bold text-blue-600 hover:underline">Cambiar</button>
                    </div>

                    @if (session()->has('auto_msg'))
                        <div
                            class="bg-blue-50 p-3 text-center text-blue-700 text-sm font-bold border-b border-blue-100">
                            {{ session('auto_msg') }}</div>
                    @endif

                    <div class="p-6 lg:p-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <h3 class="font-bold text-gray-800 border-b pb-2">1. Datos Responsable</h3>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">Nombre Completo</label>
                                <input type="text" wire:model="parent_name"
                                    class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-2.5">
                                @error('parent_name')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-600 mb-1">DNI</label>
                                    <input type="text" wire:model="parent_dni"
                                        class="w-full border-gray-300 rounded-lg py-2.5">
                                    @error('parent_dni')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-600 mb-1">Celular</label>
                                    <input type="text" wire:model="parent_phone"
                                        class="w-full border-gray-300 rounded-lg py-2.5">
                                    @error('parent_phone')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">Email</label>
                                <input type="email" wire:model="parent_email"
                                    class="w-full border-gray-300 rounded-lg py-2.5 {{ auth()->check() ? 'bg-gray-100' : '' }}"
                                    {{ auth()->check() ? 'readonly' : '' }}>
                                @error('parent_email')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="font-bold text-gray-800 border-b pb-2">2. Datos Paciente</h3>

                            @auth
                                @if (count($existingPatients) > 0)
                                    <div class="mb-4">
                                        <div class="flex flex-wrap gap-2">
                                            <label
                                                class="cursor-pointer border px-3 py-1 rounded bg-white text-sm hover:bg-gray-50">
                                                <input type="radio" wire:model.live="selected_patient_id" value="new"
                                                    class="mr-1"> + Nuevo
                                            </label>
                                            @foreach ($existingPatients as $p)
                                                <label
                                                    class="cursor-pointer border px-3 py-1 rounded bg-white text-sm hover:bg-gray-50">
                                                    <input type="radio" wire:model.live="selected_patient_id"
                                                        value="{{ $p->id }}" class="mr-1">
                                                    {{ explode(' ', $p->name)[0] }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endauth

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">Nombre Paciente</label>
                                <input type="text" wire:model="child_name"
                                    class="w-full border-gray-300 rounded-lg py-2.5">
                                @error('child_name')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-600 mb-1">Nacimiento</label>
                                    <input type="date" wire:model="child_dob" max="{{ date('Y-m-d') }}"
                                        class="w-full border-gray-300 rounded-lg py-2.5">
                                    @error('child_dob')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-600 mb-1">Obra Social</label>
                                    <select wire:model="child_insurance_id"
                                        class="w-full border-gray-300 rounded-lg py-2.5">
                                        <option value="">Particular</option>
                                        @foreach ($insurances as $os)
                                            <option value="{{ $os->id }}">{{ $os->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-gray-50 border-t border-gray-200">
                        <button wire:click="confirmBooking" wire:loading.attr="disabled"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-lg shadow transition text-lg flex justify-center items-center gap-2">
                            <span wire:loading.remove>CONFIRMAR RESERVA</span>
                            <span wire:loading>Procesando...</span>
                        </button>
                    </div>
                </div>
            @endif

            @if ($step == 3)
                <div class="text-center py-16 px-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">¡Turno Confirmado!</h2>
                    <p class="text-gray-600 mb-8">
                        Te esperamos el
                        <strong>{{ \Carbon\Carbon::parse($selectedDate)->locale('es')->isoFormat('dddd D [de] MMMM') }}</strong>
                        a las <strong>{{ $selectedTime }} hs</strong>.
                    </p>

                    <div class="flex justify-center gap-4">
                        <button wire:click="bookNewAppointmentForFamily"
                            class="px-5 py-2.5 border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50">
                            Otro turno
                        </button>
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-5 py-2.5 bg-gray-900 text-white font-bold rounded-lg hover:bg-gray-800">
                                Ir a Mis Turnos
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">
                                Finalizar
                            </a>
                        @endauth
                    </div>
                </div>
            @endif

        </div>

        <p class="text-center text-gray-400 text-xs mt-8">&copy; {{ date('Y') }}
            {{ $businessName ?? 'Consultorio' }}</p>
    </div>
</div>
