<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Asignatura;
use App\Models\Aula;
use App\Models\Docente;
use App\Models\Cohorte;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class NotasAsignaturaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($docenteDni, $asignaturaId, $cohorteId, $aulaId = null)
    {
        // Obtain enrolled students, considering optional aula
        $alumnosMatriculados = Alumno::whereHas('matriculas', function ($query) use ($asignaturaId, $cohorteId, $docenteDni) {
            $query->where('asignatura_id', $asignaturaId)
                  ->where('cohorte_id', $cohorteId)
                  ->where('docente_dni', $docenteDni);
        })
        ->with(['matriculas', 'matriculas.asignatura', 'matriculas.cohorte', 'matriculas.docente'])
        ->get();

        // Fetch related data
        $asignatura = Asignatura::find($asignaturaId);
        $aula = $aulaId ? Aula::find($aulaId) : null;
        $docente = Docente::find($docenteDni);
        $cohorte = Cohorte::find($cohorteId);
        $paralelo = $aula ? $aula->paralelo : null;
        $periodo_academico = $cohorte->periodo_academico;

        // Current date
        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        // Generate a fake URL for QR purposes (optional)
        $url = url('/'); // Puedes apuntar a la página principal u otro recurso

        // Create PDF in memory without saving
        $pdf = Pdf::loadView('record.notas_asignatura', compact(
            'alumnosMatriculados',
            'asignatura',
            'fechaActual',
            'aula',
            'docente',
            'periodo_academico',
            'cohorte',
            'paralelo',
        ))
        ->setPaper('a4')
        ->setWarnings(false);

        // Generate a friendly filename
        $pdfFileName = Str::slug(
            $docente->apellidop . ' ' . $docente->nombre1 . ' ' . $cohorte->nombre . ' ' . $asignatura->nombre
        ) . '_notas.pdf';

        // Stream the PDF directly
        return $pdf->stream($pdfFileName);
    }
}
