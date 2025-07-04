<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;
use App\Models\Alumno;
use Illuminate\Support\Facades\Auth;
use App\Models\Postulante;
use App\Notifications\PagoRechazado;
use Illuminate\Support\Facades\Storage;
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
                    $email = $pago->user->email ?? null;
                    $alumno = Alumno::where('email_institucional', $email)->first();
                    $postulante = Postulante::where('correo_electronico', $email)->first();
                    return $alumno->dni ?? $postulante->dni ?? 'N/A';
                })
                ->filterColumn('dni', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where(function ($q2) use ($keyword) {
                            $emails1 = Alumno::where('dni', 'like', "%$keyword%")->pluck('email_institucional');
                            $emails2 = Postulante::where('dni', 'like', "%$keyword%")->pluck('correo_electronico');
                            $q2->whereIn('email', $emails1)->orWhereIn('email', $emails2);
                        });
                    });
                })

                ->addColumn('email', function ($pago) {
                    return $pago->user->email ?? 'N/A';
                })
                ->filterColumn('email', function ($query, $keyword) {
                    $query->whereHas('user', fn($q) => $q->where('email', 'like', "%$keyword%"));
                })

                ->addColumn('celular', function ($pago) {
                    $email = $pago->user->email ?? null;
                    $alumno = Alumno::where('email_institucional', $email)->first();
                    $postulante = Postulante::where('correo_electronico', $email)->first();
                    return $alumno->celular ?? $postulante->celular ?? 'N/A';
                })
                ->filterColumn('celular', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $emails1 = Alumno::where('celular', 'like', "%$keyword%")->pluck('email_institucional');
                        $emails2 = Postulante::where('celular', 'like', "%$keyword%")->pluck('correo_electronico');
                        $q->whereIn('email', $emails1)->orWhereIn('email', $emails2);
                    });
                })

                ->addColumn('image', fn($pago) => $pago->user->image ?? null)
                ->filterColumn('image', function ($query, $keyword) {
                    $query->whereHas('user', fn($q) => $q->where('image', 'like', "%$keyword%"));
                })

                ->addColumn('tipo_pago', fn($pago) => ucfirst($pago->tipo_pago ?? 'N/A'))
                ->filterColumn('tipo_pago', fn($query, $keyword) => $query->where('tipo_pago', 'like', "%$keyword%"))

                ->addColumn('modalidad_pago', fn($pago) => ucfirst($pago->modalidad_pago ?? 'N/A'))
                ->filterColumn('modalidad_pago', fn($query, $keyword) => $query->where('modalidad_pago', 'like', "%$keyword%"))

                ->addColumn('acciones', function ($pago) {
                    if (!$pago->verificado) {
                        return '
                        <form id="form-verificar-' . $pago->id . '" action="' . route('pagos.verificar', $pago->id) . '" method="POST" style="display:inline-block; margin-right: 10px;">
                            ' . csrf_field() . method_field('PATCH') . '
                            <button type="button" class="btn btn-success btn-sm" onclick="confirmarVerificacion(' . $pago->id . ')">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <form id="form-rechazar-' . $pago->id . '" action="' . route('pagos.rechazar', $pago->id) . '" method="POST" style="display:inline-block;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmarRechazo(' . $pago->id . ')">
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
        $montoDescuento = ($arancel * $porcentajeDescuento) / 100;
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
        try {
            // Encontrar el pago por su ID
            $pago = Pago::with('user')->findOrFail($id);

            // Verificar el tipo de pago
            $pago_tipo = $pago->tipo_pago;

            // Buscar primero en alumnos
            $alumno = Alumno::where('email_institucional', $pago->user->email)->first();

            if ($alumno) {
                if ($pago_tipo === 'matricula') {
                    $nuevo_monto_matricula = $alumno->monto_matricula - $pago->monto;
                    $alumno->update(['monto_matricula' => $nuevo_monto_matricula]);
                } elseif ($pago_tipo === 'inscripcion') {
                    $nuevo_monto_inscripcion = $alumno->monto_inscripcion - $pago->monto;
                    $alumno->update(['monto_inscripcion' => $nuevo_monto_inscripcion]);
                } elseif ($pago_tipo === 'arancel') {
                    $nuevo_monto_total = $alumno->monto_total - $pago->monto;
                    $alumno->update(['monto_total' => $nuevo_monto_total]);
                }

                // Marcar el pago como verificado
                $pago->update(['verificado' => true]);

                return redirect()->route('pagos.index')->with('success', 'Pago verificado y monto del alumno actualizado.');
            }

            // Si no es alumno, buscar en postulantes
            $postulante = Postulante::where('correo_electronico', $pago->user->email)->first();

            if ($postulante) {
                if ($pago_tipo === 'matricula') {
                    $nuevo_monto_matricula = $postulante->monto_matricula - $pago->monto;
                    $postulante->update(['monto_matricula' => $nuevo_monto_matricula]);
                } elseif ($pago_tipo === 'inscripcion') {
                    $nuevo_monto_inscripcion = $postulante->monto_inscripcion - $pago->monto;
                    $postulante->update(['monto_inscripcion' => $nuevo_monto_inscripcion]);
                }

                // Marcar el pago como verificado para el postulante
                $pago->update(['verificado' => true]);

                return redirect()->route('pagos.index')->with('success', 'Pago verificado para postulante.');
            }

            // Si no es ni alumno ni postulante
            return redirect()->route('pagos.index')->with('error', 'No se encontró un alumno o postulante asociado al pago.');
        } catch (\Exception $e) {
            // Capturar cualquier error inesperado y mostrar mensaje
            return redirect()->route('pagos.index')->with('error', 'Error al verificar el pago: ' . $e->getMessage());
        }
    }

    public function rechazar_pago($id)
    {
        // Cargar el pago con su relación user
        $pago = Pago::with('user')->findOrFail($id);
        $usuario = $pago->user;

        if (!$usuario) {
            return redirect()->route('pagos.index')->with('error', 'Usuario no asociado al pago.');
        }
        $usuario->notify(new PagoRechazado($pago, $usuario));
        if ($pago->archivo_comprobante && Storage::disk('public')->exists($pago->archivo_comprobante)) {
            Storage::disk('public')->delete($pago->archivo_comprobante);
        }

        $pago->delete();

        return redirect()->route('pagos.index')->with('success', 'Pago rechazado y eliminado correctamente. Se ha notificado al usuario.');
    }
}
