<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

use App\Models\Alumno;

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

        $maestriasConCohortes = [];

        foreach ($pagos as $pago) {
            $maestriaNombre = $pago->alumno->maestria->nombre ?? 'Sin Maestría';
            $cohorte = optional($pago->alumno->matriculas->first())->cohorte;

            if (!$cohorte) {
                continue;
            }

            $cohorteNombre = $cohorte->nombre;

            if (!isset($maestriasConCohortes[$maestriaNombre])) {
                $maestriasConCohortes[$maestriaNombre] = [];
            }

            if (!isset($maestriasConCohortes[$maestriaNombre][$cohorteNombre])) {
                $maestriasConCohortes[$maestriaNombre][$cohorteNombre] = [
                    'monto' => 0,
                    'cantidad' => 0,
                ];
            }

            $maestriasConCohortes[$maestriaNombre][$cohorteNombre]['monto'] += $pago->monto;
            $maestriasConCohortes[$maestriaNombre][$cohorteNombre]['cantidad'] += 1;
        }

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
            'maestriasConCohortes' => $maestriasConCohortes,
        ]);
    }
    public function generarPDF(Request $request, $cohorte)
    {
        // Obtener pagos verificados de la cohorte específica con la relación 'alumno'
        $pagos = Pago::where('verificado', true)
            ->whereHas('alumno.matriculas.cohorte', function ($query) use ($cohorte) {
                $query->where('nombre', $cohorte);
            })
            ->with('alumno.maestria')
            ->get();

        if ($pagos->isEmpty()) {
            return back()->with('error', 'No hay pagos registrados para esta cohorte.');
        }

        // Obtener la primera maestría asociada a la cohorte
        $primerAlumno = $pagos->first()->alumno;
        $maestria = optional($primerAlumno->maestria)->nombre ?? 'Desconocida';
        $codigoMaestria = optional($primerAlumno->maestria)->codigo ?? '0000';

        $totalMontoPagado = 0;

        $pagosAgrupados = $pagos->groupBy('alumno.dni')->map(function ($pagosPorAlumno) use (&$totalMontoPagado) {
            $alumno = $pagosPorAlumno->first()->alumno;
            $montoTotal = $alumno->monto_total ?? 0;
            $montoPagado = $pagosPorAlumno->sum('monto');
            $totalMontoPagado += $montoPagado;

            return [
                'alumno' => $alumno,
                'cantidad_pagos' => $pagosPorAlumno->count(),
                'monto_pagado' => $montoPagado,
                'monto_total' => $montoTotal,
                'deuda_pendiente' => max(0, $montoTotal - $montoPagado),
            ];
        });

        $alumnos = Alumno::whereHas('matriculas.cohorte', function ($query) use ($cohorte) {
            $query->where('nombre', $cohorte);
        })->get();

        if ($alumnos->isEmpty()) {
            return back()->with('error', 'No hay alumnos registrados en esta cohorte.');
        }

        $totalDeuda = 0;
        $totalPagado = $pagos->sum('monto');
        $detallesPagos = [];

        foreach ($alumnos as $alumno) {
            $montoTotal = $alumno->monto_total ?? 0;
            $montoPagado = $pagosAgrupados[$alumno->dni]['monto_pagado'] ?? 0;
            $deudaPendiente = max(0, $montoTotal - $montoPagado);

            $totalDeuda += $montoTotal;

            $detallesPagos[] = [
                'nombre' => "{$alumno->nombre1} {$alumno->apellidop}",
                'monto_pagado' => $montoPagado,
                'deuda_pendiente' => $deudaPendiente,
            ];
        }

        $labels = array_column($detallesPagos, 'nombre');
        $pagados = array_column($detallesPagos, 'monto_pagado');
        $deudas = array_column($detallesPagos, 'deuda_pendiente');

        // Definir nombres de imágenes basados en cohorte, maestría y código
        $chartFilename1 = "chart1_{$cohorte}_{$codigoMaestria}_{$maestria}.png";
        $chartPath1 = storage_path("app/public/{$chartFilename1}");

        $chartFilename2 = "chart2_{$cohorte}_{$codigoMaestria}_{$maestria}.png";
        $chartPath2 = storage_path("app/public/{$chartFilename2}");

        // Generar imagen 1
        $chartData1 = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    ['label' => 'Pagado', 'backgroundColor' => 'green', 'data' => $pagados],
                    ['label' => 'Deuda', 'backgroundColor' => 'red', 'data' => $deudas],
                ],
            ],
            'options' => ['responsive' => true, 'scales' => ['y' => ['beginAtZero' => true]]],
        ];

        $charturl1 = "https://quickchart.io/chart?c=" . rawurlencode(json_encode($chartData1));
        file_put_contents($chartPath1, file_get_contents($charturl1));

        // Generar imagen 2
        $chartData2 = [
            'type' => 'bar',
            'data' => [
                'labels' => ['Total Deuda', 'Total Pagado'],
                'datasets' => [
                    [
                        'label' => 'Deuda por Cobrar',
                        'backgroundColor' => 'red',
                        'data' => [$totalDeuda, 0]  // Asignando la deuda a "Total Deuda"
                    ],
                    [
                        'label' => 'Total Pagado',
                        'backgroundColor' => 'green',
                        'data' => [0, $totalMontoPagado]  // Asignando el pago a "Total Pagado"
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'stepSize' => 1000,  // De mil en mil en el eje Y
                            'callback' => function ($value) {
                                return number_format($value);  // Formato de números
                            }
                        ],
                    ],
                ],
            ],
        ];
        $charturl2 = "https://quickchart.io/chart?c=" . rawurlencode(json_encode($chartData2));
        file_put_contents($chartPath2, file_get_contents($charturl2));

        // Generar el PDF con los datos
        $pdf = PDF::loadView('pagos.pagos_cohorte', [
            'pagos' => $pagosAgrupados,
            'cohorte' => $cohorte,
            'maestria' => $maestria,
            'codigoMaestria' => $codigoMaestria,
            'totalDeuda' => $totalDeuda,
            'totalPagado' => $totalPagado,
            'detallesPagos' => $detallesPagos,
            'chartPath1' => asset("storage/{$chartFilename1}"),
            'chartPath2' => asset("storage/{$chartFilename2}"),
        ]);

        // Definir el nombre del archivo PDF
        $nombreArchivo = "pagos_{$cohorte}_{$codigoMaestria}_{$maestria}.pdf";

        return $pdf->stream($nombreArchivo);
    }
}
