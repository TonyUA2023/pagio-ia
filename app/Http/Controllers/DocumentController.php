<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDocumentAnalysis;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DocumentController extends Controller
{
    /**
     * Muestra el panel principal con los documentos recientes.
     */
    public function index(): View
    {
        $documents = Auth::user()->documents()
            ->with('analyses.report')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', ['documents' => $documents]);
    }

    /**
     * Almacena los documentos subidos de forma rápida sin procesarlos.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'document' => 'required|array|max:5',
            'document.*' => 'required|file|mimes:pdf|max:10240',
        ]);

        $user = Auth::user();
        foreach ($request->file('document') as $file) {
            $originalName = $file->getClientOriginalName();
            $path = $file->storeAs("documents/user_{$user->id}", time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $originalName), 'local');

            // La subida ahora es súper rápida. Los metadatos se calcularán en el Job.
            Document::create([
                'user_id' => $user->id,
                'file_name' => $originalName,
                'file_type' => $file->getClientOriginalExtension(),
                'storage_path' => $path,
                'status' => 'pendiente',
                'file_size' => round($file->getSize() / 1024),
                // Los campos que tardan se dejan nulos para ser calculados después.
                'word_count' => null,
                'page_count' => null,
            ]);
        }
        
        $message = count($request->file('document')) > 1 ? 'Documentos subidos.' : 'Documento subido.';
        return redirect()->route('dashboard')->with('status', $message . ' Listos para analizar.');
    }

    /**
     * Inicia el proceso de análisis y marca el documento para el sondeo.
     */

    /**
     * Inicia o reintenta el proceso de análisis para un documento.
     */
    public function analyze(Document $document): RedirectResponse
    {
        // Autorización: se asegura de que el usuario sea el dueño del documento.
        if (Auth::id() !== $document->user_id) {
            abort(403);
        }

        // --- LÓGICA MODIFICADA ---
        // Ahora permitimos el análisis si el estado es 'pendiente' O 'error'.
        if (!in_array($document->status, ['pendiente', 'error'])) {
            return redirect()->route('dashboard')->with('error', 'Este documento no se puede analizar en su estado actual.');
        }

        // Despacha el Job a la cola para procesar el análisis en segundo plano.
        ProcessDocumentAnalysis::dispatch($document);

        // Marcamos en la sesión el ID para que el modal de carga se active.
        session()->flash('analyzing_document_id', $document->id);

        return redirect()->route('dashboard');
    }

    /**
     * Devuelve el estado actual de un documento para el sondeo (polling).
     */
    public function status(Document $document): JsonResponse
    {
        if (Auth::id() !== $document->user_id) {
            return response()->json(['status' => 'unauthorized'], 403);
        }
        
        // Obtenemos el ID del reporte si ya está completado.
        $reportId = null;
        if ($document->status === 'completado') {
            $reportId = $document->analyses->first()?->report?->id;
        }

        return response()->json([
            'status' => $document->status,
            'report_id' => $reportId
        ]);
    }
}