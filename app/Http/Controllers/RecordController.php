<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Docente;
use App\Models\Secretario;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;

class RecordController extends Controller
{
    protected $directorDocente;

    public function __construct()
    {
        $this->middleware('auth');

        // Buscar al primer usuario con rol 'director'
        $directorUser = User::role('director')->first();
        $this->directorDocente = Docente::where('email', $directorUser?->email)->first();
    }

    private function getSecretario($maestria_id)
    {
        $seccion = \App\Models\Seccion::whereHas('maestrias', function($q) use ($maestria_id){
            $q->where('maestrias.id', $maestria_id);
        })->first();

        return $seccion ? Secretario::where('seccion_id', $seccion->id)->first() : null;
    }

    private function getAlumnoEnMaestria($alumno_dni, $maestria_id)
    {
        return Alumno::where('dni', $alumno_dni)
            ->whereHas('maestrias', function($q) use ($maestria_id){
                $q->where('maestrias.id', $maestria_id);
            })
            ->firstOrFail();
    }

    protected function getMatriculaPorMaestria($alumno, $maestria_id)
    {
        return $alumno->matriculas->first(function ($matricula) use ($maestria_id) {
            return $matricula->cohorte && $matricula->cohorte->maestria_id == $maestria_id;
        });
    }

    public function record_academico($alumno_dni, $maestria_id)
    {
        $alumno = $this->getAlumnoEnMaestria($alumno_dni, $maestria_id);
        $secretario = $this->getSecretario($maestria_id);
        $directorDocente = $this->directorDocente;

        $asignaturas = $alumno->maestrias->find($maestria_id)?->asignaturas ?? collect();
        $notasRegistradas = $alumno->notas()->with('asignatura', 'docente')->get();

        $notasCompletas = $asignaturas->map(function ($asignatura) use ($notasRegistradas) {
            $notaExistente = $notasRegistradas->firstWhere('asignatura_id', $asignatura->id);
            if ($notaExistente) return $notaExistente;

            $notaFalsa = new \stdClass();
            $notaFalsa->asignatura = $asignatura;
            $notaFalsa->docente = $asignatura->docente ?? null;
            $notaFalsa->total = null;
            return $notaFalsa;
        });

        $totalHoras = $notasCompletas->sum(fn($nota) => $nota->asignatura->horas_duracion ?? ($nota->asignatura->credito * 48));
        $promedio = $notasCompletas->map(fn($nota) => $nota->total ?? 0)->avg();
        $cantidadAsignaturas = $notasCompletas->filter(fn($nota) => !is_null($nota->total) && $nota->total >= 7)->count();

        $matricula = $this->getMatriculaPorMaestria($alumno, $maestria_id);
        $cohorte = $matricula?->cohorte;
        $cohorteNombre = $cohorte->nombre ?? '--';

        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        $coordinador = Docente::where('dni', $alumno->maestrias->find($maestria_id)?->coordinador)->first();
        $nombreCompleto = $coordinador?->getFullNameAttribute() ?? 'Coordinador no encontrado';
        $maestria = $alumno->maestrias->find($maestria_id);

        $pdf = Pdf::loadView('record.record_academico', compact(
            'alumno',
            'maestria',
            'notasCompletas',
            'cohorte',
            'cohorteNombre',
            'totalHoras',
            'fechaActual',
            'nombreCompleto',
            'promedio',
            'cantidadAsignaturas',
            'directorDocente',
            'secretario'
        ));

        return $pdf->stream('record_academico_' . $alumno->dni . '.pdf');
    }

