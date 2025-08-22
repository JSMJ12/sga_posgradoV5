<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use Carbon\Carbon;
use App\Models\Postulante;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Alumno;
use App\Models\Cohorte;
use App\Models\Maestria;
use App\Models\Matricula;
use Illuminate\Support\Facades\Log;

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

    public function generarPDF(Request $request, $cohorteNombre)
{
    try {
        // Buscar cohorte y su maestría
        $cohorte = Cohorte::with('maestria')->where('nombre', $cohorteNombre)->firstOrFail();
        $maestria = $cohorte->maestria;
        $maestriaNombre = strtoupper($maestria->nombre ?? 'DESCONOCIDA');
        $codigoMaestria = $maestria->codigo ?? '0000';

        // Obtener alumnos de la cohorte
        $alumnos = Alumno::with('descuento')
            ->whereIn('dni', function ($query) use ($cohorte) {
                $query->select('alumno_dni')
                    ->from((new Matricula())->getTable())
                    ->where('cohorte_id', $cohorte->id);
            })
            ->get()
            ->map(function ($alumno) {
                $usuario = \App\Models\User::where('email', $alumno->email_institucional)->first();

                // Inicializar montos
                $pagado = ['arancel' => 0, 'matricula' => 0, 'inscripcion' => 0];
                $adeudado = ['arancel' => 0, 'matricula' => 0, 'inscripcion' => 0];

                if ($usuario) {
                    $pagado['arancel'] = \App\Models\Pago::where('user_id', $usuario->id)
                        ->where('tipo_pago', 'arancel')
                        ->where('verificado', 1)
                        ->sum('monto');

                    $pagado['matricula'] = \App\Models\Pago::where('user_id', $usuario->id)
                        ->where('tipo_pago', 'matricula')
                        ->where('verificado', 1)
                        ->sum('monto');

                    $pagado['inscripcion'] = \App\Models\Pago::where('user_id', $usuario->id)
                        ->where('tipo_pago', 'inscripcion')
                        ->where('verificado', 1)
                        ->sum('monto');
                }

                // Deuda pendiente
                $adeudado['arancel'] = max(0, $alumno->monto_total - $pagado['arancel']);
                $adeudado['matricula'] = max(0, $alumno->monto_matricula - $pagado['matricula']);
                $adeudado['inscripcion'] = max(0, $alumno->monto_inscripcion - $pagado['inscripcion']);

                return [
                    'alumno' => $alumno,
                    'usuario' => $usuario,
                    'descuento' => $alumno->descuento,
                    'pagado' => $pagado,
                    'adeudado' => $adeudado,
                ];
            })
            ->sortBy(function ($item) {
                $a = $item['alumno'];
                return strtolower($a->apellidop . ' ' . $a->apellidom . ' ' . $a->nombre1 . ' ' . $a->nombre2);
            })
            ->values();

        // Totales
        $totalRecaudado = [
            'arancel' => $alumnos->sum(fn($a) => $a['pagado']['arancel']),
            'matricula' => $alumnos->sum(fn($a) => $a['pagado']['matricula']),
            'inscripcion' => $alumnos->sum(fn($a) => $a['pagado']['inscripcion']),
        ];

        $totalDeuda = [
            'arancel' => $alumnos->sum(fn($a) => $a['adeudado']['arancel']),
            'matricula' => $alumnos->sum(fn($a) => $a['adeudado']['matricula']),
            'inscripcion' => $alumnos->sum(fn($a) => $a['adeudado']['inscripcion']),
        ];

        // Generar PDF
        $pdf = Pdf::loadView('pagos.reporte_epsu.reporte', [
            'alumnos' => $alumnos,
            'cohorte' => $cohorte,
            'maestria_nombre' => $maestriaNombre,
            'codigoMaestria' => $codigoMaestria,
            'total_recaudado' => $totalRecaudado,
            'total_deuda' => $totalDeuda,
        ])->setPaper('A4', 'landscape');

        $nombreArchivo = "Reporte_Pagos_Cohorte_{$cohorteNombre}_{$codigoMaestria}.pdf";
        return $pdf->stream($nombreArchivo);

    } catch (\Exception $e) {
        Log::error('Error al generar PDF', [
            'cohorte' => $cohorteNombre,
            'mensaje' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return back()->with('error', 'Ocurrió un error al generar el reporte. Revisa el log.');
    }
}


    public function generador_reporte()
    {
        $maestrias = Maestria::with('cohortes')->get();

        return view('pagos.reporte_epsu.generador', compact('maestrias'));
    }

    public function generar_reporte_pdf(Request $request)
    {
        $request->validate([
            'cohorte_id' => 'required|exists:cohortes,id',
        ]);

        $cohorte_id = $request->input('cohorte_id');

        // Buscar cohorte y maestría
        $cohorte = Cohorte::with('maestria')->findOrFail($cohorte_id);
        $maestria = $cohorte->maestria;
        $maestria_nombre = strtoupper($maestria->nombre);
        $codigoMaestria = $maestria->codigo;

        // Obtener alumnos de la cohorte
        $alumnos = Alumno::with('descuento')
            ->whereIn('dni', function ($query) use ($cohorte_id) {
                $query->select('alumno_dni')
                    ->from((new Matricula())->getTable())
                    ->where('cohorte_id', $cohorte_id);
            })
            ->get()
            ->map(function ($alumno) {
                $usuario = \App\Models\User::where('email', $alumno->email_institucional)->first();

                // Inicializar montos
                $pagado = ['arancel' => 0, 'matricula' => 0, 'inscripcion' => 0];
                $adeudado = ['arancel' => 0, 'matricula' => 0, 'inscripcion' => 0];

                if ($usuario) {
                    $pagado['arancel'] = \App\Models\Pago::where('user_id', $usuario->id)
                        ->where('tipo_pago', 'arancel')
                        ->where('verificado', 1)
                        ->sum('monto');

                    $pagado['matricula'] = \App\Models\Pago::where('user_id', $usuario->id)
                        ->where('tipo_pago', 'matricula')
                        ->where('verificado', 1)
                        ->sum('monto');

                    $pagado['inscripcion'] = \App\Models\Pago::where('user_id', $usuario->id)
                        ->where('tipo_pago', 'inscripcion')
                        ->where('verificado', 1)
                        ->sum('monto');
                }

                // Monto de arancel viene directamente de monto_total (con descuento aplicado)
                $adeudado['arancel'] = $alumno->monto_total - $pagado['arancel'];
                $adeudado['matricula'] = $alumno->monto_matricula - $pagado['matricula'];
                $adeudado['inscripcion'] = $alumno->monto_inscripcion - $pagado['inscripcion'];

                return [
                    'alumno' => $alumno,
                    'usuario' => $usuario,
                    'descuento' => $alumno->descuento,
                    'pagado' => $pagado,
                    'adeudado' => $adeudado,
                ];
            })
            ->sortBy(function ($item) {
                $a = $item['alumno'];
                return strtolower($a->apellidop . ' ' . $a->apellidom . ' ' . $a->nombre1 . ' ' . $a->nombre2);
            })
            ->values();

        // Calcular totales
        $total_recaudado = [
            'arancel' => $alumnos->sum(fn($a) => $a['pagado']['arancel']),
            'matricula' => $alumnos->sum(fn($a) => $a['pagado']['matricula']),
            'inscripcion' => $alumnos->sum(fn($a) => $a['pagado']['inscripcion']),
        ];
        $total_deuda = [
            'arancel' => $alumnos->sum(fn($a) => $a['adeudado']['arancel']),
            'matricula' => $alumnos->sum(fn($a) => $a['adeudado']['matricula']),
            'inscripcion' => $alumnos->sum(fn($a) => $a['adeudado']['inscripcion']),
        ];

        $pdf = Pdf::loadView('pagos.reporte_epsu.reporte', [
            'alumnos' => $alumnos,
            'cohorte' => $cohorte,
            'maestria_nombre' => $maestria_nombre,
            'codigoMaestria' => $codigoMaestria,
            'total_recaudado' => $total_recaudado,
            'total_deuda' => $total_deuda,
        ])->setPaper('A4', 'landscape');

        return $pdf->stream("Reporte_EPSU_Cohorte_{$cohorte_id}.pdf");
    }
}
