<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\Cohorte;
use App\Models\Maestria;
use App\Models\TasaTitulacion;
use Illuminate\Support\Facades\DB;

class MatriculaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($alumno_dni, $cohorte_id = null)
    {
        $alumno = Alumno::where('dni', $alumno_dni)->firstOrFail();
        $maestria = Maestria::findOrFail($alumno->maestria_id);

        // Filtrar cohortes con aforo mayor a 0
        $cohortes = $maestria->cohortes->filter(function ($cohorte) {
            return $cohorte->aforo > 0;
        });

        // Verificar si el alumno ya está matriculado en alguna asignatura de este cohorte
        $estaMatriculado = $this->verificarMatriculacion($alumno);

        if ($estaMatriculado) {
            return redirect()->back()->with('error', 'El alumno ya está matriculado en este cohorte.');
        }

        return view('matriculas.create', compact('alumno', 'cohortes'));
    }

    private function verificarMatriculacion($alumno)
    {
        $dni = $alumno->dni;
        // Verificar si el alumno está matriculado en alguna asignatura de este cohorte
        return $alumno->matriculas()->where('alumno_dni', $dni)->exists();
    }

    public function store(Request $request)
    {
        $cohorte_id = $request->input('cohorte_id');
        $asignatura_ids = $request->input('asignatura_ids');
        $docente_dnis = $request->input('docente_dnis');
        $alumno_dni = $request->input('alumno_dni');
        $cohorte = Cohorte::findOrFail($request->input('cohorte_id'));

        // verificar que el aforo del cohorte sea mayor a cero
        if ($cohorte->aforo > 0) {
            try {
                DB::beginTransaction();

                foreach ($asignatura_ids as $key => $asignatura_id) {
                    // Verificar si ya existe una matrícula con los mismos datos
                    $matriculaExistente = Matricula::where('alumno_dni', $alumno_dni)
                        ->where('asignatura_id', $asignatura_id)
                        ->where('cohorte_id', $cohorte_id)
                        ->where('docente_dni', $docente_dnis[$key])
                        ->first();

                    // Crear la matrícula solo si no existe una con los mismos datos
                    if (!$matriculaExistente) {
                        Matricula::create([
                            'alumno_dni' => $alumno_dni,
                            'asignatura_id' => $asignatura_id,
                            'cohorte_id' => $cohorte_id,
                            'docente_dni' => $docente_dnis[$key],
                        ]);
                    }
                }

                // Restar 1 al aforo del cohorte
                $cohorte->aforo = $cohorte->aforo - 1;
                $cohorte->save();
                $alumno = Alumno::where('dni', $alumno_dni)->first();

                if (!$alumno) {
                    return redirect()->back()->with('error', 'Alumno no encontrado');
                }

                $maestriaId = $alumno->maestria_id;

                // Buscar o crear la tasa de titulación para el cohorte y la maestría
                $tasaTitulacion = TasaTitulacion::where('cohorte_id', $cohorte_id)
                    ->where('maestria_id', $maestriaId)
                    ->first();

                if ($tasaTitulacion) {
                    $tasaTitulacion->numero_matriculados += 1; 
                    $tasaTitulacion->save(); 
    
                } else {
                    // Si no existe, lo creamos con valores iniciales
                    TasaTitulacion::create([
                        'cohorte_id' => $cohorte_id,
                        'maestria_id' => $maestriaId,
                        'numero_matriculados' => 1,
                    ]);
                }

                // Actualizar el aforo de las asignaturas en el cohorte
                $cohorte->asignaturas()->sync($asignatura_ids, false);

                // Commit de la transacción
                DB::commit();

                return redirect()->route('alumnos.index')->with('success', 'Matrícula exitosa.');
            } catch (\Exception $e) {
                // Si hay algún error, hacer rollback de la transacción
                DB::rollback();
                return redirect()->back()->with('error', 'Error en la matriculación: ' . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'No hay cupo disponible en este cohorte.');
        }
    }
}
