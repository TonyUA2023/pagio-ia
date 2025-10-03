<?php

namespace App\Services;

class PlagiarismAnalysisService
{
    /**
     * Simula un análisis de plagio en el texto proporcionado.
     *
     * @param string $text El texto del documento a analizar.
     * @return array Los resultados simulados del análisis.
     */
    public function analyze(string $text): array
    {
        // Simulación: En un caso real, aquí llamarías a una API externa (Google AI, OpenAI, etc.)
        // y procesarías su respuesta.

        // Por ahora, devolvemos datos falsos estructurados.
        $totalSimilarity = rand(5, 45) + (rand(0, 99) / 100);

        $matches = [
            [
                'original_text_fragment' => 'Laravel es un framework de PHP muy popular para el desarrollo web.',
                'source_text_fragment' => 'Conocido como un popular framework de PHP, Laravel es usado para el desarrollo web.',
                'fragment_similarity_percentage' => 85.50,
                'source' => [
                    'type' => 'web',
                    'url_identifier' => 'https://es.wikipedia.org/wiki/Laravel',
                    'source_title' => 'Laravel - Wikipedia, la enciclopedia libre'
                ]
            ],
            [
                'original_text_fragment' => 'El sistema de colas de Laravel permite ejecutar tareas en segundo plano.',
                'source_text_fragment' => 'Las tareas pueden ser ejecutadas en segundo plano usando el sistema de colas de Laravel.',
                'fragment_similarity_percentage' => 92.10,
                'source' => [
                    'type' => 'publicacion_academica',
                    'url_identifier' => 'https://laravel.com/docs/queues',
                    'source_title' => 'Laravel Queues Documentation'
                ]
            ]
        ];

        return [
            'total_similarity_percentage' => $totalSimilarity,
            'executive_summary' => "Se encontró un porcentaje de similitud total del {$totalSimilarity}%. Se detectaron coincidencias significativas con fuentes web y publicaciones académicas.",
            'matches' => $matches,
        ];
    }
}
