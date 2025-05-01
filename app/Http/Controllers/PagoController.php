<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;
use App\Models\Alumno;
use Illuminate\Support\Facades\Auth;
use App\Models\Postulante;
use App\Notifications\PagoRechazado;
use Illuminate\Support\Facades\Notification;

class PagoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $pagos = Pago::with('user')->orderBy('verificado', 'asc');

            return datatables()
                ->eloquent($pagos)
                ->addColumn('dni', function ($pago) {
                    $alumno = Alumno::where('email_institucional', $pago->user->email)->first();
                    $postulante = Postulante::where('correo_electronico', $pago->user->email)->first();
                    return $alumno->dni ?? $postulante->dni ?? 'N/A';
                })
                ->addColumn('email', function ($pago) {
                    $alumno = Alumno::where('email_institucional', $pago->user->email)->first();
                    $postulante = Postulante::where('correo_electronico', $pago->user->email)->first();
                    return $alumno->email_institucional ?? $postulante->correo_electronico ?? 'N/A';
                })
                ->addColumn('celular', function ($pago) {
                    $alumno = Alumno::where('email_institucional', $pago->user->email)->first();
                    $postulante = Postulante::where('correo_electronico', $pago->user->email)->first();
                    return $alumno->celular ?? $postulante->celular ?? 'N/A';
                })
                ->addColumn('image', function ($pago) {
                    return $pago->user->image ?? null;
                })
                ->addColumn('tipo_pago', function ($pago) {
                    return $pago->tipo_pago ?? 'N/A';
                })
                ->addColumn('modalidad_pago', function ($pago) {
                    return $pago->modalidad_pago ?? 'N/A';
                })
                ->addColumn('acciones', function ($pago) {
                    if (!$pago->verificado) {
                        return '
                        <form id="form-verificar-' . $pago->id . '" action="' . route('pagos.verificar', $pago->id) . '" method="POST" style="display:inline-block; margin-right: 10px;">
                            ' . csrf_field() . method_field('PATCH') . '
                            <button type="button" class="btn btn-success btn-sm" onclick="confirmarVerificacion(' . $pago->id . ')" title="Aprobar">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <form id="form-rechazar-' . $pago->id . '" action="' . route('pagos.rechazar', $pago->id) . '" method="POST" style="display:inline-block;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmarRechazo(' . $pago->id . ')" title="Rechazar">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>';
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

        // Cargar alumno con sus relaciones maestria y descuento
        $alumno = Alumno::with(['maestria', 'descuento'])
            ->where('email_institucional', $user->email)
            ->first();

        if (!$alumno) {
            return redirect()->back()->with('error', 'Alumno no encontrado.');
        }

        $maestria = $alumno->maestria;
        $descuentoRelacion = $alumno->descuento;

        if (!$maestria) {
            return redirect()->back()->with('error', 'Maestría no encontrada para el alumno.');
        }

        // Cálculo del descuento y total a pagar
        $arancel = $maestria->arancel;
        $porcentajeDescuento = $descuentoRelacion?->porcentaje ?? 0;
        $montoDescuento = $arancel * $porcentajeDescuento;
        $total_pagar = $arancel - $montoDescuento;

        $programa = [
            'nombre' => $maestria->nombre,
            'arancel' => $arancel,
            'descuento' => $montoDescuento,
            'total_pagar' => $total_pagar,
            'tipo_descuento' => $descuentoRelacion?->nombre ?? 'Sin descuento',
        ];

        $pagos = $user->pagos()->latest()->get();
        $pagosPreviosArancel = $user->pagos()->where('tipo_pago', 'arancel')->exists();

        return view('pagos.pago', compact('programa', 'alumno', 'pagos', 'pagosPreviosArancel'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'modalidad_pago' => 'required|string|in:unico,trimestral,otro',
            'tipo_pago' => 'required|string|in:arancel,matricula,inscripcion',
            'fecha_pago' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'archivo_comprobante' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4048',
        ]);

        $user = auth()->user();

        if (!$user) {
            return redirect()->back()->with('error', 'Usuario no autenticado.');
        }

        $monto = $request->monto;

        // Verificar si se aplica el descuento del 5%
        if (
            $request->tipo_pago === 'arancel' &&
            $request->modalidad_pago === 'unico' &&
            !$user->pagos()->where('tipo_pago', 'arancel')->exists()
        ) {
            $monto = $monto * 0.95; // aplicar 5% de descuento
        }

        // Guardar el archivo comprobante
        $archivo_comprobante = $request->file('archivo_comprobante');
        $archivo_path = $archivo_comprobante->store('comprobantes', 'public');

        // Crear el registro de pago
        Pago::create([
            'user_id' => $user->id,
            'monto' => $monto,
            'fecha_pago' => $request->fecha_pago,
            'archivo_comprobante' => $archivo_path,
            'modalidad_pago' => $request->modalidad_pago,
            'tipo_pago' => $request->tipo_pago,
        ]);

        // Verificar si el usuario tiene el rol de "postulante"
        if ($user->hasRole('Postulante')) {
            return redirect()->route('dashboard_postulante')->with('success', 'Pago realizado exitosamente.');
        }

        // Redirigir a la ruta por defecto si no es "postulante"
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


    public function verificar_pago($id)
    {
        // Encontrar el pago por su ID
        $pago = Pago::with('user')->findOrFail($id);

        // Verificar el tipo de pago
        $pago_tipo = $pago->tipo_pago;


        // Buscar primero en alumnos
        $alumno = Alumno::where('email_institucional', $pago->user->email)->first();

        if ($alumno) {
            // Si es un alumno y el pago es de tipo matrícula
            if ($pago_tipo === 'matricula') {
                $nuevo_monto_matricula = $alumno->monto_matricula - $pago->monto;

                if ($nuevo_monto_matricula < 0) {
                    return redirect()->route('pagos.index')->with('error', 'El monto del pago es mayor que el monto de matrícula del alumno.');
                }

                // Guardar el nuevo monto de matrícula
                $alumno->update(['monto_matricula' => $nuevo_monto_matricula]);

                // Si es un alumno y el pago es de tipo inscripción
            } elseif ($pago_tipo === 'inscripcion') {
                $nuevo_monto_inscripcion = $alumno->monto_inscripcion - $pago->monto;

                if ($nuevo_monto_inscripcion < 0) {
                    return redirect()->route('pagos.index')->with('error', 'El monto del pago es mayor que el monto de inscripción del alumno.');
                }

                // Guardar el nuevo monto de inscripción
                $alumno->update(['monto_inscripcion' => $nuevo_monto_inscripcion]);

                // Si es un alumno y el pago es de tipo arancel
            } elseif ($pago_tipo === 'arancel') {
                $nuevo_monto_total = $alumno->monto_total - $pago->monto;

                if ($nuevo_monto_total < 0) {
                    return redirect()->route('pagos.index')->with('error', 'El monto del pago es mayor que el monto total del alumno.');
                }

                // Guardar el nuevo monto total
                $alumno->update(['monto_total' => $nuevo_monto_total]);
            }

            // Marcar el pago como verificado
            $pago->update(['verificado' => true]);

            return redirect()->route('pagos.index')->with('success', 'Pago verificado y monto del alumno actualizado.');
        }

        // Si no es alumno, buscar en postulantes
        $postulante = Postulante::where('correo_electronico', $pago->user->email)->first();

        if ($postulante) {
            // Si es postulante y el pago es de tipo matrícula
            if ($pago_tipo === 'matricula') {
                $nuevo_monto_matricula = $postulante->monto_matricula - $pago->monto;

                if ($nuevo_monto_matricula < 0) {
                    return redirect()->route('pagos.index')->with('error', 'El monto del pago es mayor que el monto de matrícula del postulante.');
                }

                // Guardar el nuevo monto de matrícula
                $postulante->update(['monto_matricula' => $nuevo_monto_matricula]);

                // Si es postulante y el pago es de tipo inscripción
            } elseif ($pago_tipo === 'inscripcion') {

                $nuevo_monto_inscripcion = $postulante->monto_inscripcion - $pago->monto;

                if ($nuevo_monto_inscripcion < 0) {
                    return redirect()->route('pagos.index')->with('error', 'El monto del pago es mayor que el monto de inscripción del postulante.');
                }

                // Guardar el nuevo monto de inscripción
                $postulante->update(['monto_inscripcion' => $nuevo_monto_inscripcion]);
            }

            // Marcar el pago como verificado para el postulante
            $pago->update(['verificado' => true]);

            return redirect()->route('pagos.index')->with('success', 'Pago verificado para postulante.');
        }

        // Si no es ni alumno ni postulante
        return redirect()->route('pagos.index')->with('error', 'No se encontró un alumno o postulante asociado al pago.');
    }


    public function rechazar_pago($id)
    {
        // Cargar el pago con su relación user
        $pago = Pago::with('user')->findOrFail($id);
        $usuario = $pago->user;
        // Validar que el user esté presente
        if (!$usuario) {
            return redirect()->route('pagos.index')->with('error', 'Usuario no asociado al pago.');
        }
        // Enviar notificación al usuario
        $usuario->notify(new PagoRechazado($pago, $pago->user));

        // Eliminar el pago
        $pago->delete();

        // Redirigir con un mensaje
        return redirect()->route('pagos.index')->with('success', 'Pago rechazado y eliminado correctamente. Se ha notificado al usuario.');
    }
}
