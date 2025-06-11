<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use Carbon\Carbon;
use App\Models\Postulante;
use Barryvdh\DomPDF\Facade\Pdf;
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

        $pagos = Pago::where('verificado', '1')
            ->with('user') // cargamos el user directamente
            ->orderBy('fecha_pago')
            ->get();

        foreach ($pagos as $pago) {
            // Cargar manualmente el alumno y postulante a partir del email del user
            $email = $pago->user->email;

            $alumno = Alumno::with(['maestria', 'matriculas.cohorte'])
                ->where('email_institucional', $email)
                ->first();

            $postulante = Postulante::with('maestria')
                ->where('correo_electronico', $email)
                ->first();

            // Guardar en el pago para acceder fácilmente
            $pago->alumno_data = $alumno;
            $pago->postulante_data = $postulante;
        }

        // Agrupar por cohorte
        $pagosPorCohorte = $pagos->groupBy(function ($pago) {
            $cohorte = optional($pago->alumno_data?->matriculas->first())->cohorte;
            return $cohorte ? $cohorte->nombre : 'Sin Cohorte';
        });

        $montoPorCohorte = $pagosPorCohorte->map->sum('monto');
        $cantidadPorCohorte = $pagosPorCohorte->map->count();

        // Agrupar por maestría
        $pagosPorMaestria = $pagos->groupBy(function ($pago) {
            return $pago->alumno_data?->maestria?->nombre ??
                $pago->postulante_data?->maestria?->nombre ??
                'Sin Maestría';
        });

        $montoPorMaestria = $pagosPorMaestria->map->sum('monto');
        $cantidadPorMaestria = $pagosPorMaestria->map->count();

        // Estadísticas por fecha
        $pagosPorDia = $pagos->filter(fn($pago) => Carbon::parse($pago->fecha_pago)->isToday())->sum('monto');
        $pagosPorMes = $pagos->where('fecha_pago', '>=', $mes)->sum('monto');
        $pagosPorAnio = $pagos->where('fecha_pago', '>=', $anio)->sum('monto');

        $cantidadPorDia = $pagos->filter(fn($pago) => Carbon::parse($pago->fecha_pago)->isToday())->count();
        $cantidadPorMes = $pagos->where('fecha_pago', '>=', $mes)->count();
        $cantidadPorAnio = $pagos->where('fecha_pago', '>=', $anio)->count();

        // Pagos por verificar y alumnos pendientes
        $pagosPorVerificar = Pago::where('verificado', false)->count();

        $pagosPendientes = Pago::where('verificado', false)
            ->with('user') // Ahora solo usuario
            ->get();

        $alumnosPendientes = collect();
        foreach ($pagosPendientes as $pendiente) {
            $email = $pendiente->user->email;
            $alumno = Alumno::where('email_institucional', $email)->first();
            if ($alumno) {
                $alumnosPendientes->push($alumno);
            }
        }
        $alumnosPendientes = $alumnosPendientes->unique('id');

        // Estadísticas por Maestría + Cohorte
        $maestriasConCohortes = [];

        foreach ($pagos as $pago) {
            $alumno = $pago->alumno_data;
            if (!$alumno) continue;

            $maestriaNombre = $alumno->maestria->nombre ?? 'Sin Maestría';
            $cohorte = optional($alumno->matriculas->first())->cohorte;
            if (!$cohorte) continue;

            $cohorteNombre = $cohorte->nombre;

            $maestriasConCohortes[$maestriaNombre][$cohorteNombre] ??= ['monto' => 0, 'cantidad' => 0];
            $maestriasConCohortes[$maestriaNombre][$cohorteNombre]['monto'] += $pago->monto;
            $maestriasConCohortes[$maestriaNombre][$cohorteNombre]['cantidad'] += 1;
        }
        // Retorno
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
        // Obtener pagos verificados
        $pagos = Pago::with('user')->where('verificado', true)->get();

        // Crear una colección de pagos que sí pertenecen a la cohorte
        $pagosFiltrados = collect();

        foreach ($pagos as $pago) {
            $alumno = Alumno::with(['maestria', 'matriculas.cohorte'])
                ->where('email_institucional', $pago->user->email)
                ->first();

            // Verificar si el alumno está en la cohorte indicada
            if ($alumno && $alumno->matriculas->contains(function ($matricula) use ($cohorte) {
                return optional($matricula->cohorte)->nombre === $cohorte;
            })) {
                // Agregamos los datos temporales
                $pago->alumno_data = $alumno;
                $pagosFiltrados->push($pago);
            }
        }

        if ($pagosFiltrados->isEmpty()) {
            return back()->with('error', 'No hay pagos registrados para esta cohorte.');
        }

        // Obtener la primera maestría
        $primerAlumno = $pagosFiltrados->first()->alumno_data;
        $maestria = optional($primerAlumno->maestria)->nombre ?? 'Desconocida';
        $codigoMaestria = optional($primerAlumno->maestria)->codigo ?? '0000';

        $totalMontoPagado = 0;

        // Agrupar pagos por alumno
        $pagosAgrupados = $pagosFiltrados->groupBy(function ($p) {
            return $p->alumno_data->dni ?? 'sin_dni';
        })->map(function ($pagosPorAlumno) use (&$totalMontoPagado) {
            $alumno = $pagosPorAlumno->first()->alumno_data;
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

        // Obtener todos los alumnos de la cohorte
        $alumnos = Alumno::whereHas('matriculas.cohorte', function ($query) use ($cohorte) {
            $query->where('nombre', $cohorte);
        })->get();

        if ($alumnos->isEmpty()) {
            return back()->with('error', 'No hay alumnos registrados en esta cohorte.');
        }

        $totalDeuda = 0;
        $totalPagado = $pagosFiltrados->sum('monto');
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
            'options' => [
                'responsive' => true,
                'scales' => ['y' => ['beginAtZero' => true]],
                'plugins' => [
                    'datalabels' => [
                        'anchor' => 'end',
                        'align' => 'top',
                        'color' => 'black',
                        'font' => ['weight' => 'bold'],
                        'formatter' => 'Math.round',
                    ]
                ]
            ]
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
                        'data' => [$totalDeuda, 0]
                    ],
                    [
                        'label' => 'Total Pagado',
                        'backgroundColor' => 'green',
                        'data' => [0, $totalMontoPagado]
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'stepSize' => 1000
                        ],
                    ],
                ],
                'plugins' => [
                    'datalabels' => [
                        'anchor' => 'end',
                        'align' => 'top',
                        'color' => 'black',
                        'font' => ['weight' => 'bold'],
                        'formatter' => 'Math.round',
                    ]
                ]
            ]
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