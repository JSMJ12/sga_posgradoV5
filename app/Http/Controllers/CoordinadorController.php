<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Maestria;
use App\Models\Docente;
use App\Models\Postulante;
use App\Models\Pago;
use App\Models\User;

class CoordinadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $docente = Docente::where('email', $user->email)->first();

        if (!$docente || $docente->maestria()->count() === 0) {
            return redirect()->route('dashboard')->with('error', 'No estás asignado a ninguna maestría.');
        }

        $maestria = $docente->maestria->first();
        $alumnos = Alumno::where('maestria_id', $maestria->id)->with('matriculas.cohorte')->get();
        $cantidadPostulantes = Postulante::where('maestria_id', $maestria->id)->count();
        $totalAlumnos = $alumnos->count();
        $totalPostulantes = $cantidadPostulantes;
        $totalDocentes = Docente::whereHas('asignaturas', function ($query) use ($maestria) {
            $query->whereHas('maestria', function ($query) use ($maestria) {
                $query->where('id', $maestria->id);
            });
        })->count();

        $cohortesMaestria = $maestria->cohortes()->with('matriculas.alumno')->get();
        $cohortes = [];
        $deudaArancelPorCohorte = [];
        $deudaMatriculaPorCohorte = [];
        $deudaInscripcionPorCohorte = [];
        $recaudadoArancelPorCohorte = [];
        $recaudadoMatriculaPorCohorte = [];
        $recaudadoInscripcionPorCohorte = [];

        foreach ($cohortesMaestria as $cohorte) {
            $cohortes[] = $cohorte->nombre;
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

            $deudaArancelPorCohorte[$cohorte->nombre] = $deudaArancel;
            $deudaMatriculaPorCohorte[$cohorte->nombre] = $deudaMatricula;
            $deudaInscripcionPorCohorte[$cohorte->nombre] = $deudaInscripcion;
            $recaudadoArancelPorCohorte[$cohorte->nombre] = $recaudadoArancel;
            $recaudadoMatriculaPorCohorte[$cohorte->nombre] = $recaudadoMatricula;
            $recaudadoInscripcionPorCohorte[$cohorte->nombre] = $recaudadoInscripcion;
        }

        return view('dashboard.coordinador', compact(
            'maestria', 'totalAlumnos', 'totalDocentes', 'totalPostulantes',
            'cohortes', 'deudaArancelPorCohorte', 'deudaMatriculaPorCohorte',
            'deudaInscripcionPorCohorte', 'recaudadoArancelPorCohorte', 
            'recaudadoMatriculaPorCohorte', 'recaudadoInscripcionPorCohorte'
        ));
    }
}
