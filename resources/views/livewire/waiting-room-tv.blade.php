<div class="h-full w-full flex bg-gray-900 text-white" 
     wire:poll.5s="refreshData"
     x-data="{ 
        audio: new Audio('/sounds/ding-dong.mp3'),
        playAudio() {
            this.audio.currentTime = 0;
            this.audio.play().catch(e => console.log('Click necesario para audio'));
        }
     }"
     @play-sound.window="playAudio()">

    {{-- SECCIÓN IZQUIERDA: VIDEO (Ocupa el espacio restante: flex-1) --}}
    <div class="flex-1 relative bg-black h-full">
        {{-- El iframe usa absolute inset-0 para forzar el tamaño completo --}}
        <iframe class="absolute inset-0 w-full h-full object-cover" 
        src="https://www.youtube.com/embed/L_LUpnjgPso?autoplay=1&mute=1&controls=0&loop=1&playlist=L_LUpnjgPso&rel=0" 
        frameborder="0" 
        allow="autoplay; encrypted-media">
</iframe>
        
        {{-- Zócalo inferior --}}
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-black/80 to-transparent p-8">
            <h1 class="text-3xl font-bold text-white mb-2">
                Consultorio Médico Dra. Gomez
            </h1>
            <p class="text-xl text-gray-300 animate-pulse">
                👋 Por favor, anuncie su llegada en recepción.
            </p>
        </div>
    </div>

    {{-- SECCIÓN DERECHA: LISTA (Ancho fijo: w-96 o w-1/3) --}}
    <div class="w-[400px] flex flex-col h-full bg-gray-800 border-l border-gray-700 shadow-2xl z-10">
        
        {{-- Cabecera de la lista --}}
        <div class="bg-blue-600 p-6 text-center shrink-0">
            <h2 class="text-2xl font-black uppercase tracking-widest text-white">Turnos</h2>
            <p class="text-blue-100 font-medium">{{ date('d/m/Y') }}</p>
        </div>

        {{-- Cuerpo de la lista (Scrollable) --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            @forelse($upcomingList as $appt)
                <div class="bg-gray-700 rounded-xl p-4 flex items-center gap-4 border-l-4 border-green-500 shadow-lg transform transition hover:scale-105">
                    <div class="bg-gray-600 h-12 w-12 rounded-full flex items-center justify-center font-bold text-xl text-white shrink-0">
                        {{ substr($appt->patient->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-lg font-bold text-white truncate">
                            {{ Str::limit($appt->patient->name, 18) }}
                        </p>
                        <p class="text-sm text-green-400 font-mono font-bold flex items-center gap-1">
                            ⏰ {{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }} hs
                        </p>
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-gray-500 opacity-50">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-lg font-medium">Sin pacientes en espera</p>
                </div>
            @endforelse
        </div>

        {{-- Pie de página --}}
        <div class="p-4 bg-gray-900 text-center border-t border-gray-800 shrink-0">
            <p class="text-gray-600 text-xs uppercase tracking-widest">Sistema de Turnos v1.0</p>
        </div>
    </div>

    {{-- ================= OVERLAY DE LLAMADA ================= --}}
    @if($lastCall)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-blue-900/90 backdrop-blur-sm p-4 animate-bounce-in">
            <div class="bg-white rounded-3xl shadow-2xl p-12 text-center max-w-5xl w-full border-8 border-blue-400 transform scale-110">
                <p class="text-3xl text-gray-500 uppercase tracking-[0.3em] font-bold mb-4">Llamando a</p>
                
                <h1 class="text-7xl md:text-8xl font-black text-gray-900 mb-8 leading-tight">
                    {{ $lastCall->patient->name }}
                </h1>
                
                <div class="inline-flex items-center justify-center gap-4 bg-blue-600 text-white px-10 py-6 rounded-2xl shadow-xl">
                    <svg class="w-12 h-12 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    <span class="text-4xl font-bold tracking-wider">CONSULTORIO 1</span>
                </div>
            </div>
        </div>
    @endif

    {{-- BOTÓN INICIAL (Solo visible si no has interactuado) --}}
    <div x-data="{ started: false }" x-show="!started" class="fixed inset-0 z-[100] bg-black flex items-center justify-center">
        <button @click="started = true; playAudio()" class="group relative px-8 py-4 bg-white text-black font-black text-2xl rounded-full hover:scale-110 transition duration-300">
            <span class="absolute inset-0 w-full h-full bg-blue-500 rounded-full opacity-20 group-hover:animate-ping"></span>
            ▶ ACTIVAR PANTALLA
        </button>
    </div>

    <style>
        .animate-bounce-in { animation: bounceIn 0.6s cubic-bezier(0.215, 0.610, 0.355, 1.000) both; }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale3d(.3, .3, .3); }
            50% { opacity: 1; transform: scale3d(1.05, 1.05, 1.05); }
            70% { transform: scale3d(.9, .9, .9); }
            100% { opacity: 1; transform: scale3d(1, 1, 1); }
        }
    </style>
</div>