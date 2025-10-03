<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Muestra una lista paginada de los reportes del usuario autenticado.
     */
    public function index(): View
    {
        $reports = Report::whereHas('analysis.document', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->with('analysis.document')
        ->latest()
        ->paginate(10);

        return view('reports.index', ['reports' => $reports]);
    }

    /**
     * Muestra un reporte específico y sus detalles.
     */
    public function show(Report $report): View
    {
        // Gate::authorize('view', $report);
        
        $report->load('analysis.document');

        return view('reports.show', ['report' => $report]);
    }
}