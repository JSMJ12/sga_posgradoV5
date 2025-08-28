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

        // Obtener el docente
        $docente = Docente::where('email', $user->email)->first();
        if (!$docente || $docente->maestria()->count() === 0) {
            return redirect()->route('dashboard_docente')->with('error', 'No estás asignado a ninguna maestría.');
        }

        // Tomar la primera maestría asignada al docente
        $maestria = $docente->maestria->first();

        // Obtener alumnos que pertenecen a esta maestría
        $alumnos = Alumno::whereHas('maestrias', function ($q) use ($maestria) {
                $q->where('maestrias.id', $maestria->id);
            })
            ->with(['matriculas.cohorte', 'montos' => function ($q) use ($maestria) {
                $q->where('maestria_id', $maestria->id);
            }, 'user.pagos'])
            ->get();

        $totalAlumnos = $alumnos->count();
        $totalPostulantes = Postulante::where('maestria_id', $maestria->id)->count();

        // Contar docentes que tengan asignaturas en esta maestría
        $totalDocentes = Docente::whereHas('asignaturas.maestria', function ($q) use ($maestria) {
            $q->where('id', $maestria->id);
        })->count();

        $cohortesMaestria = $maestria->cohortes()->with('matriculas.alumno.user.pagos')->get();

        $cohortes = [];
        $deudaArancelPorCohorte = [];
        $deudaMatriculaPorCohorte = [];
        $deudaInscripcionPorCohorte = [];
        $recaudadoArancelPorCohorte = [];
        $recaudadoMatriculaPorCohorte = [];
        $recaudadoInscripcionPorCohorte = [];

        foreach ($cohortesMaestria as $cohorte) {
            $cohortes[] = [
                'id' => $cohorte->id,
                'nombre' => $cohorte->nombre
            ];

            $alumnosUnicos = $cohorte->matriculas->pluck('alumno')->filter()->unique('dni');
            $cantidadAlumnos = $alumnosUnicos->count();

            $pagosCohorte = collect();
            foreach ($alumnosUnicos as $alumno) {
                // Filtrar montos y pagos de la maestría del docente
                $monto = $alumno->montos->first();
                $pagosVerificados = $alumno->user
                    ? $alumno->user->pagos->where('verificado', '1')->where('maestria_id', $maestria->id)
                    : collect();

                $pagosCohorte = $pagosCohorte->merge($pagosVerificados);
            }

            // Deuda basada en montos específicos de cada alumno
            $deudaArancel = $alumnosUnicos->sum(fn($a) => $a->montos->first()?->monto_arancel ?? $maestria->arancel);
            $deudaMatricula = $alumnosUnicos->sum(fn($a) => $a->montos->first()?->monto_matricula ?? $maestria->matricula);
            $deudaInscripcion = $alumnosUnicos->sum(fn($a) => $a->montos->first()?->monto_inscripcion ?? $maestria->inscripcion);

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
            'maestria',
            'totalAlumnos',
            'totalDocentes',
            'totalPostulantes',
            'cohortes',
            'deudaArancelPorCohorte',
            'deudaMatriculaPorCohorte',
            'deudaInscripcionPorCohorte',
            'recaudadoArancelPorCohorte',
            'recaudadoMatriculaPorCohorte',
            'recaudadoInscripcionPorCohorte'
        ));
    }

}