    public function certificado_matricula($alumno_dni, $maestria_id)
    {
        $alumno = $this->getAlumnoEnMaestria($alumno_dni, $maestria_id);
        $matricula = $this->getMatriculaPorMaestria($alumno, $maestria_id);

        if (!$matricula || !$matricula->cohorte) return back()->with('error', 'El alumno no tiene matrícula o cohorte en esta maestría.');

        $cohorte = $matricula->cohorte;
        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');
        $nombreLimpio = Str::slug($alumno->apellidop . '_' . $alumno->nombre1);

        $coordinador = Docente::where('dni', $alumno->maestrias->find($maestria_id)?->coordinador)->first();
        $nombreCompleto = $coordinador?->getFullNameAttribute() ?? 'Coordinador no encontrado';
        $maestria = $alumno->maestrias->find($maestria_id);

        $pdf = Pdf::loadView('record.certificado_matricula', compact(
            'alumno',
            'maestria',
            'cohorte',
            'fechaActual',
            'nombreCompleto'
        ))->setPaper('A4', 'portrait');

        return $pdf->stream("certificado_matricula_{$nombreLimpio}.pdf");
    }

    public function certificado($alumno_dni, $maestria_id)
    {
        $alumno = $this->getAlumnoEnMaestria($alumno_dni, $maestria_id);
        $asignaturas = $alumno->maestrias->find($maestria_id)?->asignaturas ?? collect();
        $notasRegistradas = $alumno->notas()->with('asignatura', 'docente')->get();

        $notasCompletas = $asignaturas->map(function ($asignatura) use ($notasRegistradas) {
            $notaExistente = $notasRegistradas->firstWhere('asignatura_id', $asignatura->id);
            if ($notaExistente) return $notaExistente;

            $notaFalsa = new \stdClass();
            $notaFalsa->asignatura = $asignatura;
            $notaFalsa->docente = $asignatura->docente ?? null;
            $notaFalsa->total = null;
            return $notaFalsa;
        });

        $totalHoras = $notasCompletas->sum(fn($nota) => $nota->asignatura->horas_duracion ?? ($nota->asignatura->credito * 48));
        $matricula = $this->getMatriculaPorMaestria($alumno, $maestria_id);
        $cohorte = $matricula?->cohorte;
        preg_match('/cohorte[:\s\-]*([A-Z0-9]+)/i', $cohorte?->nombre ?? '', $matches);
        $numeroRomano = $matches[1] ?? '';
        $periodo_academico = $cohorte?->periodo_academico;
        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        $coordinador = Docente::where('dni', $alumno->maestrias->find($maestria_id)?->coordinador)->first();
        $nombreCompleto = $coordinador?->getFullNameAttribute() ?? 'Coordinador no encontrado';
        $maestria = $alumno->maestrias->find($maestria_id);

        $pdf = Pdf::loadView('record.certificacion', compact(
            'alumno',
            'notasCompletas',
            'maestria',
            'periodo_academico',
            'cohorte',
            'totalHoras',
            'numeroRomano',
            'fechaActual',
            'nombreCompleto'
        ));

        return $pdf->stream('certificado_' . $alumno->dni . '.pdf');
    }

    public function certificado_culminacion($alumno_dni, $maestria_id)
    {
        $alumno = $this->getAlumnoEnMaestria($alumno_dni, $maestria_id);
        $notas = $alumno->notas()->with('asignatura', 'docente')->get();
        $totalHoras = $notas->sum(fn($nota) => $nota->asignatura->horas_duracion ?? ($nota->asignatura->credito * 48));

        $matricula = $this->getMatriculaPorMaestria($alumno, $maestria_id);
        $cohorte = $matricula?->cohorte;
        preg_match('/cohorte[:\s\-]*([A-Z0-9]+)/i', $cohorte?->nombre ?? '', $matches);
        $numeroRomano = $matches[1] ?? '';
        $periodo_academico = $cohorte?->periodo_academico;
        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        $coordinador = Docente::where('dni', $alumno->maestrias->find($maestria_id)?->coordinador)->first();
        $nombreCompleto = $coordinador?->getFullNameAttribute() ?? 'Coordinador no encontrado';
        $maestria = $alumno->maestrias->find($maestria_id);

        $pdf = Pdf::loadView('record.certificado_culminacion', compact(
            'alumno',
            'notas',
            'periodo_academico',
            'cohorte',
            'totalHoras',
            'numeroRomano',
            'maestria',
            'fechaActual',
            'nombreCompleto'
        ));

        return $pdf->stream('certificado_culminacion_' . $alumno->dni . '.pdf');
    }

}
