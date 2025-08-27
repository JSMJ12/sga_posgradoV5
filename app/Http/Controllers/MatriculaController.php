<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\Cohorte;
use App\Models\Maestria;
use App\Models\TasaTitulacion;
use App\Models\Asignatura;
use Illuminate\Support\Facades\DB;

class MatriculaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar formulario de creación de matrícula
     */
    public function create($alumno_dni, $maestria_id)
    {
        $alumno = Alumno::where('dni', $alumno_dni)->firstOrFail();
        $maestria = Maestria::findOrFail($maestria_id);

        // Cohortes disponibles para la maestría
        $cohortes = $maestria->cohortes
            ->filter(fn($cohorte) => $cohorte->aforo > 0)
            ->sortByDesc('id');

        // Verificar si el alumno ya está matriculado en esta maestría
        $estaMatriculado = $this->verificarMatriculacion($alumno, $maestria_id);

        if ($estaMatriculado) {
            return redirect()->back()->with('error', 'El alumno ya está matriculado en algún cohorte de esta maestría.');
        }

        return view('matriculas.create', compact('alumno', 'maestria', 'cohortes'));
    }

    /**
     * Verificar si el alumno ya tiene matrícula en la maestría indicada
     */
    private function verificarMatriculacion($alumno, $maestria_id)
    {
        return $alumno->matriculas()
            ->whereHas('cohorte', fn($q) => $q->where('maestria_id', $maestria_id))
            ->exists();
    }

    /**
     * Guardar matrículas
     */
    public function store(Request $request)
    {
        $request->validate([
            'cohorte_id' => 'required|exists:cohortes,id',
            'alumno_dni' => 'required|exists:alumnos,dni',
        ]);

        try {
            DB::beginTransaction();

            $cohorte = Cohorte::findOrFail($request->cohorte_id);

            if ($cohorte->aforo <= 0) {
                return redirect()->back()->with('error', 'No hay cupo disponible en este cohorte.');
            }

            // Asignaturas de la maestría del cohorte
            $asignaturas = Asignatura::where('maestria_id', $cohorte->maestria_id)
                ->with('docentes')
                ->get();

            // Filtrar asignaturas que el alumno aún no tiene matriculadas
            $asignaturasNoMatriculadas = $asignaturas->reject(fn($asignatura) =>
                Matricula::where('alumno_dni', $request->alumno_dni)
                    ->where('asignatura_id', $asignatura->id)
                    ->where('cohorte_id', $cohorte->id)
                    ->exists()
            );

            if ($asignaturasNoMatriculadas->isEmpty()) {
                return redirect()->back()->with('info', 'El alumno ya está matriculado en todas las asignaturas de este cohorte.');
            }

            // Crear matrículas
            $matriculas = $asignaturasNoMatriculadas->map(fn($asignatura) => [
                'alumno_dni' => $request->alumno_dni,
                'asignatura_id' => $asignatura->id,
                'cohorte_id' => $cohorte->id,
                'docente_dni' => optional($asignatura->docentes->first())->dni,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Matricula::insert($matriculas->toArray());

            // Reducir aforo
            $cohorte->decrement('aforo');

            // Actualizar o crear tasa de titulación
            $tasa = TasaTitulacion::firstOrCreate(
                ['cohorte_id' => $cohorte->id, 'maestria_id' => $cohorte->maestria_id],
                ['numero_matriculados' => 0]
            );
            $tasa->increment('numero_matriculados');

            DB::commit();
            return redirect(route('alumnos.index'))->with('success', 'Matrícula realizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect(route('alumnos.index'))->with('error', 'Error en la matriculación. Intente nuevamente.');
        }
    }
}
