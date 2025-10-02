<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DocumentController extends Controller
{
    /**
     * Display the dashboard with a list of the user's recent documents.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Obtiene al usuario actualmente autenticado.
        $user = Auth::user();

        // Carga los 5 documentos más recientes asociados a ese usuario.
        // Se utiliza 'latest()' para ordenar por fecha de creación descendente.
        $documents = $user->documents()->latest()->take(5)->get();

        // Devuelve la vista 'dashboard' y le pasa la colección de documentos.
        return view('dashboard', [
            'documents' => $documents
        ]);
    }

    /**
     * Store newly uploaded documents in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validar la petición del formulario.
        //    - 'document' debe ser un array con un máximo de 5 elementos.
        //    - Cada elemento ('document.*') debe ser un archivo PDF de máximo 10MB.
        //    - 'analysis_mode' es obligatorio y debe ser 'cloud' o 'local'.
        $request->validate([
            'document' => 'required|array|max:5',
            'document.*' => 'required|file|mimes:pdf|max:10240',
            'analysis_mode' => ['required', 'string', Rule::in(['cloud', 'local'])],
        ]);

        $files = $request->file('document');
        $user = Auth::user();

        // 2. Iterar sobre cada uno de los archivos enviados.
        foreach ($files as $file) {
            // Se procesa cada archivo solo si el modo es 'cloud'.
            if ($request->input('analysis_mode') === 'cloud') {
                
                $originalName = $file->getClientOriginalName();
                
                // Se guarda el archivo en 'storage/app/documents/user_{id}/...'
                $path = $file->storeAs(
                    "documents/user_{$user->id}",
                    time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $originalName),
                    'local'
                );

                // Se crea el registro del documento en la base de datos.
                Document::create([
                    'user_id' => $user->id,
                    'file_name' => $originalName,
                    'file_type' => $file->getClientOriginalExtension(),
                    'storage_path' => $path,
                    'status' => 'pendiente', // Estado inicial del análisis.
                ]);
            } else {
                // MODO LOCAL: En un futuro, aquí iría la lógica para el análisis rápido.
                // Por ahora, no se realiza ninguna acción de guardado.
            }
        }
        
        // 3. Preparar un mensaje de respuesta para el usuario.
        $count = count($files);
        $message = $count > 1 ? "$count documentos subidos con éxito." : "¡Documento subido con éxito!";

        if ($request->input('analysis_mode') === 'local') {
            $message = 'Documentos recibidos para análisis rápido. (Funcionalidad en desarrollo)';
        }

        // 4. Redirigir al dashboard con el mensaje de estado.
        return redirect()->route('dashboard')->with('status', $message);
    }
}