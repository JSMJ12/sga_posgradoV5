<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentoPostulante;

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
            'Cédula' => 'Cédula',
            'Papel Votación' => 'Papel Votación',
            'Título Universidad' => 'Título Universidad',
            'Hoja de Vida' => 'Hoja de Vida',
            'CONADIS' => 'CONADIS',
            'Carta de Aceptación' => 'Carta de Aceptación',
            'Comprobante de Pago' => 'Comprobante de Pago',
        ];

        // Procesar cada documento
        foreach ($documentos as $titulo => $tipo_documento) {
            // Buscar o crear el registro del documento en la base de datos
            $documento = DocumentoPostulante::firstOrNew([
                'dni_postulante' => $dni_postulante,
                'tipo_documento' => $tipo_documento,
            ]);

            // Verificar si el documento está marcado como verificado en el formulario
            $documento->verificado = $request->has("documentos_verificados.{$titulo}");

            // Guardar el registro
            $documento->ruta_documento = $documento->ruta_documento ?? ''; // Mantener la ruta existente o establecer un valor predeterminado si es necesario
            $documento->save();
        }
        

        return redirect()->back()->with('success', 'Los documentos del postulante se han actualizado correctamente.');
    
    }
}
