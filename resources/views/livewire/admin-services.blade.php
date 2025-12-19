<div> <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Servicios y Facturación</h2>
                <p class="text-sm text-gray-500">Configura los tipos de consulta, precios, duraciones y obras sociales aceptadas.</p>
            </div>
    
            @if (session()->has('message'))
                <div class="mb-6 rounded-lg bg-green-50 p-4 border-l-4 border-green-500 text-green-700 font-bold shadow-sm">
                    {{ session('message') }}
                </div>
            @endif
    
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800">Tipos de Consulta</h3>
                            <button wire:click="createType" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-2 rounded-lg transition">
                                + Nuevo Servicio
                            </button>
                        </div>
                        
                        <div class="divide-y divide-gray-100">
                            @foreach($types as $type)
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm" style="background-color: {{ $type->color }}">
                                            {{ substr($type->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800">{{ $type->name }}</h4>
                                            <div class="text-xs text-gray-500 flex items-center gap-2">
                                                <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-600 font-mono">{{ $type->duration_minutes }} min</span>
                                                <span class="text-green-600 font-bold">${{ number_format($type->price, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button wire:click="editType({{ $type->id }})" class="text-gray-400 hover:text-blue-600 p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button wire:click="deleteType({{ $type->id }})" wire:confirm="¿Borrar este servicio?" class="text-gray-400 hover:text-red-600 p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
    
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800">Obras Sociales</h3>
                            <button wire:click="createInsurance" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-bold px-3 py-2 rounded-lg transition">
                                + Agregar
                            </button>
                        </div>
                        
                        <ul class="divide-y divide-gray-100">
                            @foreach($insurances as $ins)
                                <li class="p-4 flex justify-between items-center hover:bg-gray-50 transition">
                                    <span class="text-sm font-medium text-gray-700">{{ $ins->name }}</span>
                                    <div class="flex gap-2">
                                        <button wire:click="editInsurance({{ $ins->id }})" class="text-xs text-blue-600 hover:underline">Editar</button>
                                        <button wire:click="deleteInsurance({{ $ins->id }})" wire:confirm="¿Borrar?" class="text-xs text-red-600 hover:underline">X</button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
    
            </div>
        </div>
    
        @if($modalTypeOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
                <div class="bg-blue-600 px-6 py-4">
                    <h3 class="text-white font-bold text-lg">Configurar Servicio</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre del Servicio</label>
                        <input type="text" wire:model="type_name" class="w-full rounded-lg border-gray-300" placeholder="Ej: Control Niño Sano">
                        @error('type_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Duración (min)</label>
                            <input type="number" wire:model="type_duration" class="w-full rounded-lg border-gray-300" placeholder="30">
                            @error('type_duration') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Precio ($)</label>
                            <input type="number" wire:model="type_price" class="w-full rounded-lg border-gray-300" placeholder="0">
                            @error('type_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Color en Calendario</label>
                        <div class="flex gap-3 justify-center">
                            @php $colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899']; @endphp
                            @foreach($colors as $color)
                                <button wire:click="$set('type_color', '{{ $color }}')" 
                                        class="w-8 h-8 rounded-full border-2 transition transform hover:scale-110 {{ $type_color == $color ? 'border-gray-900 ring-2 ring-gray-300' : 'border-transparent' }}"
                                        style="background-color: {{ $color }};">
                                </button>
                            @endforeach
                        </div>
                    </div>
    
                    <div class="flex gap-3 pt-2">
                        <button wire:click="closeModals" class="flex-1 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-lg">Cancelar</button>
                        <button wire:click="saveType" class="flex-1 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    
        @if($modalInsuranceOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden animate-fade-in-up">
                <div class="bg-gray-800 px-6 py-4">
                    <h3 class="text-white font-bold text-lg">Obra Social</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre</label>
                        <input type="text" wire:model="insurance_name" class="w-full rounded-lg border-gray-300" placeholder="Ej: OSDE">
                        @error('insurance_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button wire:click="closeModals" class="flex-1 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-lg">Cancelar</button>
                        <button wire:click="saveInsurance" class="flex-1 py-3 bg-gray-800 text-white font-bold rounded-lg hover:bg-gray-900 shadow">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    
        <style>
            .animate-fade-in-up { animation: fadeInUp 0.3s ease-out forwards; }
            @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        </style>
    </div>
</div>