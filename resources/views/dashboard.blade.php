<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Estudiante') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- Sección para subir documentos --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Sube tus documentos para el análisis
                    </h3>

                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
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

                        <div id="file-list-container" class="mt-4 text-sm text-gray-600 dark:text-gray-400"></div>

                        @error('document') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        @error('document.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

                        <div class="mt-6 flex justify-end">
                            <x-primary-button>
                                {{ __('Subir Documentos') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sección de Documentos Recientes --}}
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
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($documents as $document)
                                        <tr id="document-row-{{ $document->id }}" data-filename="{{ $document->file_name }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ Str::limit($document->file_name, 30) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="status-cell px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="status-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($document->status == 'pendiente') bg-yellow-100 text-yellow-800 @endif
                                                    @if($document->status == 'completado') bg-green-100 text-green-800 @endif
                                                    @if($document->status == 'error') bg-red-100 text-red-800 @endif
                                                    @if($document->status == 'en_proceso') bg-blue-100 text-blue-800 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                                </span>
                                            </td>
                                            <td class="actions-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($document->status == 'pendiente')
                                                    @php $time = 2 + round($document->file_size / 200); @endphp
                                                    <form action="{{ route('documents.analyze', $document) }}" method="POST" data-time="{{ $time }}">
                                                        @csrf
                                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900 font-semibold">Analizar</button>
                                                        <span class="ml-2 text-xs text-gray-400">(Est. {{ $time }}s)</span>
                                                    </form>
                                                
                                                @elseif($document->status == 'error')
                                                    @php $time = 2 + round($document->file_size / 200); @endphp
                                                    <form action="{{ route('documents.analyze', $document) }}" method="POST" data-time="{{ $time }}">
                                                        @csrf
                                                        <button type="submit" class="text-orange-500 hover:text-orange-700 font-semibold">Reintentar Análisis</button>
                                                    </form>

                                                @elseif($document->status == 'completado' && $document->analyses->first()?->report)
                                                    <a href="{{ route('reports.show', $document->analyses->first()->report->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Ver Reporte</a>
                                                @else
                                                    <span>-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">Aún no has subido ningún documento.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE CARGA CON BARRA DE PROGRESO --}}
    <div id="loading-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8 max-w-sm w-full text-center">
            <svg class="animate-spin h-10 w-10 text-indigo-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-gray-100">Procesando Análisis</h3>
            <p id="modal-filename" class="mt-2 text-sm text-gray-600 dark:text-gray-400 truncate"></p>
            
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mt-4">
                <div id="progress-bar" class="bg-indigo-600 h-2.5 rounded-full" style="width: 0%"></div>
            </div>
            <p id="modal-progress-text" class="mt-2 text-xs text-gray-500">0%</p>
            
            <p class="mt-4 text-sm text-gray-500">Esto puede tardar un momento...</p>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Script para la previsualización de archivos
            const fileInput = document.getElementById('dropzone-file');
            const fileListContainer = document.getElementById('file-list-container');
            const maxFiles = 5;

            fileInput.addEventListener('change', (event) => {
                fileListContainer.innerHTML = '';
                const files = event.target.files;

                if (files.length > maxFiles) {
                    fileListContainer.innerHTML = `<p class="text-red-500 font-semibold">Error: No puedes seleccionar más de ${maxFiles} archivos.</p>`;
                    fileInput.value = '';
                    return;
                }

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

            // --- Lógica del Modal, Barra de Progreso y Sondeo (Polling) ---
            const modal = document.getElementById('loading-modal');
            const modalFilename = document.getElementById('modal-filename');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('modal-progress-text');
            let pollingInterval;
            let progressInterval;

            function startPolling(documentId, estimatedTime) {
                const row = document.getElementById(`document-row-${documentId}`);
                if (!row) return;

                modalFilename.textContent = row.dataset.filename;
                modal.classList.remove('hidden');

                // Iniciar la animación de la barra de progreso
                let progress = 0;
                const increment = 100 / (estimatedTime * 2); 
                progressInterval = setInterval(() => {
                    progress += increment;
                    if (progress >= 95) {
                        clearInterval(progressInterval);
                        progress = 95;
                    }; 
                    progressBar.style.width = progress + '%';
                    progressText.textContent = Math.round(progress) + '%';
                }, 500);

                // Iniciar el sondeo al backend
                pollingInterval = setInterval(() => {
                    fetch(`/documents/${documentId}/status`)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            updateTableRowStatus(documentId, data.status);

                            if (data.status === 'completado' || data.status === 'error') {
                                clearInterval(pollingInterval);
                                clearInterval(progressInterval);

                                progressBar.style.width = '100%';
                                progressText.textContent = '100%';
                                setTimeout(() => {
                                    modal.classList.add('hidden');
                                    updateTableRowActions(documentId, data.status, data.report_id);
                                }, 500);
                            }
                        })
                        .catch(error => {
                            console.error('Error durante el sondeo:', error);
                            clearInterval(pollingInterval);
                            clearInterval(progressInterval);
                            modal.classList.add('hidden');
                        });
                }, 3000); 
            }

            function updateTableRowStatus(documentId, status) {
                 const row = document.getElementById(`document-row-${documentId}`);
                 if (!row) return;
                 const statusCell = row.querySelector('.status-cell');
                 let badgeClass = 'bg-gray-100 text-gray-800';
                 if (status === 'completado') badgeClass = 'bg-green-100 text-green-800';
                 if (status === 'en_proceso') badgeClass = 'bg-blue-100 text-blue-800';
                 if (status === 'error') badgeClass = 'bg-red-100 text-red-800';
                 if (status === 'pendiente') badgeClass = 'bg-yellow-100 text-yellow-800';
                 
                 statusCell.innerHTML = `<span class="status-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${badgeClass}">${status.replace('_', ' ')}</span>`;
            }

            function updateTableRowActions(documentId, status, reportId) {
                const row = document.getElementById(`document-row-${documentId}`);
                if (!row) return;
                const actionsCell = row.querySelector('.actions-cell');
                const form = document.querySelector(`form[action*="documents/${documentId}/analyze"]`);
                const time = form ? form.dataset.time : 15;
                
                if (status === 'completado' && reportId) {
                    actionsCell.innerHTML = `<a href="/reports/${reportId}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-semibold">Ver Reporte</a>`;
                
                } else if (status === 'error') {
                    // Generamos el formulario de reintento dinámicamente
                    // Es necesario incluir el token CSRF para que el POST funcione
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    actionsCell.innerHTML = `
                        <form action="/documents/${documentId}/analyze" method="POST" data-time="${time}">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="text-orange-500 hover:text-orange-700 font-semibold">Reintentar Análisis</button>
                        </form>
                    `;
                } else {
                     actionsCell.innerHTML = `<span>-</span>`;
                }
            }

            // Inicia el sondeo si la sesión lo indica al recargar la página.
            @if (session('analyzing_document_id'))
                const analyzingDocId = {{ session('analyzing_document_id') }};
                const form = document.querySelector(`form[action*="documents/${analyzingDocId}/analyze"]`);
                const time = form ? form.dataset.time : 15;
                startPolling(analyzingDocId, time);
            @endif

            // Añadir el token CSRF a la cabecera del layout para que el script pueda acceder a él.
            // Esto se debe poner en tu archivo principal de layout, como app.blade.php
            // <meta name="csrf-token" content="{{ csrf_token() }}">
        });
    </script>
</x-app-layout>