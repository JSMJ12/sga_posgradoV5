<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Descuento;
use Illuminate\Http\Request;

class DescuentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $descuentos = Descuento::all();

        return view('descuentos.index', compact('descuentos'));
    }
    public function alumnos(Request $request)
    {
        if ($request->ajax()) {
            $query = Alumno::with(['maestrias', 'descuentos', 'matriculas'])
                ->orderBy('apellidop')  
                ->orderBy('apellidom')   
                ->orderBy('nombre1');    

            return datatables()->eloquent($query)
                ->addColumn('maestria_nombre', function ($alumno) {
                    return $alumno->maestrias->pluck('nombre')->join(', ') ?: 'Sin Maestría';
                })
                ->addColumn('foto', function ($alumno) {
                    return '<img src="' . asset('storage/' . $alumno->image) . '" alt="Foto de ' . $alumno->nombre1 . '" 
                        class="img-thumbnail rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">';
                })
                ->addColumn('nombre_completo', function ($alumno) {
                    return "{$alumno->nombre1} {$alumno->nombre2} {$alumno->apellidop} {$alumno->apellidom}";
                })
                ->addColumn('descuento_nombre', function ($alumno) {
                    return $alumno->descuentos->pluck('nombre')->join(', ') ?: 'Sin descuento';
                })
                ->addColumn('acciones', function ($alumno) {
                    $acciones = '';

                    // IDs de maestrías con descuento
                    $maestriasConDescuento = $alumno->descuentos->map(fn($d) => $d->pivot->maestria_id)->toArray();

                    // Maestrías pendientes
                    $maestriasPendientes = $alumno->maestrias
                        ->filter(fn($m) => !in_array($m->id, $maestriasConDescuento))
                        ->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre]);

                    if ($maestriasPendientes->isNotEmpty()) {
                        $acciones .= '<button class="btn btn-primary btn-sm select-descuento" 
                                        data-dni="' . $alumno->dni . '" 
                                        data-maestrias=\'' . json_encode($maestriasPendientes) . '\' 
                                        data-toggle="modal">
                                        <i class="fas fa-tags"></i> Descuento
                                    </button>';
                    } else {
                        $acciones .= '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Todos los descuentos aplicados</span>';
                    }

                    // Botón de reportes
                    if ($alumno->matriculas && $alumno->matriculas->count() > 0) {
                        $maestriasAlumno = $alumno->maestrias->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre]);
                        $acciones .= ' <button type="button" class="btn btn-outline-warning btn-sm open-reportes" 
                                        data-dni="' . $alumno->dni . '" 
                                        data-nombre="' . $alumno->nombre1 . ' ' . $alumno->apellidop . '" 
                                        data-maestrias=\'' . json_encode($maestriasAlumno) . '\' 
                                        title="Ver Reportes">
                                        <i class="fas fa-file-alt"></i>
                                    </button>';
                    }

                    return $acciones;
                })
                ->rawColumns(['foto', 'acciones', 'nombre_completo'])
                ->toJson();
        }

        return view('descuentos.alumnos');
    }

    
    public function showDescuentoForm($dni)
    {
        $alumno = Alumno::with(['maestrias', 'descuentos'])->where('dni', $dni)->first();

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado.'], 404);
        }

        // IDs de maestrías con descuento
        $maestriasConDescuento = $alumno->descuentos->map(fn($d) => $d->pivot->maestria_id)->toArray();

        // Maestrías pendientes (puede estar vacío)
        $maestriasPendientes = $alumno->maestrias
            ->filter(fn($m) => !in_array($m->id, $maestriasConDescuento))
            ->map(fn($m) => [
                'id' => $m->id,
                'nombre' => $m->nombre,
                'arancel' => $m->arancel ?? 0,
            ])
            ->values(); // para resetear índices

        // Descuentos activos
        $descuentos = Descuento::where('activo', true)->get()->mapWithKeys(fn($d) => [
            strtolower($d->nombre) => [
                'id' => $d->id,
                'nombre' => $d->nombre,
                'porcentaje' => $d->porcentaje,
                'requisitos' => $d->requisitos ? json_decode($d->requisitos) : [],
            ]
        ]);

        return response()->json([
            'alumno' => $alumno,
            'maestrias' => $maestriasPendientes, // puede estar vacío
            'descuentos' => $descuentos
        ]);
    }



    public function processDescuento(Request $request)
    {
        $request->validate([
            'dni' => 'required|string',
            'maestria_id' => 'required|exists:maestrias,id',
            'descuento_id' => 'required|exists:descuentos,id',
            'documento' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);

        try {
            $alumno = Alumno::with(['montos', 'descuentos'])->where('dni', $request->dni)->first();
            if (!$alumno) {
                return redirect()->back()->with('error', 'Alumno no encontrado.');
            }

            $descuento = Descuento::find($request->descuento_id);
            if (!$descuento || !$descuento->activo) {
                return redirect()->back()->with('error', 'Descuento inválido.');
            }

            // Obtener el monto del arancel actual del alumno para la maestría seleccionada
            $montoRelacion = $alumno->montos()->where('maestria_id', $request->maestria_id)->first();
            if (!$montoRelacion) {
                return redirect()->back()->with('error', 'El alumno no tiene registrado el monto de arancel para esta maestría.');
            }

            $arancelActual = $montoRelacion->pivot->monto_arancel ?? 0;

            // Total pagado antes de aplicar el descuento
            $usuario = \App\Models\User::where('email', $alumno->email_institucional)->first();
            $totalPagado = 0;
            if ($usuario) {
                $totalPagado = \App\Models\Pago::where('user_id', $usuario->id)
                    ->where('verificado', 1)
                    ->where('tipo_pago', 'arancel')
                    ->where('maestria_id', $request->maestria_id)
                    ->sum('monto');
            }

            // Calcular monto con descuento
            $montoConDescuento = $arancelActual - ($arancelActual * ($descuento->porcentaje / 100));

            // Guardar el descuento aplicado en la tabla pivote
            $alumno->descuentos()->syncWithoutDetaching([
                $descuento->id => ['maestria_id' => $request->maestria_id]
            ]);

            // Actualizar monto_arancel en la tabla pivote alumno_maestria_monto
            $alumno->montos()->updateExistingPivot($request->maestria_id, [
                'monto_arancel' => $montoConDescuento
            ]);

            // Guardar documento si aplica
            if ($request->hasFile('documento')) {
                $documentoPath = $request->file('documento')->store('documentos_autenticidad', 'public');
                $alumno->documento = $documentoPath;
                $alumno->save();
            }

            // Calcular deuda o reembolso
            if ($totalPagado < $montoConDescuento) {
                $deudaRestante = $montoConDescuento - $totalPagado;
                $mensaje_pago = 'Total pagado: $' . number_format($totalPagado, 2) .
                                ' | Monto con descuento: $' . number_format($montoConDescuento, 2) .
                                ' | Deuda restante: $' . number_format($deudaRestante, 2);
            } else {
                $reembolso = $totalPagado - $montoConDescuento;
                $mensaje_pago = 'Total pagado: $' . number_format($totalPagado, 2) .
                                ' | Monto con descuento: $' . number_format($montoConDescuento, 2) .
                                ' | Reembolso pendiente: $' . number_format($reembolso, 2);
            }

            return redirect()->route('descuentos.alumnos')->with('success', 'Descuento aplicado correctamente. ' . $mensaje_pago);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al aplicar el descuento: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'porcentaje' => 'required|integer|min:0|max:100',
            'activo' => 'required|boolean',
            'requisitos' => 'nullable|string',
            'comprobante' => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        if (!empty($data['requisitos'])) {
            $decoded = json_decode($data['requisitos'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // No es JSON: lo procesamos por saltos de línea
                $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $data['requisitos'])));
                $data['requisitos'] = json_encode($lines);
            } else {
                // Ya es un JSON válido
                $data['requisitos'] = json_encode($decoded);
            }
        } else {
            $data['requisitos'] = null;
        }

        Descuento::create($data);

        return redirect()->route('descuentos.index')->with('success', '¡Descuento creado exitosamente!');
    }

    public function update(Request $request, Descuento $descuento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'porcentaje' => 'required|integer|min:0|max:100',
            'activo' => 'required|boolean',
            'requisitos' => 'nullable|string',
            'comprobante' => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        if (!empty($data['requisitos'])) {
            $decoded = json_decode($data['requisitos'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $data['requisitos'])));
                $data['requisitos'] = json_encode($lines);
            } else {
                $data['requisitos'] = json_encode($decoded);
            }
        } else {
            $data['requisitos'] = null;
        }

        $descuento->update($data);

        return redirect()->route('descuentos.index')->with('success', '¡Descuento actualizado!');
    }


    public function destroy(Descuento $descuento)
    {
        $descuento->delete();

        return redirect()->route('descuentos.index')->with('success', '¡Descuento eliminado!');
    }
}
