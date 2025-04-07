<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Maestria;
use App\Models\Docente;
use App\Models\Postulante;
use App\Models\Pago;

class CoordinadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        // Obtener al usuario autenticado y su maestría
        $user = auth()->user();
        $docente = Docente::where('email', $user->email)->first();

        if (!$docente || $docente->maestria()->count() === 0) {
            return redirect()->route('dashboard')->with('error', 'No estás asignado a ninguna maestría.');
        }

        $maestria = $docente->maestria->first();

        // Obtener los alumnos, postulantes y otras estadísticas
        $alumnos = Alumno::where('maestria_id', $maestria->id)->get();
        $cantidadPostulantes = Postulante::where('maestria_id', $maestria->id)->count();
        $matriculadosPorMaestria = Maestria::withCount('alumnos')->get();
        $asignaturas = $maestria->asignaturas;

        $totalAlumnos = $alumnos->count();
        $totalPostulantes = $cantidadPostulantes;
        $totalDocentes = Docente::whereHas('asignaturas', function ($query) use ($maestria) {
            $query->whereHas('maestria', function ($query) use ($maestria) {
                $query->where('id', $maestria->id);
            });
        })->count();

        // Obtener los pagos de los alumnos
        $pagos = Pago::with(['alumno.maestria', 'alumno.matriculas.cohorte'])
            ->where('verificado', '1')
            ->whereHas('alumno.maestria', function ($query) use ($maestria) {
                $query->where('id', $maestria->id);
            })
            ->orderBy('fecha_pago')
            ->get();

        // Agrupar los pagos por cohorte
        $pagosPorCohorte = $pagos->groupBy(function ($pago) {
            $cohorte = optional($pago->alumno->matriculas->first())->cohorte;
            return $cohorte ? $cohorte->nombre : 'Sin Cohorte';
        });

        // Calcular el monto total pendiente por cohorte
        $montoPendientePorCohorte = $alumnos->groupBy(function ($alumno) {
            $cohorte = optional($alumno->matriculas->first())->cohorte;
            return $cohorte ? $cohorte->nombre : 'Sin Cohorte';
        });
        // Sumar los montos pendientes (deuda) por cohorte
        $montoPendientePorCohorte = $montoPendientePorCohorte->map(function ($alumnosPorCohorte) {
            return $alumnosPorCohorte->sum(function ($alumno) {
                return $alumno->monto_total; 
            });
        });


        // Calcular el monto total y la cantidad de pagos por cohorte
        $montoPorCohorte = $pagosPorCohorte->map->sum('monto');
        $cantidadPorCohorte = $pagosPorCohorte->map->count();

        // Preparar los datos para el gráfico
        $cohortes = $montoPendientePorCohorte->keys();
        $monto = $montoPendientePorCohorte->values(); // Usar el monto pendiente para el gráfico
        $cantidad = $cantidadPorCohorte->values();

        // Retornar los datos a la vista
        return view('dashboard.coordinador', compact(
            'alumnos',
            'matriculadosPorMaestria',
            'totalAlumnos',
            'totalPostulantes',
            'maestria',
            'totalDocentes',
            'asignaturas',
            'cohortes',
            'monto',
            'cantidad',
            'montoPendientePorCohorte'
        ));
    }

}
