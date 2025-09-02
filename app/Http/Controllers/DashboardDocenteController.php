<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\Alumno;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AlumnosExport;
use App\Models\Nota;
use App\Models\Asignatura;
use App\Models\CalificacionVerificacion;
use App\Models\Aula;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

class DashboardDocenteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $user    = auth()->user();

        // Eager Loading para evitar N+1
        $docente = Docente::where('email', $user->email)
            ->with([
                'asignaturas.maestria',
                'asignaturas.cohortes.periodo_academico',
                'asignaturas.cohortes.aula',
                'asignaturas.cohortes.matriculas.alumno',
            ])
            ->firstOrFail();

        $data = $docente->asignaturas->map(function ($asignatura) use ($docente) {
            $maestria = $asignatura->maestria;

            return [
                'nombre'          => $asignatura->nombre,
                'id'              => $asignatura->id,
                'silabo'          => $asignatura->silabo,
                'maestria_id'     => $maestria->id ?? null,
                'maestria_nombre' => $maestria->nombre ?? 'Sin Maestría',
                'maestria_codigo' => $maestria->codigo ?? 'N/A',

                'cohortes' => $asignatura->cohortes
                    ->sortBy('periodo_academico.fecha_fin')
                    ->map(function ($cohorte) use ($docente, $asignatura) {
                        // Manejo seguro de fechas
                        $fechaFin    = $cohorte->periodo_academico?->fecha_fin;
                        $fechaFin    = $fechaFin instanceof Carbon ? $fechaFin : Carbon::parse($fechaFin);
                        $fechaLimite = $fechaFin->copy()->addWeek();

                        // Consultas únicas en lugar de dentro del loop
                        $existenNotas = Nota::where([
                            'docente_dni'   => $docente->dni,
                            'asignatura_id' => $asignatura->id,
                            'cohorte_id'    => $cohorte->id,
                        ])->exists();

                        $calificacionVerificacion = CalificacionVerificacion::where([
                            'docente_dni'   => $docente->dni,
                            'asignatura_id' => $asignatura->id,
                            'cohorte_id'    => $cohorte->id,
                        ])->first();

                        $editar = $calificacionVerificacion?->editar ?? false;

                        $aulaId     = $cohorte->aula?->id;
                        $paraleloId = $cohorte->aula?->paralelo;

                        return [
                            'nombre'       => $cohorte->nombre,
                            'aula'         => $cohorte->aula?->nombre ?? 'Sin aula',
                            'paralelo'     => $cohorte->aula?->paralelo ?? 'Sin paralelo',
                            'fechaLimite'  => $fechaLimite,
                            'docenteId'    => $docente->dni,
                            'asignaturaId' => $asignatura->id,
                            'cohorteId'    => $cohorte->id,

                            'pdfNotasUrl'  => $existenNotas ? route('pdf.notas.asignatura', [
                                'docenteId'    => $docente->dni,
                                'asignaturaId' => $asignatura->id,
                                'cohorteId'    => $cohorte->id,
                                'aulaId'       => $aulaId,
                                'paraleloId'   => $paraleloId,
                            ]) : null,

                            'excelUrl' => route('exportar.excel', [
                                'docenteId'    => $docente->dni,
                                'asignaturaId' => $asignatura->id,
                                'cohorteId'    => $cohorte->id,
                                'aulaId'       => $aulaId,
                                'paraleloId'   => $paraleloId,
                            ]),

                            'calificarUrl' => $editar ? route('calificaciones.create1', [
                                'docente_id'    => $docente->dni,
                                'asignatura_id' => $asignatura->id,
                                'cohorte_id'    => $cohorte->id,
                                'aula_id'       => $aulaId,
                                'paralelo_id'   => $paraleloId,
                                'notasExisten'  => $existenNotas,
                            ]) : null,

                            'alumnos' => $cohorte->matriculas
                                ->where('asignatura_id', $asignatura->id)
                                ->unique('alumno_dni')
                                ->map(function ($matricula) use ($docente, $asignatura, $cohorte) {
                                    $alumno = $matricula->alumno;

                                    // 🚀 Opción 1: dejar así (mínimo impacto si son pocos alumnos)
                                    $nota = Nota::where([
                                        'alumno_dni'   => $alumno->dni,
                                        'docente_dni'  => $docente->dni,
                                        'asignatura_id'=> $asignatura->id,
                                        'cohorte_id'   => $cohorte->id,
                                    ])->first();

                                    return [
                                        'imagen'         => asset($alumno->image),
                                        'nombreCompleto' => trim("{$alumno->apellidop} {$alumno->apellidom} {$alumno->nombre1} {$alumno->nombre2}"),
                                        'verNotasUrl'    => route('calificaciones.show1', [
                                            'alumno_dni'   => $alumno->dni,
                                            'docente_id'   => $docente->dni,
                                            'asignatura_id'=> $asignatura->id,
                                            'cohorte_id'   => $cohorte->id,
                                        ]),
                                        'notas' => [
                                            'nota_actividades' => $nota->nota_actividades ?? 'N/A',
                                            'nota_practicas'   => $nota->nota_practicas   ?? 'N/A',
                                            'nota_autonomo'    => $nota->nota_autonomo    ?? 'N/A',
                                            'examen_final'     => $nota->examen_final     ?? 'N/A',
                                            'recuperacion'     => $nota->recuperacion     ?? 'N/A',
                                            'total'            => $nota->total            ?? 'N/A',
                                        ],
                                    ];
                                }),
                        ];
                    }),
            ];
        });

        return view('dashboard.docente', compact('docente', 'perPage', 'data'));
    }

    public function updateSilabo(Request $request)
    {
        $request->validate([
            'asignatura_id' => 'required|exists:asignaturas,id',
            'silabo'        => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $asignatura = Asignatura::findOrFail($request->input('asignatura_id'));

        if ($request->hasFile('silabo')) {
            // Asegura que trabajamos en el mismo disco donde se guarda
            if ($asignatura->silabo && Storage::disk('public')->exists($asignatura->silabo)) {
                Storage::disk('public')->delete($asignatura->silabo);
            }
            $path = $request->file('silabo')->store('silabos', 'public');
            $asignatura->silabo = $path;
        }

        $asignatura->save();

        return redirect()->route('inicio')->with('success', 'Sílabo actualizado correctamente.');
    }

    public function exportarExcel($docenteId, $asignaturaId, $cohorteId, $aulaId = null, $paraleloId = null)
    {
        // Eager loading FILTRADO para que la primera matrícula corresponda al mismo contexto
        $alumnos = Alumno::whereHas('matriculas', function ($q) use ($asignaturaId, $cohorteId, $docenteId) {
                $q->where('asignatura_id', $asignaturaId)
                  ->where('cohorte_id', $cohorteId)
                  ->where('docente_dni', $docenteId);
            })
            ->with([
                'matriculas' => function ($q) use ($asignaturaId, $cohorteId, $docenteId) {
                    $q->where('asignatura_id', $asignaturaId)
                      ->where('cohorte_id', $cohorteId)
                      ->where('docente_dni', $docenteId)
                      ->with([
                          'asignatura:id,nombre',
                          'cohorte:id,nombre,maestria_id,aula_id',
                          'cohorte.maestria:id,nombre',
                          'docente:dni,nombre1,nombre2,apellidop,apellidom,email',
                      ]);
                },
            ]);

        if (!is_null($aulaId)) {
            $alumnos->whereHas('matriculas.cohorte.aula', function ($q) use ($aulaId) {
                $q->where('id', $aulaId);
            });
        }

        $alumnosMatriculados = $alumnos->get()
            ->sortBy(fn($alumno) => "{$alumno->apellidop} {$alumno->apellidom} {$alumno->nombre1}")
            ->values();

        // Datos de encabezado seguros (con el eager loading filtrado ya corresponden al contexto)
        $primerAlumno   = $alumnosMatriculados->first();
        $primMatricula  = $primerAlumno?->matriculas->first();
        $nombreCohorte  = $primMatricula?->cohorte->nombre ?? 'sin_cohorte';
        $asignaturaNom  = $primMatricula?->asignatura->nombre ?? 'sin_asignatura';
        $maestriaNom    = $primMatricula?->cohorte->maestria->nombre ?? 'sin_maestria';

        if ($aulaId) {
            $aula       = Aula::find($aulaId);
            $nombreAula = $aula->nombre ?? 'sin_aula';
            $paralelo   = $aula->paralelo ?? 'sin_paralelo';
        } else {
            $nombreAula = 'sin_aula';
            $paralelo   = 'sin_paralelo';
        }

        return Excel::download(
            new AlumnosExport($alumnosMatriculados, $maestriaNom, $nombreCohorte, $asignaturaNom),
            "alumnos_{$nombreCohorte}_{$asignaturaNom}_{$nombreAula}_{$paralelo}.xlsx"
        );
    }
}
