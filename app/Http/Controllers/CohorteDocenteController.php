<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maestria;
use App\Models\Asignatura;
use App\Models\Docente;
use App\Models\Cohorte;
use App\Models\CohorteDocente;
use App\Models\Nota;
use App\Models\CalificacionVerificacion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CohorteDocenteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($docente_dni, $asignatura_id = null)
    {
        // Buscar al docente por su DNI
        $docente = Docente::where('dni', $docente_dni)->first();

        // Si el docente no existe, redirigir con un mensaje de error
        if (!$docente) {
            return redirect()->route('docentes.index')->with('error', 'Docente no encontrado.');
        }

        // Inicializar el arreglo $maestriaCohortes
        $maestriaCohortes = [];

        // Obtener todos los cohortes asignados al docente
        $cohortesAsignados = $docente->cohortes->pluck('id')->toArray();

        if ($asignatura_id) {
            // Si se proporciona $asignatura_id, obtener cohortes para esa asignatura específica
            $asignatura = Asignatura::find($asignatura_id);
            
            if (!$asignatura) {
                return redirect()->route('docentes.index')->with('error', 'Asignatura no encontrada.');
            }

            $maestria = $asignatura->maestria;
            $cohortes = $maestria->cohortes()->whereDate('fecha_fin', '>', Carbon::now())->get();

            if ($cohortes->isNotEmpty()) {
                $maestriaCohortes[] = [
                    'maestria' => $maestria,
                    'asignaturas' => [$asignatura],
                    'cohortes' => $cohortes
                ];
            }
        } else {
            // Agrupar asignaturas por maestría y filtrar cohortes válidas
            $asignaturas = $docente->asignaturas->groupBy('maestria_id');

            foreach ($asignaturas as $maestriaId => $asignaturasPorMaestria) {
                $maestria = Maestria::find($maestriaId);
                
                if (!$maestria) {
                    continue;
                }

                $cohortes = $maestria->cohortes()->whereDate('fecha_fin', '>', Carbon::now())->get();

                if ($cohortes->isNotEmpty()) {
                    $maestriaCohortes[] = [
                        'maestria' => $maestria,
                        'asignaturas' => $asignaturasPorMaestria,
                        'cohortes' => $cohortes
                    ];
                }
            }
        }

        return view('cohortes_docentes.create', compact('docente', 'maestriaCohortes', 'asignatura_id', 'cohortesAsignados'));
    }

    public function store(Request $request)
    {
        $docenteDni = $request->input('docente_dni');
        $asignaturaCohortePairs = $request->input('asignatura_cohorte', []);

        try {
            // Obtener el docente por su DNI
            $docente = Docente::where('dni', $docenteDni)->firstOrFail();

            // Obtener todos los cohortes asignados actualmente al docente
            $cohortesActuales = $docente->cohortes->pluck('id')->toArray();

            // Convertir el input de cohortes en un array plano de IDs de cohortes seleccionados
            $cohortesSeleccionados = [];
            foreach ($asignaturaCohortePairs as $asignaturaId => $cohorteIds) {
                $cohortesSeleccionados = array_merge($cohortesSeleccionados, $cohorteIds);
            }

            // Encontrar cohortes para desasignar (están en actuales pero no en seleccionados)
            $cohortesADesasignar = array_diff($cohortesActuales, $cohortesSeleccionados);

            // Remover los cohortes que ya no están seleccionados
            if (!empty($cohortesADesasignar)) {
                foreach ($cohortesADesasignar as $cohorteId) {
                    $asignaturasIds = CohorteDocente::where('cohorte_id', $cohorteId)
                                                    ->where('docente_dni', $docenteDni)
                                                    ->pluck('asignatura_id');

                    foreach ($asignaturasIds as $asignaturaId) {
                        // Eliminar el registro de CohorteDocente
                        CohorteDocente::where([
                            'cohorte_id' => $cohorteId,
                            'docente_dni' => $docenteDni,
                            'asignatura_id' => $asignaturaId,
                        ])->delete();

                        // Verificar si existen notas
                        $notasExistentes = Nota::where([
                            'cohorte_id' => $cohorteId,
                            'docente_dni' => $docenteDni,
                            'asignatura_id' => $asignaturaId,
                        ])->exists();

                        // Actualizar el registro de CalificacionVerificacion
                        if ($notasExistentes) {
                            CalificacionVerificacion::where([
                                'cohorte_id' => $cohorteId,
                                'docente_dni' => $docenteDni,
                                'asignatura_id' => $asignaturaId,
                            ])->update([
                                'calificado' => true,
                                'editar' => false,
                            ]);
                        } else {
                            CalificacionVerificacion::where([
                                'cohorte_id' => $cohorteId,
                                'docente_dni' => $docenteDni,
                                'asignatura_id' => $asignaturaId,
                            ])->delete();
                        }
                    }
                }
            }

            // Asignar los nuevos cohortes seleccionados al docente
            foreach ($asignaturaCohortePairs as $asignaturaId => $cohorteIds) {
                foreach ($cohorteIds as $cohorteId) {
                    if (!empty($cohorteId)) {
                        CohorteDocente::updateOrCreate(
                            [
                                'cohort_id' => $cohorteId,
                                'docente_dni' => $docenteDni,
                                'asignatura_id' => $asignaturaId,
                            ]
                        );

                        // Verificar si existen notas
                        $notasExistentes = Nota::where([
                            'cohorte_id' => $cohorteId,
                            'docente_dni' => $docenteDni,
                            'asignatura_id' => $asignaturaId,
                        ])->exists();

                        // Actualizar o crear el registro de CalificacionVerificacion
                        CalificacionVerificacion::updateOrCreate(
                            [
                                'cohorte_id' => $cohorteId,
                                'docente_dni' => $docenteDni,
                                'asignatura_id' => $asignaturaId,
                            ],
                            [
                                'calificado' => $notasExistentes,
                                'editar' => !$notasExistentes,
                            ]
                        );
                    }
                }
            }

            return redirect()->route('docentes.index')->with('success', 'Cohortes añadidos con éxito.');
        } catch (ModelNotFoundException $e) {
            // Manejar el caso donde no se encuentre el docente
            return redirect()->route('docentes.index')->with('error', 'Docente no encontrado.');
        } catch (\Exception $e) {
            // Manejar cualquier otro error
            return redirect()->route('docentes.index')->with('error', 'Ocurrió un error al actualizar los cohortes.');
        }
    }
}
