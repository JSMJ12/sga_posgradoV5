<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Tesis;

class TitulacionAlumnoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Request $request)
    {
        try {
            // Validar que venga el id de la tesis
            $request->validate([
                'tesis_id' => 'required|exists:tesis,id',
            ], [
                'tesis_id.required' => 'El campo tesis es obligatorio.',
                'tesis_id.exists'   => 'La tesis seleccionada no existe en el sistema.',
            ]);

            // Buscar la tesis
            $tesis = Tesis::findOrFail($request->tesis_id);

            // Cambiar estado a titulado
            $tesis->estado = 'titulado';
            $tesis->save();

            return redirect()->back()->with('success', 'Alumno titulado correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Captura errores de validación y los devuelve en español
            return redirect()->back()
                            ->withErrors($e->validator)
                            ->withInput();
        } catch (\Exception $e) {
            // Captura cualquier otro error y lo muestra
            return redirect()->back()
                            ->with('error', 'Ocurrió un error al titular al alumno: ' . $e->getMessage())
                            ->withInput();
        }
    }

}
