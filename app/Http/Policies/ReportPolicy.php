<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Report $report): bool
    {
        // Esta es la regla clave:
        // El ID del usuario autenticado ($user->id) debe ser estrictamente igual (===)
        // al ID del usuario dueño del documento asociado a este reporte.
        // La ruta es: $report -> analysis -> document -> user_id
        return $user->id === $report->analysis->document->user_id;
    }
}