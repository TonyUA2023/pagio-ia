<x-app-layout>
    {{-- Slot para el encabezado de la página --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Estudiante') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- 1. Sección para subir documentos --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    {{-- Muestra un mensaje de éxito si existe en la sesión --}}
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Sube tus documentos para el análisis (máx. 5)
                    </h3>

                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Selección de modo de análisis --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Modo de Análisis</label>
                            <div class="flex items-center space-x-6">
                                <div class="flex items-center">
                                    <input checked id="mode-cloud" type="radio" value="cloud" name="analysis_mode" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                    <label for="mode-cloud" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Guardar y Analizar</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="mode-local" type="radio" value="local" name="analysis_mode" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                    <label for="mode-local" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Análisis Rápido</o>
                                </div>
                            </div>
                        </div>

                        {{-- Área para arrastrar y soltar o seleccionar archivos --}}
                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/></svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Haz clic para subir</span> o arrastra y suelta</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">SOLO ARCHIVOS PDF (MAX. 10MB)</p>
                                </div>
                                <input id="dropzone-file" name="document[]" type="file" class="hidden" accept=".pdf" multiple />
                            </label>
                        </div> 

                        {{-- Contenedor para la lista de archivos seleccionados --}}
                        <div id="file-list-container" class="mt-4 text-sm text-gray-600 dark:text-gray-400"></div>

                        {{-- Muestra errores de validación --}}
                        @error('document') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        @error('document.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        @error('analysis_mode') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

                        <div class="mt-6 flex justify-end">
                            <x-primary-button>
                                {{ __('Analizar Documentos') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 2. Sección de Documentos Recientes --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Tus Documentos Recientes
                    </h3>
                    @if($documents->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre del Archivo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Subida</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($documents as $document)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $document->file_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{-- Asigna colores según el estado del documento --}}
                                                    @if($document->status == 'pendiente') bg-yellow-100 text-yellow-800 @endif
                                                    @if($document->status == 'completado') bg-green-100 text-green-800 @endif
                                                    @if($document->status == 'error') bg-red-100 text-red-800 @endif
                                                    @if($document->status == 'en_proceso') bg-blue-100 text-blue-800 @endif">
                                                    {{-- Formatea el texto del estado --}}
                                                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">Aún no has subido ningún documento en modo "Guardar y Analizar".</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    {{-- JavaScript para la vista previa de archivos --}}
    <script>
        const fileInput = document.getElementById('dropzone-file');
        const fileListContainer = document.getElementById('file-list-container');
        const maxFiles = 5;

        fileInput.addEventListener('change', (event) => {
            fileListContainer.innerHTML = '';
            const files = event.target.files;

            // Valida que no se exceda el número máximo de archivos
            if (files.length > maxFiles) {
                fileListContainer.innerHTML = `<p class="text-red-500 font-semibold">Error: No puedes seleccionar más de ${maxFiles} archivos a la vez.</p>`;
                fileInput.value = ''; // Limpia la selección si es inválida
                return;
            }

            // Si hay archivos, crea una lista para mostrarlos
            if (files.length > 0) {
                const list = document.createElement('ul');
                list.className = 'list-disc pl-5 space-y-1';
                list.innerHTML = '<p class="font-semibold mb-2">Archivos seleccionados:</p>';
                
                for (const file of files) {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                    list.appendChild(listItem);
                }
                fileListContainer.appendChild(list);
            }
        });
    </script>
</x-app-layout>

