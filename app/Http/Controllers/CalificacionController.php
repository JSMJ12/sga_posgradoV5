<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nota;
use App\Models\Matricula;
use App\Models\Docente;
use App\Models\CalificacionVerificacion;
use App\Models\Cohorte;
use App\Models\User;
use App\Models\Alumno;
use Spatie\Permission\Models\Role;

class CalificacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($docente_dni, $asignatura_id, $cohorte_id)
    {
        $cohorte = Cohorte::findOrFail($cohorte_id);
        $aforoMaximo = $cohorte->aforo;

        // Obtener matrículas con relación alumno
        $matriculas = Matricula::where([
            'docente_dni' => $docente_dni,
            'asignatura_id' => $asignatura_id,
            'cohorte_id' => $cohorte_id
        ])->with('alumno')
        ->get();

        // Extraer y ordenar alumnos por apellido y nombre
        $alumnos = $matriculas->pluck('alumno')
            ->sortBy(fn($a) => $a->apellidop . ' ' . $a->apellidom . ' ' . $a->nombre1 . ' ' . $a->nombre2)
            ->values(); // resetear índices

        // Traer notas de esos alumnos
        $notas = Nota::whereIn('alumno_dni', $alumnos->pluck('dni'))
            ->where([
                'docente_dni' => $docente_dni,
                'asignatura_id' => $asignatura_id,
                'cohorte_id' => $cohorte_id
            ])->get()
            ->keyBy('alumno_dni');

        return view('calificaciones.create', compact('alumnos', 'aforoMaximo', 'notas', 'docente_dni', 'asignatura_id', 'cohorte_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'docente_dni' => 'required|string',
            'asignatura_id' => 'required|integer',
            'cohorte_id' => 'required|integer',
            'alumno_dni' => 'nullable|array',
            'nota_actividades' => 'nullable|array',
            'nota_practicas' => 'nullable|array',
            'nota_autonomo' => 'nullable|array',
            'examen_final' => 'nullable|array',
            'recuperacion' => 'nullable|array',
            'total' => 'nullable|array',
            'nuevo_alumno.dni' => 'nullable|array',
            'nuevo_alumno.nombre1' => 'nullable|array',
            'nuevo_alumno.nombre2' => 'nullable|array',
            'nuevo_alumno.apellidop' => 'nullable|array',
            'nuevo_alumno.apellidom' => 'nullable|array',
        ]);

        $docenteDni = $request->input('docente_dni');
        $asignaturaId = $request->input('asignatura_id');
        $cohorteId = $request->input('cohorte_id');
        $cohorte = Cohorte::findOrFail($cohorteId);
        $alumnoDnis = $request->input('alumno_dni', []);
        $notas = $request->only(['nota_actividades', 'nota_practicas', 'nota_autonomo', 'examen_final', 'recuperacion', 'total']);
        $nuevosAlumnos = $request->input('nuevo_alumno', []);
        $nuevosAlumnos = array_map(function ($field) {
            return array_filter($field, fn($value) => !is_null($value));
        }, $nuevosAlumnos);
        $calificacionVerificacion = CalificacionVerificacion::where([
            'docente_dni' => $docenteDni,
            'asignatura_id' => $asignaturaId,
            'cohorte_id' => $cohorteId
        ])->first();

        if ($calificacionVerificacion) {
            $calificacionVerificacion->calificado = 1;
            $calificacionVerificacion->editar = 0;
            $calificacionVerificacion->save();
        }

        // Actualización o creación de notas para alumnos existentes
        foreach ($alumnoDnis as $alumnoDni) {
            $updateData = array_filter([
                'nota_actividades' => $notas['nota_actividades'][$alumnoDni] ?? null,
                'nota_practicas' => $notas['nota_practicas'][$alumnoDni] ?? null,
                'nota_autonomo' => $notas['nota_autonomo'][$alumnoDni] ?? null,
                'examen_final' => $notas['examen_final'][$alumnoDni] ?? null,
                'recuperacion' => $notas['recuperacion'][$alumnoDni] ?? null,
                'total' => $notas['total'][$alumnoDni] ?? null,
            ]);

            Nota::updateOrCreate(
                [
                    'docente_dni' => $docenteDni,
                    'alumno_dni' => $alumnoDni,
                    'asignatura_id' => $asignaturaId,
                    'cohorte_id' => $cohorteId,
                ],
                $updateData
            );
        }


        return redirect()->route('dashboard_docente')->with('success', 'Calificaciones almacenadas exitosamente');
    }

    public function edit($alumno_dni, $docente_dni, $asignatura_id, $cohorte_id)
    {
        $calificacionVerificacion = CalificacionVerificacion::where([
            'docente_dni' => $docente_dni,
            'asignatura_id' => $asignatura_id,
            'cohorte_id' => $cohorte_id
        ])->first();

        $tienePermisoEditar = $calificacionVerificacion ? $calificacionVerificacion->editar : false;

        $nota = Nota::where([
            'cohorte_id' => $cohorte_id,
            'asignatura_id' => $asignatura_id,
            'docente_dni' => $docente_dni,
            'alumno_dni' => $alumno_dni
        ])->firstOrFail();

        return view('calificaciones.edit', compact('nota', 'tienePermisoEditar'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nota_actividades' => 'nullable|numeric',
            'nota_practicas' => 'nullable|numeric',
            'nota_autonomo' => 'nullable|numeric',
            'examen_final' => 'nullable|numeric',
            'recuperacion' => 'nullable|numeric',
            'total' => 'nullable|numeric',
        ]);

        $nota = Nota::findOrFail($id);
        $nota->update($request->only('nota_actividades', 'nota_practicas', 'nota_autonomo', 'examen_final', 'recuperacion', 'total'));

        return redirect()->route('calificaciones.show1', [
            $nota->alumno_dni,
            $nota->docente_dni,
            $nota->asignatura_id,
            $nota->cohorte_id
        ])->with('success', 'La nota ha sido actualizada exitosamente.');
    }

    public function show($alumno_dni, $docente_dni, $asignatura_id, $cohorte_id)
    {
        $calificacionVerificacion = CalificacionVerificacion::where([
            'docente_dni' => $docente_dni,
            'asignatura_id' => $asignatura_id,
            'cohorte_id' => $cohorte_id
        ])->first();

        $tienePermisoVerNotas = $calificacionVerificacion ? $calificacionVerificacion->editar : false;

        $notas = Nota::where([
            'cohorte_id' => $cohorte_id,
            'asignatura_id' => $asignatura_id,
            'docente_dni' => $docente_dni,
            'alumno_dni' => $alumno_dni
        ])->get();

        $cohorte = Cohorte::with('periodo_academico')->find($cohorte_id);
        $fechaLimite = $cohorte->periodo_academico->fecha_fin->addWeek();

        return view('calificaciones.show', compact('notas', 'fechaLimite', 'tienePermisoVerNotas'));
    }
}
