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

        // === Cargar pagos verificados con usuario y maestría ===
        $pagos = Pago::where('verificado', 1)
            ->with(['user', 'maestria'])
            ->orderBy('fecha_pago')
            ->get();

        foreach ($pagos as $pago) {
            $email = $pago->user->email;

            // Relacionar con alumno y sus cohortes
            $alumno = Alumno::with(['maestrias', 'matriculas.cohorte.maestria'])
                ->where('email_institucional', $email)
                ->first();

            // Relacionar con postulante
            $postulante = Postulante::with('maestria')
                ->where('correo_electronico', $email)
                ->first();

            // Guardar referencias en el pago
            $pago->alumno_data = $alumno;
            $pago->postulante_data = $postulante;

            // Determinar maestría efectiva del pago
            $pago->maestria_nombre = $pago->maestria?->nombre
                ?? $postulante?->maestria?->nombre
                ?? 'Sin Maestría';

            // Determinar cohorte asociada a la maestría del pago
            $pago->cohorte_nombre = 'Sin Cohorte';
            $pago->cohorte_id = null; // inicializamos el ID
            if ($alumno && $pago->maestria_nombre !== 'Sin Maestría') {
                $matriculaRelacionada = $alumno->matriculas
                    ->first(fn($m) => $m->cohorte && $m->cohorte->maestria
                        && $m->cohorte->maestria->nombre === $pago->maestria_nombre);

                if ($matriculaRelacionada) {
                    $pago->cohorte_nombre = $matriculaRelacionada->cohorte->nombre;
                    $pago->cohorte_id = $matriculaRelacionada->cohorte->id; // <-- ID agregado
                }
            }
        }

        // === Agrupaciones ===

        // Agrupar por cohorte
        $pagosPorCohorte = $pagos->groupBy('cohorte_nombre');
        $montoPorCohorte = $pagosPorCohorte->map->sum('monto');
        $cantidadPorCohorte = $pagosPorCohorte->map->count();

        // Agrupar por maestría
        $pagosPorMaestria = $pagos->groupBy('maestria_nombre');
        $montoPorMaestria = $pagosPorMaestria->map->sum('monto');
        $cantidadPorMaestria = $pagosPorMaestria->map->count();

        // === Estadísticas temporales ===
        $pagosPorDia = $pagos->filter(fn($p) => Carbon::parse($p->fecha_pago)->isToday())->sum('monto');
        $pagosPorMes = $pagos->where('fecha_pago', '>=', $mes)->sum('monto');
        $pagosPorAnio = $pagos->where('fecha_pago', '>=', $anio)->sum('monto');

        $cantidadPorDia = $pagos->filter(fn($p) => Carbon::parse($p->fecha_pago)->isToday())->count();
        $cantidadPorMes = $pagos->where('fecha_pago', '>=', $mes)->count();
        $cantidadPorAnio = $pagos->where('fecha_pago', '>=', $anio)->count();

        // === Pendientes de verificación ===
        $pagosPorVerificar = Pago::where('verificado', false)->count();

        $pagosPendientes = Pago::where('verificado', false)
            ->with('user')
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

        // === Estadísticas combinadas (Maestría + Cohorte) ===
        $maestriasConCohortes = [];

        foreach ($pagos as $pago) {
            $maestriaNombre = $pago->maestria_nombre;
            $cohorteNombre = $pago->cohorte_nombre;

            if ($maestriaNombre === 'Sin Maestría' || $cohorteNombre === 'Sin Cohorte') {
                continue;
            }

            $maestriasConCohortes[$maestriaNombre][$cohorteNombre] ??= [
                'id' => $pago->cohorte_id,  
                'monto' => 0,
                'cantidad' => 0
            ];

            $maestriasConCohortes[$maestriaNombre][$cohorteNombre]['monto'] += $pago->monto;
            $maestriasConCohortes[$maestriaNombre][$cohorteNombre]['cantidad'] += 1;
        }

        // === Retorno ===
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

    public function generarPDF(Request $request, $cohorte_id)
    {
        // Obtener cohorte y maestría
        $cohorte = Cohorte::with('maestria')->findOrFail($cohorte_id);
        $maestria = $cohorte->maestria;
        $maestria_nombre = strtoupper($maestria->nombre);
        $codigoMaestria = $maestria->codigo;

        // Obtener alumnos de la cohorte con descuentos y montos
        $alumnos = Alumno::with(['descuentos', 'montos'])
            ->whereHas('matriculas', function ($query) use ($cohorte_id) {
                $query->where('cohorte_id', $cohorte_id);
            })
            ->get();

        // Cargar usuarios y pagos de una vez para evitar N+1 queries
        $emails = $alumnos->pluck('email_institucional')->filter()->unique();
        $usuarios = \App\Models\User::whereIn('email', $emails)
            ->with(['pagos' => fn($q) => $q->where('verificado', 1)->where('maestria_id', $maestria->id)])
            ->get()
            ->keyBy('email');

        $alumnos = $alumnos->map(function ($alumno) use ($maestria, $usuarios) {
            $usuario = $usuarios->get($alumno->email_institucional);

            // Obtener montos de la pivot para esta maestría
            $monto = $alumno->montos->where('id', $maestria->id)->first();
            $arancelAlumno = $monto?->pivot->monto_arancel ?? 0;
            $montoMatricula = $monto?->pivot->monto_matricula ?? 0;
            $montoInscripcion = $monto?->pivot->monto_inscripcion ?? 0;

            // Filtrar descuento correspondiente a esta maestría
            $descuento_maestria = $alumno->descuentos
                ->filter(fn($d) => $d->pivot->maestria_id == $maestria->id)
                ->first();

            $descuento_nombre = $descuento_maestria?->nombre ?? '-';
            $descuento_porcentaje = $descuento_maestria?->porcentaje ?? 0;

            // Ajustar arancel con descuento
            $arancelConDescuento = $arancelAlumno - ($arancelAlumno * ($descuento_porcentaje / 100));

            // Inicializar pagado y adeudado
            $pagado = ['arancel' => 0, 'matricula' => 0, 'inscripcion' => 0];
            $adeudado = [
                'arancel' => $arancelConDescuento,
                'matricula' => $montoMatricula,
                'inscripcion' => $montoInscripcion
            ];

            if ($usuario) {
                $pagos = $usuario->pagos->groupBy('tipo_pago');

                $pagado['arancel'] = $pagos->get('arancel')?->sum('monto') ?? 0;
                $pagado['matricula'] = $pagos->get('matricula')?->sum('monto') ?? 0;
                $pagado['inscripcion'] = $pagos->get('inscripcion')?->sum('monto') ?? 0;

                // Actualizar adeudado restando lo pagado
                $adeudado['arancel'];
                $adeudado['matricula'];
                $adeudado['inscripcion'];
            }

            return [
                'alumno' => $alumno,
                'usuario' => $usuario,
                'descuento_nombre' => $descuento_nombre,
                'descuento_porcentaje' => $descuento_porcentaje,
                'pagado' => $pagado,
                'adeudado' => $adeudado,
                'arancel_con_descuento' => $arancelConDescuento,
            ];
        })
        ->sortBy(fn($item) => strtolower("{$item['alumno']->apellidop} {$item['alumno']->apellidom} {$item['alumno']->nombre1} {$item['alumno']->nombre2}"))
        ->values();

        // Totales
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

        // Obtener cohorte y maestría
        $cohorte = Cohorte::with('maestria')->findOrFail($cohorte_id);
        $maestria = $cohorte->maestria;
        $maestria_nombre = strtoupper($maestria->nombre);
        $codigoMaestria = $maestria->codigo;

        // Obtener alumnos de la cohorte con descuentos y montos
        $alumnos = Alumno::with(['descuentos', 'montos'])
            ->whereHas('matriculas', function ($query) use ($cohorte_id) {
                $query->where('cohorte_id', $cohorte_id);
            })
            ->get();

        // Cargar usuarios y pagos de una vez para evitar N+1 queries
        $emails = $alumnos->pluck('email_institucional')->filter()->unique();
        $usuarios = \App\Models\User::whereIn('email', $emails)
            ->with(['pagos' => fn($q) => $q->where('verificado', 1)->where('maestria_id', $maestria->id)])
            ->get()
            ->keyBy('email');

        $alumnos = $alumnos->map(function ($alumno) use ($maestria, $usuarios) {
            $usuario = $usuarios->get($alumno->email_institucional);

            // Obtener montos de la pivot para esta maestría
            $monto = $alumno->montos->where('id', $maestria->id)->first();
            $arancelAlumno = $monto?->pivot->monto_arancel ?? 0;
            $montoMatricula = $monto?->pivot->monto_matricula ?? 0;
            $montoInscripcion = $monto?->pivot->monto_inscripcion ?? 0;

            // Filtrar descuento correspondiente a esta maestría
            $descuento_maestria = $alumno->descuentos
                ->filter(fn($d) => $d->pivot->maestria_id == $maestria->id)
                ->first();

            $descuento_nombre = $descuento_maestria?->nombre ?? '-';
            $descuento_porcentaje = $descuento_maestria?->porcentaje ?? 0;

            // Ajustar arancel con descuento
            $arancelConDescuento = $arancelAlumno - ($arancelAlumno * ($descuento_porcentaje / 100));

            // Inicializar pagado y adeudado
            $pagado = ['arancel' => 0, 'matricula' => 0, 'inscripcion' => 0];
            $adeudado = [
                'arancel' => $arancelConDescuento,
                'matricula' => $montoMatricula,
                'inscripcion' => $montoInscripcion
            ];

            if ($usuario) {
                $pagos = $usuario->pagos->groupBy('tipo_pago');

                $pagado['arancel'] = $pagos->get('arancel')?->sum('monto') ?? 0;
                $pagado['matricula'] = $pagos->get('matricula')?->sum('monto') ?? 0;
                $pagado['inscripcion'] = $pagos->get('inscripcion')?->sum('monto') ?? 0;

                // Actualizar adeudado restando lo pagado
                $adeudado['arancel'];
                $adeudado['matricula'];
                $adeudado['inscripcion'];
            }

            return [
                'alumno' => $alumno,
                'usuario' => $usuario,
                'descuento_nombre' => $descuento_nombre,
                'descuento_porcentaje' => $descuento_porcentaje,
                'pagado' => $pagado,
                'adeudado' => $adeudado,
                'arancel_con_descuento' => $arancelConDescuento,
            ];
        })
        ->sortBy(fn($item) => strtolower("{$item['alumno']->apellidop} {$item['alumno']->apellidom} {$item['alumno']->nombre1} {$item['alumno']->nombre2}"))
        ->values();

        // Totales
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
