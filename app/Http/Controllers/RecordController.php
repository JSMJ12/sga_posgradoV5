<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Docente;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


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

        // Total de horas
        $totalHoras = $notas->sum(function ($nota) {
            return $nota->asignatura->horas_duracion ?? $nota->asignatura->credito * 48;
        });

        // Promedio solo de notas no nulas
        $notasValidas = $notas->filter(function ($nota) {
            return !is_null($nota->total);
        });

        $promedio = $notasValidas->avg('total');

        // Contador de asignaturas
        $cantidadAsignaturas = $notas->filter(function ($nota) {
            return !is_null($nota->total) && $nota->total >= 7;
        })->count();


        // Datos de cohorte
        $matricula = $alumno->matriculas->first();
        $cohorte = $matricula->cohorte;

        // Fecha actual formateada
        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        // Código QR
        $url = route('record.show', $alumno_dni);
        $qrCode = QrCode::format('png')
            ->size(100)
            ->eye('circle')
            ->gradient(24, 115, 108, 33, 68, 59, 'diagonal')
            ->errorCorrection('H')
            ->generate($url);

        // Coordinador
        $coordinadorDni = $alumno->maestria->coordinador;
        $coordinador = Docente::where('dni', $coordinadorDni)->first();
        $nombreCompleto = $coordinador ? $coordinador->getFullNameAttribute() : 'Coordinador no encontrado';

        // Retornar el PDF
        $pdf = Pdf::loadView('record.show', compact(
            'alumno',
            'notas',
            'cohorte',
            'totalHoras',
            'fechaActual',
            'qrCode',
            'nombreCompleto',
            'promedio',
            'cantidadAsignaturas'
        ));

        return $pdf->stream('record_academico_' . $alumno->dni . '.pdf');
    }


    public function certificado_matricula($alumno_dni)
    {
        $alumno = Alumno::findOrFail($alumno_dni);

        $matricula = $alumno->matriculas->first();
        if (!$matricula || !$matricula->cohorte) {
            return back()->with('error', 'El alumno no tiene matrícula o cohorte.');
        }

        $cohorte = $matricula->cohorte;

        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        $nombreLimpio = Str::slug($alumno->apellidop . '_' . $alumno->nombre1);
        $url = url()->full();

        $qrCode = QrCode::format('png')
            ->size(100)
            ->eye('circle')
            ->gradient(24, 115, 108, 33, 68, 59, 'diagonal')
            ->errorCorrection('H')
            ->generate($url);

        $coordinador = Docente::where('dni', $alumno->maestria->coordinador)->first();
        $nombreCompleto = $coordinador?->getFullNameAttribute() ?? 'Coordinador no encontrado';

        $pdf = Pdf::loadView('record.certificado_matricula', compact(
            'alumno',
            'cohorte',
            'fechaActual',
            'qrCode',
            'nombreCompleto'
        ));

        return $pdf->stream("certificado_matricula_{$nombreLimpio}.pdf");
    }
    public function certificado($alumno_dni)
    {
        // Obtener el alumno y sus notas
        $alumno = Alumno::findOrFail($alumno_dni);
        $notas = $alumno->notas()->with('asignatura', 'docente')->get();

        $seccionId = $alumno->maestria->secciones->first()->id;

        $totalHoras = $notas->sum(function ($nota) {
            return $nota->asignatura->horas_duracion ?? $nota->asignatura->credito * 48;
        });

        $matricula = $alumno->matriculas->first();
        $cohorte = $matricula->cohorte;

        preg_match('/cohorte[:\s\-]*([A-Z0-9]+)/i', $cohorte->nombre, $matches);
        $numeroRomano = $matches[1] ?? '';

        $periodo_academico = $cohorte->periodo_academico;

        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        // Generar el código QR con URL de visualización (si deseas usarlo aún)
        $url = route('certificado', $alumno->dni);
        $qrCode = QrCode::format('png')
            ->size(100)
            ->eye('circle')
            ->gradient(24, 115, 108, 33, 68, 59, 'diagonal')
            ->errorCorrection('H')
            ->generate($url);

        $coordinadorDni = $alumno->maestria->coordinador;
        $coordinador = Docente::where('dni', $coordinadorDni)->first();
        $nombreCompleto = $coordinador ? $coordinador->getFullNameAttribute() : 'Coordinador no encontrado';

        // Generar y retornar el PDF en línea (sin guardar)
        $pdf = Pdf::loadView('record.certificacion', compact(
            'alumno',
            'notas',
            'periodo_academico',
            'cohorte',
            'totalHoras',
            'numeroRomano',
            'fechaActual',
            'qrCode',
            'nombreCompleto'
        ));

        return $pdf->stream('certificado_' . $alumno->dni . '.pdf');
    }

    public function certificado_culminacion($alumno_dni)
    {
        // Obtener el alumno y sus notas
        $alumno = Alumno::findOrFail($alumno_dni);
        $notas = $alumno->notas()->with('asignatura', 'docente')->get();

        $seccionId = $alumno->maestria->secciones->first()->id;

        $totalHoras = $notas->sum(function ($nota) {
            return $nota->asignatura->horas_duracion ?? $nota->asignatura->credito * 48;
        });

        $matricula = $alumno->matriculas->first();
        $cohorte = $matricula->cohorte;

        preg_match('/cohorte[:\s\-]*([A-Z0-9]+)/i', $cohorte->nombre, $matches);
        $numeroRomano = $matches[1] ?? '';

        $periodo_academico = $cohorte->periodo_academico;

        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        // Generar el código QR con URL de visualización (si deseas usarlo aún)
        $url = route('certificado_culminacion', $alumno->dni);
        $qrCode = QrCode::format('png')
            ->size(100)
            ->eye('circle')
            ->gradient(24, 115, 108, 33, 68, 59, 'diagonal')
            ->errorCorrection('H')
            ->generate($url);

        $coordinadorDni = $alumno->maestria->coordinador;
        $coordinador = Docente::where('dni', $coordinadorDni)->first();
        $nombreCompleto = $coordinador ? $coordinador->getFullNameAttribute() : 'Coordinador no encontrado';

        // Generar y retornar el PDF en línea (sin guardar)
        $pdf = Pdf::loadView('record.certificado_culminacion', compact(
            'alumno',
            'notas',
            'periodo_academico',
            'cohorte',
            'totalHoras',
            'numeroRomano',
            'fechaActual',
            'qrCode',
            'nombreCompleto'
        ));

        return $pdf->stream('certificado_culminacion_' . $alumno->dni . '.pdf');
    }
}
