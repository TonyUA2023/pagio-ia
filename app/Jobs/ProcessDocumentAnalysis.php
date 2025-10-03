<?php

namespace App\Jobs;

use App\Models\Analysis;
use App\Models\Document;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser; // Ya estabas usando esta clase, ¡ahora la usaremos al máximo!
// use Spatie\PdfToText\Pdf; // Ya no necesitamos esta, la puedes eliminar.

class ProcessDocumentAnalysis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function handle(): void
    {
        try {
            $this->document->update(['status' => 'en_proceso']);
            $fullPath = Storage::disk('local')->path($this->document->storage_path);

            // --- INICIO DE LA MODIFICACIÓN ---

            // 1. Extraer metadatos y texto usando Smalot/PdfParser
            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);
            
            $text = $pdf->getText(); // Obtenemos el texto completo del PDF.
            $pageCount = count($pdf->getPages()); // Obtenemos el conteo de páginas.
            $wordCount = str_word_count($text); // Contamos las palabras del texto extraído.
            
            // --- FIN DE LA MODIFICACIÓN ---

            // Actualizamos el documento con los metadatos calculados.
            $this->document->update([
                'word_count' => $wordCount,
                'page_count' => $pageCount,
            ]);
            
            // 2. Crear el registro de análisis (esto no cambia)
            $analysis = Analysis::create([
                'document_id' => $this->document->id,
                'start_date' => now(),
                'ai_engine_version' => '1.0.0-metadata'
            ]);

            // 3. Generar el resumen ejecutivo (esto no cambia)
            $summary = sprintf(
                "Reporte de metadatos generado. Páginas: %d. Palabras: %s. Tamaño: %d KB. Subido el: %s.",
                $pageCount, number_format($wordCount), $this->document->file_size, $this->document->created_at->format('d/m/Y')
            );

            // 4. Crear el reporte (esto no cambia)
            Report::create([
                'analysis_id' => $analysis->id,
                'total_similarity_percentage' => 0.00,
                'executive_summary' => $summary,
            ]);

            // 5. Finalizar el proceso (esto no cambia)
            $analysis->update(['end_date' => now()]);
            $this->document->update(['status' => 'completado']);

        } catch (\Exception $e) {
            Log::error("Fallo en el análisis del documento ID {$this->document->id}: " . $e->getMessage());
            $this->document->update(['status' => 'error']);
        }
    }
}