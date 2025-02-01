<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Alumno;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class PagoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $pagos = Pago::with('alumno.matriculas')->orderBy('verificado', 'asc');

            return datatables()
                ->eloquent($pagos)
                ->addColumn('acciones', function ($pago) {
                    if (!$pago->verificado) {
                        return '
                        <form id="form-verificar-' . $pago->id . '" action="' . route('pagos.verificar', $pago->id) . '" method="POST">
                            ' . csrf_field() . method_field('PATCH') . '
                            <button type="button" class="btn btn-success btn-sm" onclick="confirmarVerificacion(' . $pago->id . ')">
                                <i class="fas fa-check"></i> Aprobado
                            </button>
                        </form>
                    ';
                    }
                    return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Verificado</span>';
                })
                ->rawColumns(['acciones'])
                ->make(true);
        }
    
        return view('pagos.index');
    }
    
    public function pago()
    {
        $user = Auth::user();

        // Buscar al alumno
        $alumno = Alumno::where('nombre1', $user->name)
            ->where('email_institucional', $user->email)
            ->first();

        if (!$alumno) {
            return redirect()->back()->with('error', 'Alumno no encontrado.');
        }

        $maestria = $alumno->maestria;

        if (!$maestria) {
            return redirect()->back()->with('error', 'Maestría no encontrada para el alumno.');
        }

        // Calcular descuento y total a pagar según el tipo de descuento del alumno
        $descuento = 0;
        $total_pagar = 0;

        if ($alumno->descuento == 'academico') {
            $descuento = $maestria->arancel * 0.30;
            $total_pagar = $maestria->arancel * 0.70;
        } elseif ($alumno->descuento == 'socioeconomico') {
            $descuento = $maestria->arancel * 0.20;
            $total_pagar = $maestria->arancel * 0.80;
        } elseif ($alumno->descuento == 'graduados') {
            $descuento = $maestria->arancel * 0.20;
            $total_pagar = $maestria->arancel * 0.80;
        } elseif ($alumno->descuento == 'mejor_graduado') {
            $descuento = $maestria->arancel * 1;
            $total_pagar = 0;
        }

        $programa = [
            'nombre' => $maestria->nombre,
            'arancel' => $maestria->arancel,
            'descuento' => $descuento,
            'total_pagar' => $total_pagar,
        ];

        $pagos = $alumno->pagos;

        return view('pagos.pago', compact('programa', 'alumno', 'pagos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|exists:alumnos,dni',
            'modalidad_pago' => 'required|string|in:unico,trimestral,otro',
            'fecha_pago' => 'required|date',
            'archivo_comprobante' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4048',
        ]);

        $alumno = Alumno::where('dni', $request->dni)->first();

        if (!$alumno) {
            return redirect()->back()->with('error', 'Alumno no encontrado.');
        }

        // Obtener el monto total previamente calculado
        $total_pagar = $alumno->monto_total;

        // Ajustar el monto a pagar según la modalidad seleccionada
        $monto_pagar = $request->monto;

        // Guardar el archivo comprobante
        $archivo_comprobante = $request->file('archivo_comprobante');
        $archivo_path = $archivo_comprobante->store('comprobantes', 'public');

        // Crear el registro de pago usando el método create
        $pago = Pago::create([
            'dni' => $alumno->dni,
            'monto' => $monto_pagar,
            'fecha_pago' => $request->fecha_pago,
            'archivo_comprobante' => $archivo_path,
            'modalidad_pago' => $request->modalidad_pago
        ]);

        return redirect()->route('pagos.pago')->with('success', 'Pago realizado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha_pago' => 'required|date',
            'pagado' => 'required|boolean',
        ]);

        $pago = Pago::findOrFail($id);
        $pago->update($request->all());

        return redirect()->route('pagos.index')->with('success', 'Pago actualizado con éxito.');
    }
    
    public function showDescuentoForm($dni)
    {
        // Busca al alumno por su DNI
        $alumno = Alumno::where('dni', $dni)->first();

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado.'], 404);
        }

        // Obtén la maestría del alumno
        $maestria = $alumno->maestria;

        if (!$maestria) {
            return response()->json(['error' => 'Maestría no encontrada para el alumno.'], 404);
        }

        // Datos del programa con descuentos y requisitos
        $programa = [
            'nombre' => $maestria->nombre,
            'arancel' => $maestria->arancel,
            'descuentos' => [
                'academico' => [
                    'descuento' => $maestria->arancel * 0.30,
                    'total' => $maestria->arancel * 0.70,
                    'requisitos' => ['Promedio mayor a 9.6'],
                    'color' => 'success',
                ],
                'socioeconomico' => [
                    'descuento' => $maestria->arancel * 0.20,
                    'total' => $maestria->arancel * 0.80,
                    'requisitos' => ['Condición socioeconómica comprobada'],
                    'color' => 'warning',
                ],
                'graduados' => [
                    'descuento' => $maestria->arancel * 0.20,
                    'total' => $maestria->arancel * 0.80,
                    'requisitos' => ['Ser graduado en cualquier programa de pregrado ofrecido por UNESUM'],
                    'color' => 'primary',
                ],
                'mejor_graduado' => [
                    'descuento' => $maestria->arancel * 1.00,
                    'total' => 0,
                    'requisitos' => [
                        'Certificado de mejor graduado',
                        'Certificación de los dos últimos periodos académicos',
                    ],
                    'color' => 'danger',
                ],
            ]
        ];

        return response()->json(['programa' => $programa, 'alumno' => $alumno]);
    }

    public function processDescuento(Request $request)
    {
        $alumno = Alumno::where('dni', $request->dni)
            ->first();

        if (!$alumno) {
            return redirect()->back()->with('error', 'Alumno no encontrado.');
        }

        $request->validate([
            'descuento' => 'required',
        ]);

        $alumno->descuento = $request->input('descuento');

        if ($request->hasFile('documento')) {
            $documentoPath = $request->file('documento')->store('documentos_autenticidad', 'public');
            $alumno->documento_autenticidad = $documentoPath;
        }

        $maestria = $alumno->maestria;

        if (!$maestria) {
            return redirect()->back()->with('error', 'Maestría no encontrada para el alumno.');
        }

        $arancel = $maestria->arancel;
        $descuento = 0;

        switch ($alumno->descuento) {
            case 'academico':
                $descuento = $arancel * 0.30;
                break;
            case 'socioeconomico':
                $descuento = $arancel * 0.20;
                break;
            case 'graduados':
                $descuento = $arancel * 0.20;
                break;
            case 'mejor_graduado':
                $descuento = $arancel;
                break;
            default:
                $descuento = 0;
                break;
        }
        $monto_total = $arancel - $descuento;

        $alumno->monto_total = $monto_total;

        $alumno->save();

        return redirect()->route('descuentos.alumnos')->with('success', 'Descuento aplicado y monto total actualizado.');
    }
    public function verificar_pago($id)
    {
        // Encontrar el pago por su ID
        $pago = Pago::findOrFail($id);

        // Encontrar el alumno por el DNI del pago
        $alumno = Alumno::where('dni', $pago->dni)->first();
        if (!$alumno) {
            return redirect()->route('pagos.index')->with('error', 'Alumno no encontrado.');
        }

        // Restar el monto del pago del monto total del alumno
        $nuevo_monto_total = $alumno->monto_total - $pago->monto;
        // Asegurarse de que el nuevo monto total no sea negativo
        if ($nuevo_monto_total < 0) {
            return redirect()->route('pagos.index')->with('error', 'El monto del pago es mayor que el monto total del alumno.');
        }

        // Actualizar el monto total del alumno
        $alumno->update(['monto_total' => $nuevo_monto_total]);
        // Actualizar el campo verificado del pago a true
        $pago->update(['verificado' => true]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('pagos.index')->with('success', 'Pago verificado con éxito y monto total actualizado.');
    }
}
