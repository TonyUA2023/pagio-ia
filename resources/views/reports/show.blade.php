<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Reporte de Análisis
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    
                    {{-- Encabezado del Reporte --}}
                    <div class="mb-6 border-b pb-4 dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $report->analysis->document->file_name }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Reporte generado el: {{ $report->created_at->format('d/m/Y \a \l\a\s H:i') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        {{-- Columna de Metadatos del Documento --}}
                        <div class="space-y-4">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 border-b dark:border-gray-700 pb-2">Metadatos del Documento</h4>
                            <div>
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Páginas Encontradas</p>
                                <p class="text-lg">{{ $report->analysis->document->page_count ?? 'No disponible' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Palabras Contabilizadas</p>
                                <p class="text-lg">{{ number_format($report->analysis->document->word_count, 0, ',', '.') ?? 'No disponible' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Tamaño del Archivo</p>
                                <p class="text-lg">{{ $report->analysis->document->file_size }} KB</p>
                            </div>
                             <div>
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Fecha de Subida</p>
                                <p class="text-lg">{{ $report->analysis->document->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        {{-- Columna del Resumen del Análisis --}}
                        <div class="space-y-4">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 border-b dark:border-gray-700 pb-2">Resumen del Reporte</h4>
                             <div>
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">% Similitud (Plagio)</p>
                                <p class="text-2xl font-bold text-green-500">{{ $report->total_similarity_percentage }}%</p>
                                <p class="text-xs text-gray-400">Este es un reporte básico de metadatos. No se ha realizado un análisis de plagio.</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Conclusión del Sistema</p>
                                <p class="text-base italic text-gray-700 dark:text-gray-300">"{{ $report->executive_summary }}"</p>
                            </div>
                        </div>
                    </div>

                    {{-- Botón para volver al listado de reportes --}}
                    <div class="mt-8 border-t dark:border-gray-700 pt-4 text-right">
                        <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Volver a Mis Reportes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>