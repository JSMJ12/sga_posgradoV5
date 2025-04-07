<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use App\Models\Asignatura;
use App\Models\Aula;
use App\Models\Docente;
use App\Models\Cohorte;
use App\Models\Alumno;
use Carbon\Carbon;

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

        // Fetch related data, allowing for null values
        $asignatura = Asignatura::find($asignaturaId);
        $aula = $aulaId ? Aula::find($aulaId) : null;
        $docente = Docente::find($docenteDni);
        $cohorte = Cohorte::find($cohorteId);
        $paralelo = $aula ? $aula->paralelo : null;

        // Access academic period data in the cohort
        $periodo_academico = $cohorte->periodo_academico;

        // Current date
        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        // Construct PDF path
        $pdfFileName = $docente->apellidop . $docente->nombre1 . $cohorte->nombre . $asignatura->nombre . '_notas.pdf';
        $pdfPath = 'pdfs/' . $pdfFileName;
        $url = url($pdfPath);

        // Generate QR code with logo
        $qrCode = QrCode::format('png')
            ->size(100)
            ->eye('circle')
            ->gradient(24, 115, 108, 33, 68, 59, 'diagonal')
            ->errorCorrection('H')
            ->generate($url);

        // Create PDF and pass data to the view
        $pdf = Pdf::loadView('record.notas_asignatura', compact(
            'alumnosMatriculados',
            'asignatura',
            'fechaActual',
            'aula',
            'docente',
            'periodo_academico',
            'cohorte',
            'paralelo',
            'qrCode'
        ));

        // Ensure the PDF directory exists
        $pdfDirectory = public_path('pdfs');
        if (!file_exists($pdfDirectory)) {
            mkdir($pdfDirectory, 0755, true);
        }

        // Save the PDF
        $pdf->save(public_path($pdfPath));

        // Set paper size and suppress warnings
        $pdf->setPaper('a4')->setWarnings(false);

        // Stream the PDF for viewing or download
        return $pdf->stream($pdfFileName);
    }
}
