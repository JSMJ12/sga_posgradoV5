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
        $descuentos = Descuento::all(); // O puedes usar paginación si quieres

        return view('descuentos.index', compact('descuentos'));
    }
    public function alumnos(Request $request)
    {
        if ($request->ajax()) {
            $query = Alumno::with('maestria', 'descuento');

            return datatables()->eloquent($query)
                ->addColumn('maestria_nombre', function ($alumno) {
                    return $alumno->maestria ? $alumno->maestria->nombre : 'Sin Maestría';
                })
                ->addColumn('foto', function ($alumno) {
                    return '<img src="' . asset('storage/' . $alumno->image) . '" alt="Foto de ' . $alumno->nombre1 . '" 
                        class="img-thumbnail rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">';
                })
                ->addColumn('nombre_completo', function ($alumno) {
                    return "{$alumno->nombre1} {$alumno->nombre2} {$alumno->apellidop} {$alumno->apellidom}";
                })
                ->addColumn('acciones', function ($alumno) {
                    if ($alumno->descuento_id !== null) {
                        return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Descuento aplicado</span>';
                    }
                    return '<button class="btn btn-primary btn-sm select-descuento" data-dni="' . $alumno->dni . '" data-toggle="modal">
                                <i class="fas fa-tags"></i> Descuento
                            </button>';
                })
                ->rawColumns(['foto', 'acciones', 'nombre_completo'])
                ->toJson();
        }

        return view('descuentos.alumnos');
    }

    public function showDescuentoForm($dni)
    {
        $alumno = Alumno::where('dni', $dni)->first();

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado.'], 404);
        }

        $maestria = $alumno->maestria;

        if (!$maestria) {
            return response()->json(['error' => 'Maestría no encontrada para el alumno.'], 404);
        }

        // Obtener descuentos activos, formateándolos como objeto clave => valor
        $descuentos = Descuento::where('activo', true)->get()->mapWithKeys(function ($descuento) use ($maestria) {
            $montoDescuento = ($descuento->porcentaje / 100) * $maestria->arancel;
            return [
                strtolower(str_replace(' ', '_', $descuento->nombre)) => [
                    'id' => $descuento->id,
                    'nombre' => $descuento->nombre,
                    'descuento' => $montoDescuento,
                    'total' => $maestria->arancel - $montoDescuento,
                    'requisitos' => $descuento->requisitos ? json_decode($descuento->requisitos) : [],
                ]
            ];
        });

        $programa = [
            'nombre' => $maestria->nombre,
            'arancel' => $maestria->arancel,
            'descuentos' => $descuentos,
        ];

        return response()->json(['programa' => $programa, 'alumno' => $alumno]);
    }


    public function processDescuento(Request $request)
    {
        $request->validate([
            'dni' => 'required|string',
            'descuento_id' => 'required|exists:descuentos,id',
            'documento' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);

        $alumno = Alumno::where('dni', $request->dni)->first();

        if (!$alumno) {
            return redirect()->back()->with('error', 'Alumno no encontrado.');
        }

        $descuento = Descuento::find($request->descuento_id);

        if (!$descuento || !$descuento->activo) {
            return redirect()->back()->with('error', 'Descuento inválido.');
        }

        // Actualizar descuento asignado al alumno
        $alumno->descuento_id = $descuento->id;

        if ($request->hasFile('documento')) {
            $documentoPath = $request->file('documento')->store('documentos_autenticidad', 'public');
            $alumno->documento_autenticidad = $documentoPath;
        }

        $maestria = $alumno->maestria;

        if (!$maestria) {
            return redirect()->back()->with('error', 'Maestría no encontrada para el alumno.');
        }

        // Calcular monto con descuento
        $monto_total = $maestria->arancel - ($maestria->arancel * ($descuento->porcentaje / 100));
        $alumno->monto_total = $monto_total;

        $alumno->save();

        return redirect()->route('descuentos.alumnos')->with('success', 'Descuento aplicado y monto total actualizado.');
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
