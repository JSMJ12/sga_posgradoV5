<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Secretario;
use App\Models\Docente;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class RecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function show($alumno_dni)
    {
        // Obtener el alumno y sus notas
        $alumno = Alumno::findOrFail($alumno_dni);
        $notas = $alumno->notas()->with('asignatura', 'docente')->get();

        $seccionId = $alumno->maestria->secciones->first()->id;

        $secretarios = Secretario::where('seccion_id', $seccionId)->get();

        $totalCreditos = $notas->sum(function ($nota) {
            return $nota->asignatura->credito;
        });

        // Obtener la cohorte del alumno
        $cohorte = $alumno->maestria->cohortes->first();

        preg_match('/Cohorte (\w+)/', $cohorte->nombre, $matches);
        $numeroRomano = $matches[1] ?? '';

        // Acceder a los datos de periodo_academico en la cohorte
        $periodo_academico = $cohorte->periodo_academico;

        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        // Ruta del archivo PDF, incluye DNI, dentro del subdirectorio
        $pdfPath = 'record_academico/pdf/' . $alumno->dni . '_' . $alumno->apellidop . '_' . $alumno->nombre1 . '_notas.pdf';
        $pdfFullPath = public_path($pdfPath); // Ruta completa del archivo PDF
        $url = url($pdfPath);

        // Verificar si el archivo ya existe
        if (!File::exists($pdfFullPath)) {
            // Generar el código QR con logotipo
            $qrCode = QrCode::format('png')
                ->size(100)
                ->eye('circle')
                ->gradient(24, 115, 108, 33, 68, 59, 'diagonal')
                ->errorCorrection('H')
                ->generate($url);

            $coordinadorDni = $alumno->maestria->coordinador;

            // Buscar al docente utilizando el DNI
            $coordinador = Docente::where('dni', $coordinadorDni)->first();

            if ($coordinador) {
                // Acceder al nombre completo utilizando el método getFullNameAttribute
                $nombreCompleto = $coordinador->getFullNameAttribute();
            } else {
                $nombreCompleto = 'Coordinador no encontrado';
            }

            // Crear una instancia de Dompdf con las opciones
            $pdf = Pdf::loadView('record.show', compact('secretarios', 'alumno', 'notas', 'periodo_academico', 'cohorte', 'totalCreditos', 'numeroRomano', 'fechaActual', 'qrCode', 'nombreCompleto'));

            // Directorio para almacenar los PDFs
            $pdfDirectory = public_path('record_academico/pdf');

            // Verificar si el directorio existe, si no, crearlo
            if (!file_exists($pdfDirectory)) {
                mkdir($pdfDirectory, 0755, true);
            }

            // Guardar el PDF
            $pdf->save($pdfFullPath);
        }

        // Redirigir al archivo PDF en una nueva pestaña
        return redirect()->away($url);
    }
}
