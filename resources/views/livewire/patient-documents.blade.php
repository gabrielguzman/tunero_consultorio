<div class="min-h-screen bg-gray-50 py-8 sm:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="border-b border-gray-200 pb-6">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                Mis Estudios
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                Accede a las recetas, órdenes y resultados de estudios de tus hijos.
            </p>
        </div>

        @if($patients->count() > 0)
            <div class="space-y-8">
                @foreach($patients as $p)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        
                        <div class="bg-blue-50/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                                    {{ substr($p->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $p->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $p->age_string }} • {{ $p->healthInsurance->name ?? 'Particular' }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold bg-white border border-gray-200 px-2.5 py-1 rounded-full text-gray-600">
                                {{ $p->files->count() }} archivos
                            </span>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @if($p->files->count() > 0)
                                @foreach($p->files as $file)
                                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                                        <div class="flex items-center gap-4 min-w-0">
                                            <div class="flex-shrink-0">
                                                @if(in_array($file->file_type, ['pdf']))
                                                    <span class="h-10 w-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    </span>
                                                @elseif(in_array($file->file_type, ['jpg', 'jpeg', 'png', 'webp']))
                                                    <span class="h-10 w-10 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </span>
                                                @else
                                                    <span class="h-10 w-10 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $file->original_name }}</p>
                                                <p class="text-xs text-gray-500">Subido el {{ $file->created_at->format('d/m/Y') }}</p>
                                            </div>
                                        </div>

                                        <div class="flex-shrink-0 ml-4">
                                            <a href="{{ Storage::url($file->file_path) }}" 
                                               target="_blank" 
                                               download="{{ $file->original_name }}"
                                               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 hover:text-blue-600 focus:outline-none transition">
                                                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4 4m4-4v12"></path></svg>
                                                Descargar
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-6 py-8 text-center">
                                    <p class="text-sm text-gray-500 italic">No hay documentos cargados para {{ $p->name }}.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay pacientes registrados</h3>
                <div class="mt-6">
                    <a href="{{ route('patient.manager') }}" class="text-blue-600 hover:underline font-bold text-sm">Registrar mis hijos</a>
                </div>
            </div>
        @endif
    </div>
</div>