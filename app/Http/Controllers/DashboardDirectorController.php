<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maestria;
use App\Models\Docente;
use App\Models\Alumno;
use App\Models\Postulante;
use App\Models\User;

class DashboardDirectorController extends Controller
{
    // Dentro de DashboardDirectorController

    public function index()
    {
        $maestrias = Maestria::all();

        return view('dashboard.director', compact('maestrias'));
    }

    public function getMaestriaResumen(Request $request, $maestriaId)
    {
        $maestria = Maestria::with(['cohortes.matriculas.alumno', 'asignaturas'])->findOrFail($maestriaId);

        $totalAlumnos = Alumno::where('maestria_id', $maestria->id)->count();
        $totalPostulantes = Postulante::where('maestria_id', $maestria->id)->count();
        $totalDocentes = Docente::whereHas('asignaturas', function ($query) use ($maestria) {
            $query->whereHas('maestria', function ($q) use ($maestria) {
                $q->where('id', $maestria->id);
            });
        })->count();

        $cohortesMaestria = $maestria->cohortes()->with('matriculas.alumno')->get();

        $cohortesResumen = [];

        foreach ($cohortesMaestria as $cohorte) {
            $cohorteKey = $maestria->nombre . ' - ' . $cohorte->nombre;

            $alumnosUnicos = $cohorte->matriculas->unique('alumno_dni');
            $cantidadAlumnos = $alumnosUnicos->count();

            $pagosCohorte = collect();

            foreach ($alumnosUnicos as $matricula) {
                $alumno = $matricula->alumno;
                if ($alumno) {
                    $userPago = User::where('email', $alumno->email_institucional)->first();
                    if ($userPago) {
                        $pagosVerificados = $userPago->pagos()->where('verificado', '1')->get();
                        $pagosCohorte = $pagosCohorte->merge($pagosVerificados);
                    }
                }
            }

            $deudaArancel = $cantidadAlumnos * $maestria->arancel;
            $deudaMatricula = $cantidadAlumnos * $maestria->matricula;
            $deudaInscripcion = $cantidadAlumnos * $maestria->inscripcion;
            $recaudadoArancel = $pagosCohorte->where('tipo_pago', 'arancel')->sum('monto');
            $recaudadoMatricula = $pagosCohorte->where('tipo_pago', 'matricula')->sum('monto');
            $recaudadoInscripcion = $pagosCohorte->where('tipo_pago', 'inscripcion')->sum('monto');

            $cohortesResumen[] = [
                'id' => $cohorte->id,
                'nombre' => $cohorteKey,
                'deudaArancel' => $deudaArancel,
                'recaudadoArancel' => $recaudadoArancel,
                'deudaMatricula' => $deudaMatricula,
                'recaudadoMatricula' => $recaudadoMatricula,
                'deudaInscripcion' => $deudaInscripcion,
                'recaudadoInscripcion' => $recaudadoInscripcion,
            ];
        }

        return response()->json([
            'maestria' => [
                'id' => $maestria->id,
                'nombre' => $maestria->nombre,
                'totalAlumnos' => $totalAlumnos,
                'totalPostulantes' => $totalPostulantes,
                'totalDocentes' => $totalDocentes,
                'cohortesResumen' => $cohortesResumen,
            ],
        ]);
    }
}
