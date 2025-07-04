<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\Cohorte;
use App\Models\Maestria;
use App\Models\TasaTitulacion;
use App\Models\Asignatura;
use Illuminate\Support\Facades\Log;
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

        $cohortes = $maestria->cohortes
            ->filter(function ($cohorte) {
                return $cohorte->aforo > 0;
            })
            ->sortByDesc('id');

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
        $request->validate([
            'cohorte_id' => 'required|exists:cohortes,id',
            'alumno_dni' => 'required|exists:alumnos,dni',
        ]);

        try {
            DB::beginTransaction();

            // Obtener el cohorte
            $cohorte = Cohorte::find($request->cohorte_id, ['id', 'aforo', 'maestria_id']);

            if (!$cohorte || $cohorte->aforo <= 0) {
                return redirect()->back()->with('error', 'No hay cupo disponible en este cohorte.');
            }

            // Obtener todas las asignaturas asociadas a la maestría del cohorte
            $asignaturas = Asignatura::where('maestria_id', $cohorte->maestria_id)
                ->with('docentes') // Cargar los docentes asociados
                ->get();

            // Filtrar las asignaturas que el alumno aún no tiene matriculadas
            $asignaturasNoMatriculadas = $asignaturas->reject(function ($asignatura) use ($request, $cohorte) {
                return Matricula::where('alumno_dni', $request->alumno_dni)
                    ->where('asignatura_id', $asignatura->id)
                    ->where('cohorte_id', $cohorte->id)
                    ->exists();
            });

            if ($asignaturasNoMatriculadas->isEmpty()) {
                return redirect()->back()->with('info', 'El alumno ya está matriculado en todas las asignaturas de este cohorte.');
            }

            // Crear las matrículas para las asignaturas no matriculadas
            $matriculas = $asignaturasNoMatriculadas->map(function ($asignatura) use ($request, $cohorte) {
                // Se asume que la asignatura tiene un docente asignado
                return [
                    'alumno_dni' => $request->alumno_dni,
                    'asignatura_id' => $asignatura->id,
                    'cohorte_id' => $cohorte->id,
                    'docente_dni' => optional($asignatura->docentes->first())->dni, // Obtener el primer docente asignado
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            // Insertar las matrículas
            Matricula::insert($matriculas->toArray());

            // Reducir el aforo del cohorte
            $cohorte->decrement('aforo');

            // Buscar o crear la tasa de titulación para el cohorte y la maestría
            $tasaTitulacion = TasaTitulacion::where('cohorte_id', $cohorte->id)
                ->where('maestria_id', $cohorte->maestria_id)
                ->first();

            if ($tasaTitulacion) {
                $tasaTitulacion->numero_matriculados += 1;
                $tasaTitulacion->save();
            } else {
                // Si no existe, lo creamos con valores iniciales
                TasaTitulacion::create([
                    'cohorte_id' => $cohorte->id,
                    'maestria_id' => $cohorte->maestria_id,
                    'numero_matriculados' => 1,
                ]);
            }

            DB::commit();
            return redirect(route('alumnos.index'))->with('success', 'Matrícula realizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect(route('alumnos.index'))->with('error', 'Error en la matriculación. Intente nuevamente.');
        }
    }
}
