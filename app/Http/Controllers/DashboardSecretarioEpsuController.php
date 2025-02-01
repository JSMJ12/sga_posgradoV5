<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use Carbon\Carbon;

class DashboardSecretarioEpsuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $hoy = Carbon::today();
        $mes = Carbon::now()->startOfMonth();
        $anio = Carbon::now()->startOfYear();

        $pagos = Pago::with(['alumno.maestria'])
            ->where('verificado', '1')
            ->orderBy('fecha_pago')
            ->get();

        $pagosPorCohorte = $pagos->groupBy(function ($pago) {
            $cohorte = optional($pago->alumno->matriculas->first())->cohorte;
            return $cohorte ? $cohorte->nombre : 'Sin Cohorte'; 
        });

        $montoPorCohorte = $pagosPorCohorte->map->sum('monto');

        $cantidadPorCohorte = $pagosPorCohorte->map->count();
        
        $pagosPorMaestria = $pagos->groupBy(function ($pago) {
            return $pago->alumno->maestria->nombre ?? 'Sin Maestría';
        });

        $montoPorMaestria = $pagosPorMaestria->map->sum('monto');
        $cantidadPorMaestria = $pagosPorMaestria->map->count();

        $pagosPorDia = $pagos->filter(function ($pago) use ($hoy) {
            return Carbon::parse($pago->fecha_pago)->isToday();
        })->sum('monto');        
        $pagosPorMes = $pagos->where('fecha_pago', '>=', $mes)->sum('monto');
        $pagosPorAnio = $pagos->where('fecha_pago', '>=', $anio)->sum('monto');

        $cantidadPorDia = $pagos->filter(function ($pago) use ($hoy) {
            return \Carbon\Carbon::parse($pago->fecha_pago)->isToday();
        })->count();
        
        $cantidadPorMes = $pagos->where('fecha_pago', '>=', $mes)->count();
        $cantidadPorAnio = $pagos->where('fecha_pago', '>=', $anio)->count();

        $pagosPorVerificar = Pago::where('verificado', false)->count();

        $alumnosPendientes = Pago::where('verificado', false)
            ->with('alumno')
            ->get()
            ->unique('alumno_id');

        if ($request->ajax()) {
            return response()->json([
                'pagosPorDia' => $pagosPorDia,
                'pagosPorMes' => $pagosPorMes,
                'pagosPorAnio' => $pagosPorAnio,
                'montoPorMaestria' => $montoPorMaestria,
                'cantidadPorMaestria' => $cantidadPorMaestria,
                'cantidadPorDia' => $cantidadPorDia,
                'cantidadPorMes' => $cantidadPorMes,
                'cantidadPorAnio' => $cantidadPorAnio,
                'pagosPorVerificar' => $pagosPorVerificar,
                'alumnosPendientes' => $alumnosPendientes,
                'montoPorCohorte' => $montoPorCohorte,
                'cantidadPorCohorte' => $cantidadPorCohorte,
            ]);
        }

        return view('dashboard.secretario_epsu', [
            'pagosPorDia' => $pagosPorDia,
            'pagosPorMes' => $pagosPorMes,
            'pagosPorAnio' => $pagosPorAnio,
            'montoPorMaestria' => $montoPorMaestria,
            'cantidadPorMaestria' => $cantidadPorMaestria,
            'cantidadPorDia' => $cantidadPorDia,
            'cantidadPorMes' => $cantidadPorMes,
            'cantidadPorAnio' => $cantidadPorAnio,
            'pagosPorVerificar' => $pagosPorVerificar,
            'alumnosPendientes' => $alumnosPendientes,
            'montoPorCohorte' => $montoPorCohorte,
            'cantidadPorCohorte' => $cantidadPorCohorte,
        ]);
    }
}
