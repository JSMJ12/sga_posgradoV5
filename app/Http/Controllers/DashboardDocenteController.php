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

class DashboardDocenteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $user = auth()->user();
        $docente = Docente::where('email', $user->email)->firstOrFail();
        $asignaturas = $docente->asignaturas;
    
        $data = $asignaturas->map(function ($asignatura) use ($docente) {
            return [
                'nombre' => $asignatura->nombre,
                'id' => $asignatura->id,
                'silabo' => $asignatura->silabo,
                'cohortes' => $asignatura->cohortes->sortBy('periodo_academico.fecha_fin')->map(function ($cohorte) use ($docente, $asignatura) {
                    $fechaFinCohorte = $cohorte->periodo_academico->fecha_fin;
                    $fechaLimite = $fechaFinCohorte->addWeek();
        
                    // Obtener las notas existen array
                    $notasExisten = [];
        
                    $notasExisten[$cohorte->id] = Nota::where([
                        'docente_dni' => $docente->dni,
                        'asignatura_id' => $asignatura->id,
                        'cohorte_id' => $cohorte->id,
                    ])->exists();
        
                    $calificacionVerificacion = CalificacionVerificacion::where([
                        'docente_dni' => $docente->dni,
                        'asignatura_id' => $asignatura->id,
                        'cohorte_id' => $cohorte->id,
                    ])->first();
        
                    $editar = $calificacionVerificacion ? $calificacionVerificacion->editar : false;
        
                    $aulaId = $cohorte->aula ? $cohorte->aula->id : null;
                    $paraleloId = $cohorte->aula && $cohorte->aula->paralelo ? $cohorte->aula->paralelo : null;
        
                    return [
                        'nombre' => $cohorte->nombre,
                        'aula' => $cohorte->aula ? $cohorte->aula->nombre : 'Sin aula',
                        'paralelo' => $cohorte->aula && $cohorte->aula->paralelo ? $cohorte->aula->paralelo : 'Sin paralelo',
                        'fechaLimite' => $fechaLimite,
                        'docenteId' => $docente->dni,
                        'asignaturaId' => $asignatura->id,
                        'cohorteId' => $cohorte->id,
                        'pdfNotasUrl' => $notasExisten[$cohorte->id] ? route('pdf.notas.asignatura', [
                            'docenteId' => $docente->dni,
                            'asignaturaId' => $asignatura->id,
                            'cohorteId' => $cohorte->id,
                            'aulaId' => $aulaId,
                            'paraleloId' => $paraleloId,
                        ]) : null,
                        'excelUrl' => route('exportar.excel', [
                            'docenteId' => $docente->dni,
                            'asignaturaId' => $asignatura->id,
                            'cohorteId' => $cohorte->id,
                            'aulaId' => $aulaId ?? null,
                            'paraleloId' => $paraleloId ?? null,
                        ]),
                        'calificarUrl' => $editar ? route('calificaciones.create1', [
                            'docente_id' => $docente->dni,
                            'asignatura_id' => $asignatura->id,
                            'cohorte_id' => $cohorte->id,
                            'aula_id' => $aulaId,
                            'paralelo_id' => $paraleloId,
                            'notasExisten' => $notasExisten[$cohorte->id],
                        ]) : null,
                        'alumnos' => $cohorte->matriculas->where('asignatura_id', $asignatura->id)->unique('alumno_dni')->map(function ($matricula) use ($docente, $asignatura, $cohorte) {
                            // Obtener las notas del alumno
                            $notas = Nota::where([
                                'alumno_dni' => $matricula->alumno->dni,
                                'docente_dni' => $docente->dni,
                                'asignatura_id' => $asignatura->id,
                                'cohorte_id' => $cohorte->id,
                            ])->first();

                            return [
                                'imagen' => asset($matricula->alumno->image),
                                'nombreCompleto' => $matricula->alumno->apellidop . ' ' . $matricula->alumno->apellidom . ' ' . $matricula->alumno->nombre1 . ' ' . $matricula->alumno->nombre2,
                                'verNotasUrl' => route('calificaciones.show1', [
                                    'alumno_id' => $matricula->alumno->dni,
                                    'docente_id' => $docente->dni,
                                    'asignatura_id' => $asignatura->id,
                                    'cohorte_id' => $cohorte->id,
                                ]),
                                'notas' => [
                                    'nota_actividades' => $notas ? $notas->nota_actividades : 'N/A',
                                    'nota_practicas' => $notas ? $notas->nota_practicas : 'N/A',
                                    'nota_autonomo' => $notas ? $notas->nota_autonomo : 'N/A',
                                    'examen_final' => $notas ? $notas->examen_final : 'N/A',
                                    'recuperacion' => $notas ? $notas->recuperacion : 'N/A',
                                    'total' => $notas ? $notas->total : 'N/A',
                                ]
                            ];
                        }),

                    ];
                }),
            ];
        });
        return view('dashboard.docente', compact('docente', 'asignaturas', 'perPage', 'data'));
    }
    public function updateSilabo(Request $request)
    {
        $request->validate([
            'asignatura_id' => 'required|exists:asignaturas,id',
            'silabo' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // Limita a archivos específicos
        ]);

        $asignatura = Asignatura::findOrFail($request->input('asignatura_id'));

        if ($request->hasFile('silabo')) {
            if ($asignatura->silabo && Storage::exists($asignatura->silabo)) {
                Storage::delete($asignatura->silabo);
            }
            $path = $request->file('silabo')->store('silabos', 'public');
            $asignatura->silabo = $path;
        }

        $asignatura->save();

        return redirect()->route('inicio')->with('success', 'Sílabo actualizado correctamente.');
    }
    public function exportarExcel($docenteId, $asignaturaId, $cohorteId, $aulaId = null, $paraleloId = null)
    {
        $query = Alumno::whereHas('matriculas', function ($query) use ($asignaturaId, $cohorteId, $docenteId) {
            $query->where('asignatura_id', $asignaturaId)
                ->where('cohorte_id', $cohorteId)
                ->where('docente_dni', $docenteId);
        })
        ->with(['matriculas', 'matriculas.asignatura', 'matriculas.cohorte', 'matriculas.docente']);

        if ($aulaId !== null) {
            $query->whereHas('matriculas.cohorte.aula', function ($q) use ($aulaId) {
                $q->where('id', $aulaId);
            });
        }

        if ($paraleloId !== null) {
            $query->whereHas('matriculas.cohorte.aula.paralelo', function ($q) use ($paraleloId) {
                $q->where('id', $paraleloId);
            });
        }

        $alumnosMatriculados = $query->get();

        $primerAlumno = $alumnosMatriculados->first();
        $nombreCohorte = $primerAlumno ? ($primerAlumno->matriculas->first()->cohorte->nombre ?? 'sin_cohorte') : 'sin_cohorte';
        $asignatura = $primerAlumno ? $primerAlumno->matriculas->first()->asignatura->nombre : 'sin_asignatura';

        $aula = $aulaId ? Aula::find($aulaId)->nombre : 'sin_aula';
        $paralelo =  $aulaId ? Aula::find($aulaId)->nombre->paralelo: 'sin_paralelo';

        return Excel::download(new AlumnosExport($alumnosMatriculados), "alumnos_{$nombreCohorte}_{$asignatura}_{$aula}_{$paralelo}.xlsx");
    }


}
