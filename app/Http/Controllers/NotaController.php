<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nota;
use App\Models\Asignatura;
use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\TasaTitulacion;
use App\Models\User;

class NotaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function show($alumno_dni)
    {
        $alumno = Alumno::find($alumno_dni);
        $notas = Nota::where('alumno_dni', $alumno_dni)->with('asignatura')->get();

        return view('notas.index', compact('alumno', 'notas'));
    }

    public function create($alumno_dni)
    {
        $alumno = Alumno::findOrFail($alumno_dni);

        $matriculas = $alumno->matriculas;
        $asignaturas = $matriculas->map(function ($matricula) {
            return $matricula->asignatura;
        })->unique('id');

        return view('notas.create', compact('alumno', 'asignaturas'));
    }

    public function store(Request $request)
    {
        $alumno_dni = $request->input('alumno_dni');
        $notas_actividades = $request->input('nota_actividades');
        $notas_practicas = $request->input('nota_practicas');
        $notas_autonomo = $request->input('nota_autonomo');
        $examenes_finales = $request->input('examen_final');
        $recuperaciones = $request->input('recuperacion');

        try {
            $alumno = Alumno::findOrFail($alumno_dni);

            foreach ($notas_actividades as $asignatura_id => $nota_actividades) {
                $asignatura = Asignatura::findOrFail($asignatura_id);
                $docente_dni = optional($asignatura->docentes->first())->dni;

                if (!$docente_dni) {
                    return redirect(route('notas.create', ['alumno_dni' => $alumno_dni]))
                        ->with('error', "No se encontró un docente asignado a la asignatura ID: $asignatura_id");
                }

                $matricula = Matricula::where('alumno_dni', $alumno_dni)
                    ->where('asignatura_id', $asignatura_id)
                    ->whereHas('cohorte', function ($query) use ($docente_dni) {
                        $query->where('docente_dni', $docente_dni);
                    })
                    ->first();

                if (!$matricula) {
                    return redirect(route('notas.create', ['alumno_dni' => $alumno_dni]))
                        ->with('error', "No se encontró matrícula para la asignatura ID: $asignatura_id del alumno DNI: $alumno_dni");
                }

                $cohorte = $matricula->cohorte;

                // ✅ Calcular el total sin recuperación
                $total = $nota_actividades
                    + $notas_practicas[$asignatura_id]
                    + $notas_autonomo[$asignatura_id]
                    + $examenes_finales[$asignatura_id];

                Nota::updateOrCreate(
                    [
                        'alumno_dni' => $alumno_dni,
                        'asignatura_id' => $asignatura_id,
                        'cohorte_id' => $cohorte->id,
                        'docente_dni' => $docente_dni,
                    ],
                    [
                        'nota_actividades' => $nota_actividades,
                        'nota_practicas' => $notas_practicas[$asignatura_id],
                        'nota_autonomo' => $notas_autonomo[$asignatura_id],
                        'examen_final' => $examenes_finales[$asignatura_id],
                        'recuperacion' => $recuperaciones[$asignatura_id] ?? null,
                        'total' => $total,
                    ]
                );
            }

            return redirect()->route('notas.show', $alumno_dni)
                ->with('success', 'Notas guardadas exitosamente.');
        } catch (\Exception $e) {
            return redirect(route('notas.create', ['alumno_dni' => $alumno_dni]))
                ->with('error', 'Ocurrió un error al guardar las notas. Por favor, revisa los datos ingresados.');
        }
    }

    public function destroy($id)
    {
        $nota = Nota::findOrFail($id);
        $nota->delete();
        return redirect()->route('notas.index')
            ->with('success', 'Nota eliminada exitosamente.');
    }
}
