<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentoPostulante;
use App\Models\Postulante;
use Yajra\DataTables\Facades\DataTables;

class DocumentoPostulanteController extends Controller
{
    /**
     * Verificar los documentos de un postulante.
     *
     * @param Request $request
     * @param string $dni
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verificar(Request $request, $dni_postulante)
    {
        $documentos = [
            'Cédula',
            'Papel de Votación',
            'Título de Universidad',
            'Hoja de Vida',
            'CONADIS',
            'Carta de Aceptación',
            'Comprobante de Pago',
        ];

        try {
            // Primero verifica el Comprobante de Pago
            if ($request->input('tipo_documento') === 'Comprobante de Pago') {
                // Actualiza solo el Comprobante de Pago
                $documento = DocumentoPostulante::firstOrNew([
                    'dni_postulante' => $dni_postulante,
                    'tipo_documento' => 'Comprobante de Pago',
                ]);
                $documento->verificado = true; // Lo marca como verificado
                $documento->ruta_documento = $documento->ruta_documento ?? null; // Si no tiene ruta, la establece a null
                $documento->save();

                return redirect(route('postulaciones.index'))->with('success', 'El comprobante de pago se ha actualizado correctamente.');
            }

            // Si no es el comprobante de pago, procedemos con los otros documentos
            foreach ($documentos as $tipo_documento) {
                // Skip 'Comprobante de Pago' ya que ya fue procesado
                if ($tipo_documento === 'Comprobante de Pago') {
                    continue;
                }

                // Buscar o crear el documento
                $documento = DocumentoPostulante::firstOrNew([
                    'dni_postulante' => $dni_postulante,
                    'tipo_documento' => $tipo_documento,
                ]);

                // Si el documento está en el request, lo marcamos como verificado
                if ($request->has("documentos_verificados") && isset($request->documentos_verificados[$tipo_documento])) {
                    $documento->verificado = true;
                } else {
                    $documento->verificado = false;
                }

                // Mantener la ruta existente o establecer NULL si está vacía
                if (!$documento->ruta_documento) {
                    $documento->ruta_documento = null;
                }

                $documento->save();
            }

            return redirect()->back()->with('success', 'Los documentos del postulante se han actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar los documentos.');
        }
    }


    public function pagosMatricula(Request $request)
    {
        if ($request->ajax()) {
            $documentos = DocumentoPostulante::where('tipo_documento', 'Comprobante de Pago')
                ->where('verificado', 0)
                ->with('postulante')
                ->get();

            return datatables()->collection($documentos)
                ->addColumn('foto', function ($documento) {
                    return '<img src="' . asset('storage/' . $documento->postulante->imagen) . '" 
                        alt="Imagen de ' . $documento->postulante->nombre1 . '" 
                        style="max-width: 60px; border-radius: 50%;">';
                })
                ->addColumn('dni', function ($documento) {
                    return $documento->postulante->dni;
                })
                ->addColumn('nombre_completo', function ($documento) {
                    return $documento->postulante->nombre1 . ' ' . $documento->postulante->nombre2 . ' ' .
                        $documento->postulante->apellidop . ' ' . $documento->postulante->apellidom;
                })
                ->addColumn('correo_electronico', function ($documento) {
                    return $documento->postulante->correo_electronico;
                })
                ->addColumn('celular', function ($documento) {
                    return $documento->postulante->celular;
                })
                ->addColumn('archivo_comprobante', function ($documento) {
                    return '<a href="' . asset('storage/' . $documento->postulante->pago_matricula) . '" target="_blank" class="btn btn-info btn-sm" title="Ver Comprobante">
                            <i class="fas fa-file-alt"></i>
                        </a>';
                })
                ->addColumn('acciones', function ($documento) {
                    $dni = $documento->postulante->dni;
                    return '<form id="form-verificar-' . $dni . '" action="' . route('documentos.verificar', $dni) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . '
                                <input type="hidden" name="tipo_documento" value="Comprobante de Pago">
                                <button type="button" class="btn btn-success btn-sm" onclick="confirmarVerificacion(\'' . $dni . '\')">
                                    <i class="fas fa-check"></i> Aprobar 
                                </button>
                            </form>';
                })

                ->rawColumns(['foto', 'nombre_completo', 'archivo_comprobante', 'acciones'])
                ->toJson();
        }

        return view('pagos.pagos_matriculas');
    }
}
