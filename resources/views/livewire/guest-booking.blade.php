<div>

    <nav class="border-b border-gray-800 sticky top-0 z-50 shadow-lg" style="background-color: #111827;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center gap-3">
                        <a href="{{ route('guest.booking') }}">
                            <svg class="h-9 w-auto text-white fill-current" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24">
                                <path
                                    d="M19.428 12.502l-3.623-1.137a.75.75 0 0 0-.806.24l-1.713 2.212a.75.75 0 0 1-.965.19c-1.992-1.022-3.619-2.65-4.64-4.642a.75.75 0 0 1 .19-.964l2.211-1.714a.749.749 0 0 0 .24-.805l-1.137-3.623a.75.75 0 0 0-.906-.524l-4.281.823A.75.75 0 0 0 3.75 3.5c0 10.355 8.395 18.75 18.75 18.75a.75.75 0 0 0 .743-.599l.823-4.281a.75.75 0 0 0-.523-.906l-3.623-1.137zM6.743 3.253l3.507-.674 1.04 3.313-2.214 1.716A17.333 17.333 0 0 0 15.397 14.93l1.717-2.214 3.313 1.04-.674 3.507a17.217 17.217 0 0 1-13.01-13.01z" />
                                <path
                                    d="M15.75 2.25a.75.75 0 0 1 .75.75v2.25h2.25a.75.75 0 0 1 0 1.5h-2.25v2.25a.75.75 0 0 1-1.5 0v-2.25h-2.25a.75.75 0 0 1 0-1.5h2.25V3a.75.75 0 0 1 .75-.75z" />
                            </svg>
                        </a>
                        <span class="font-bold text-white text-lg hidden sm:block tracking-wide">Consultorio Dra.
                            López</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('login') }}"
                        class="text-sm font-bold text-gray-300 hover:text-white transition px-4 py-2 rounded-lg hover:bg-gray-800 border border-transparent hover:border-gray-600">
                        Soy Paciente (Ingresar)
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-12 bg-slate-200 min-h-[calc(100vh-64px)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-8 text-center sm:text-left">
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Reservar Nuevo Turno</h1>
                <p class="mt-2 text-lg text-gray-600">Seleccione el motivo de la consulta y la fecha deseada.</p>
            </div>

            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-200">

                @if ($step == 1)
                    <div class="grid grid-cols-1 lg:grid-cols-3 animate-fade-in">
                        <div class="p-6 lg:p-8 bg-white border-b lg:border-b-0 lg:border-r border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-3">
                                <span
                                    class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shadow-md">1</span>
                                Configuración
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Especialidad /
                                        Motivo</label>
                                    <select wire:model.live="type_id"
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base bg-gray-50 cursor-pointer">
                                        <option value="">Seleccione motivo...</option>
                                        @foreach ($types as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }} —
                                                ${{ number_format($t->price, 0, ',', '.') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($type_id)
                                    <div class="animate-fade-in">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Fecha del
                                            Turno</label>
                                        <input type="date" wire:model.live="selectedDate" min="{{ date('Y-m-d') }}"
                                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 text-base cursor-pointer bg-gray-50">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-span-2 p-6 lg:p-8 bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-3">
                                <span
                                    class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shadow-md">2</span>
                                Horarios Disponibles
                            </h3>

                            @if ($type_id && $selectedDate)
                                @if (count($availableSlots) > 0)
                                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4 animate-fade-in">
                                        @foreach ($availableSlots as $slot)
                                            <button wire:click="selectSlot('{{ $slot }}')"
                                                class="group relative flex items-center justify-center px-4 py-3.5 border-2 text-base font-bold rounded-xl transition-all duration-200
                                                {{ $selectedTime === $slot
                                                    ? 'bg-blue-600 text-white shadow-md transform -translate-y-1 border-transparent ring-2 ring-blue-300 ring-offset-2'
                                                    : 'border-white bg-white text-gray-700 hover:border-blue-400 hover:text-blue-600 hover:shadow-md' }}">
                                                {{ $slot }}
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <div
                                        class="flex flex-col items-center justify-center py-12 border-2 border-dashed border-gray-300 rounded-xl bg-gray-100/50 animate-fade-in">
                                        <p class="text-gray-600 font-bold text-lg">Sin horarios disponibles</p>
                                    </div>
                                @endif
                            @else
                                <div
                                    class="flex flex-col items-center justify-center h-64 bg-white rounded-xl border border-dashed border-gray-300 text-center px-4">
                                    <p class="text-gray-700 font-bold text-lg">¿Cuándo quieres venir?</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($step == 2)
                    <div class="animate-fade-in">
                        @if (session()->has('auto_msg'))
                            <div
                                class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 mx-6 md:mx-8 mt-6 shadow-sm rounded-r">
                                <div class="flex">
                                    <div class="shrink-0">
                                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-green-700 font-bold">
                                            {{ session('auto_msg') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div
                            class="bg-white px-6 py-6 md:px-8 flex flex-col sm:flex-row justify-between items-center border-b border-gray-200">
                            <div class="mb-4 sm:mb-0 text-center sm:text-left">
                                <p class="text-blue-600 text-sm font-bold uppercase tracking-widest mb-1">Confirmando
                                    Turno</p>
                                <h2
                                    class="text-2xl md:text-3xl font-extrabold text-gray-900 flex items-center gap-2 justify-center sm:justify-start">
                                    {{ \Carbon\Carbon::parse($selectedDate)->locale('es')->isoFormat('dddd D [de] MMMM') }}
                                    <span class="font-light text-gray-300 mx-2">|</span>
                                    {{ $selectedTime }} hs
                                </h2>
                            </div>
                            <button wire:click="back"
                                class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-full transition font-bold flex items-center gap-2 border border-gray-200">
                                Cambiar Fecha
                            </button>
                        </div>

                        <div class="p-6 lg:p-10 grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 bg-gray-50/30">

                            <div class="space-y-6 bg-white p-8 rounded-2xl shadow-sm border border-gray-200 relative">
                                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-2">
                                    <span
                                        class="bg-blue-100 text-blue-700 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                                    <h3 class="text-xl font-bold text-gray-900">Datos del Responsable</h3>
                                </div>

                                @if (!empty($parent_name))
                                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 animate-fade-in">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-xs font-bold text-blue-500 uppercase tracking-wide">
                                                    Responsable a cargo</p>
                                                <p class="text-lg font-bold text-gray-800">{{ $parent_name }}</p>
                                                <p class="text-sm text-gray-600">{{ $parent_dni }} •
                                                    {{ $parent_phone }}</p>
                                                <p class="text-sm text-gray-600">{{ $parent_email }}</p>
                                            </div>
                                            <button wire:click="$set('parent_name', '')"
                                                class="text-xs font-bold text-blue-600 hover:text-blue-800 underline">
                                                Editar
                                            </button>
                                        </div>
                                        <div class="mt-3 flex items-center gap-2 text-xs text-blue-600 font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Datos guardados de la reserva anterior
                                        </div>
                                    </div>
                                @else
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre y
                                            Apellido</label>
                                        <input type="text" wire:model="parent_name" placeholder="Ej: Juan Pérez"
                                            class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-3">
                                        @error('parent_name')
                                            <span
                                                class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">DNI</label>
                                            <input type="text" wire:model="parent_dni" placeholder="Sin puntos"
                                                class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-3">
                                            @error('parent_dni')
                                                <span
                                                    class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Celular</label>
                                            <input type="text" wire:model="parent_phone" placeholder="Con WhatsApp"
                                                class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-3">
                                            @error('parent_phone')
                                                <span
                                                    class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                                        <input type="email" wire:model="parent_email"
                                            placeholder="Para enviarte el comprobante"
                                            class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-3">
                                        @error('parent_email')
                                            <span
                                                class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-6 bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-2">
                                    <span
                                        class="bg-blue-100 text-blue-700 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                                    <h3 class="text-xl font-bold text-gray-900">Datos del Paciente</h3>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Niño/a</label>
                                    <input type="text" wire:model="child_name" placeholder="Nombre completo"
                                        class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-3">
                                    @error('child_name')
                                        <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Fecha
                                            Nacimiento</label>
                                        <input type="date" wire:model="child_dob" max="{{ date('Y-m-d') }}"
                                            class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-3 cursor-pointer">
                                        @error('child_dob')
                                            <span
                                                class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Obra Social</label>
                                        <select wire:model="child_insurance_id"
                                            class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-3 text-base">
                                            <option value="">Particular / Ninguna</option>
                                            @foreach ($insurances as $os)
                                                <option value="{{ $os->id }}">{{ $os->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="pt-8">
                                    <button wire:click="confirmBooking" wire:loading.attr="disabled"
                                        class="w-full py-4 bg-blue-600 rounded-xl text-white font-bold text-lg shadow-md hover:bg-blue-700 hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex justify-center items-center gap-3 disabled:opacity-75 disabled:cursor-not-allowed">
                                        <svg wire:loading wire:target="confirmBooking"
                                            class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span>CONFIRMAR RESERVA</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($step == 3)
                    <div class="text-center py-20 animate-fade-in px-4 bg-white">
                        <div
                            class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-green-50 mb-8 shadow-sm border-4 border-green-100">
                            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">¡Turno Confirmado!</h2>
                        <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                            Te esperamos el
                            <strong>{{ \Carbon\Carbon::parse($selectedDate)->locale('es')->isoFormat('dddd D [de] MMMM') }}</strong>
                            a las <strong>{{ $selectedTime }} hs</strong>.
                        </p>
                        <div class="mt-6 bg-blue-50 border border-blue-100 rounded-xl p-4 text-left max-w-lg mx-auto">
                            <div class="flex gap-3">
                                <svg class="w-6 h-6 text-blue-600 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-bold text-blue-900 text-sm uppercase mb-1">Tu cuenta ha sido creada
                                    </h4>
                                    <p class="text-sm text-blue-800 leading-relaxed">
                                        Para gestionar tus turnos o cancelar, podrás ingresar al sistema usando tu
                                        email: <strong>{{ $parent_email }}</strong>.
                                        <br><br>
                                        La primera vez, deberás hacer clic en <strong>"¿Olvidaste tu
                                            contraseña?"</strong> en la pantalla de ingreso para generar una clave
                                        personal.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-center gap-5">
                            <button wire:click="bookNewAppointmentForFamily"
                                class="px-8 py-4 bg-white border-2 border-blue-600 rounded-xl text-blue-700 font-bold hover:bg-blue-50 transition shadow-sm flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Agendar otro hermano/a
                            </button>

                            <a href="{{ route('login') }}"
                                class="px-8 py-4 bg-gray-900 rounded-xl text-white font-bold hover:bg-black transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                Finalizar e Ingresar
                            </a>
                        </div>
                    </div>
                @endif

            </div>

            <div class="text-center mt-10 text-sm text-gray-500 pb-10 font-medium">
                &copy; {{ date('Y') }} Consultorio Pediátrico Dra. López.
            </div>

        </div>
    </div>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(25px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        input[type="date"] {
            min-height: 3rem;
        }
    </style>
</div>
