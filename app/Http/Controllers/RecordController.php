<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Docente;
use App\Models\Secretario;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;


class RecordController extends Controller
{
    protected $directorDocente;
    protected $secretario;

    public function __construct()
    {
        $this->middleware('auth');

        // Buscar al primer usuario con rol 'director'
        $directorUser = User::role('director')->first();

        // Buscar en tabla docentes por su email
        $this->directorDocente = Docente::where('email', $directorUser?->email)->first();
    }

    private function getSecretario($alumno)
    {
        $seccion = $alumno->maestria->secciones->first();
        if (!$seccion) {
            return null;
        }
        return Secretario::where('seccion_id', $seccion->id)->first();
    }

    public function show($alumno_dni)
    {
        $alumno = Alumno::findOrFail($alumno_dni);

        $this->secretario = $this->getSecretario($alumno);
        $directorDocente = $this->directorDocente;
        $secretario = $this->secretario;

        // Obtener asignaturas de la maestría
        $asignaturas = $alumno->maestria->asignaturas ?? collect();

        // Obtener notas registradas con relaciones
        $notasRegistradas = $alumno->notas()->with('asignatura', 'docente')->get();

        // Crear estructura completa de "notas", incluyendo asignaturas sin nota
        $notasCompletas = $asignaturas->map(function ($asignatura) use ($notasRegistradas) {
            // Buscar si hay una nota para esta asignatura
            $notaExistente = $notasRegistradas->firstWhere('asignatura_id', $asignatura->id);

            if ($notaExistente) {
                return $notaExistente;
            }

            // Crear objeto "falso" de Nota si no existe
            $notaFalsa = new \stdClass();
            $notaFalsa->asignatura = $asignatura;
            $notaFalsa->docente = $asignatura->docente ?? null;
            $notaFalsa->total = null;

            return $notaFalsa;
        });

        // Total de horas (usando horas_duracion o crédito * 48)
        $totalHoras = $notasCompletas->sum(function ($nota) {
            return $nota->asignatura->horas_duracion ?? ($nota->asignatura->credito * 48);
        });

        // Promedio considerando todas las asignaturas, usando 0 si no hay nota
        $totalNotas = $notasCompletas->map(function ($nota) {
            return $nota->total ?? 0;
        });
        $promedio = $totalNotas->avg();

        // Asignaturas aprobadas (nota >= 7 y no nula)
        $cantidadAsignaturas = $notasCompletas
            ->filter(fn($nota) => !is_null($nota->total) && $nota->total >= 7)
            ->count();

        // Datos de cohorte
        $matricula = $alumno->matriculas->first();
        $cohorte = $matricula->cohorte;
        $cohorteNombre = $cohorte->nombre ?? '--';

        // Fecha actual en español
        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        // Código QR con enlace al record
        $url = route('record.show', $alumno_dni);
        $qrCode = QrCode::format('png')
            ->size(100)
            ->eye('circle')
            ->gradient(24, 115, 108, 33, 68, 59, 'diagonal')
            ->errorCorrection('H')
            ->generate($url);

        // Coordinador de la maestría
        $coordinadorDni = $alumno->maestria->coordinador;
        $coordinador = Docente::where('dni', $coordinadorDni)->first();
        $nombreCompleto = $coordinador ? $coordinador->getFullNameAttribute() : 'Coordinador no encontrado';

        // Generar PDF con los datos
        $pdf = Pdf::loadView('record.show', compact(
            'alumno',
            'notasCompletas',
            'cohorte',
            'cohorteNombre',
            'totalHoras',
            'fechaActual',
            'qrCode',
            'nombreCompleto',
            'promedio',
            'cantidadAsignaturas',
            'directorDocente',
            'secretario'
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
        // Obtener el alumno
        $alumno = Alumno::findOrFail($alumno_dni);

        // Obtener asignaturas de la maestría
        $asignaturas = $alumno->maestria->asignaturas ?? collect();

        // Obtener notas registradas con relaciones
        $notasRegistradas = $alumno->notas()->with('asignatura', 'docente')->get();

        // Crear estructura completa de asignaturas (con o sin nota)
        $notasCompletas = $asignaturas->map(function ($asignatura) use ($notasRegistradas) {
            $notaExistente = $notasRegistradas->firstWhere('asignatura_id', $asignatura->id);

            if ($notaExistente) {
                return $notaExistente;
            }

            $notaFalsa = new \stdClass();
            $notaFalsa->asignatura = $asignatura;
            $notaFalsa->docente = $asignatura->docente ?? null;
            $notaFalsa->total = null;

            return $notaFalsa;
        });

        // Total de horas
        $totalHoras = $notasCompletas->sum(function ($nota) {
            return $nota->asignatura->horas_duracion ?? ($nota->asignatura->credito * 48);
        });

        $matricula = $alumno->matriculas->first();
        $cohorte = $matricula->cohorte;

        // Extraer número romano del cohorte
        preg_match('/cohorte[:\s\-]*([A-Z0-9]+)/i', $cohorte->nombre, $matches);
        $numeroRomano = $matches[1] ?? '';

        $periodo_academico = $cohorte->periodo_academico;
        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        // Código QR
        $url = route('certificado', $alumno->dni);
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

        // Renderizar PDF
        $pdf = Pdf::loadView('record.certificacion', compact(
            'alumno',
            'notasCompletas',
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
