<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\Maestria;
use App\Models\Asignatura;
use App\Models\Secretario;
use App\Models\AsignaturaDocente;

class AsignaturaDocenteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($docente_dni)
    {
        $docente = Docente::findOrFail($docente_dni);
        $user = auth()->user();

        // Inicializamos las asignaturas y maestrías
        $asignaturas = Asignatura::all();
        $maestrias = Maestria::where('status', 'ACTIVO')->get();

        if ($user->hasRole('Secretario')) {
            $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();

            // Obtener las maestrías a las que el secretario tiene acceso
            $maestriasIds = $secretario->seccion->maestrias->pluck('id');
            $maestrias = Maestria::whereIn('id', $maestriasIds)
                ->where('status', 'ACTIVO')
                ->get();

            // Obtener las asignaturas correspondientes a esas maestrías
            $asignaturas = Asignatura::whereIn('maestria_id', $maestriasIds)->get();
        }

        // Obtener las asignaturas ya asignadas al docente
        $asignaturasAsignadas = AsignaturaDocente::where('docente_dni', $docente_dni)
            ->pluck('asignatura_id')
            ->toArray();

        // Pasar la información de las asignaturas ya asignadas
        return view('asignaturas_docentes.create', compact('docente', 'asignaturas', 'maestrias', 'asignaturasAsignadas'));
    }


    public function store(Request $request)
    {
        $asignaturas = $request->input('asignaturas');
        $docente_dni = $request->input('docente_dni');

        if (is_array($asignaturas)) {
            foreach ($asignaturas as $asignatura) {
                // Utilizar updateOrCreate para crear o actualizar la asignación
                AsignaturaDocente::updateOrCreate(
                    ['docente_dni' => $docente_dni, 'asignatura_id' => $asignatura],
                    ['docente_dni' => $docente_dni, 'asignatura_id' => $asignatura]
                );
            }
        } else {
            // Utilizar updateOrCreate para crear o actualizar la asignación
            AsignaturaDocente::updateOrCreate(
                ['docente_dni' => $docente_dni, 'asignatura_id' => $asignaturas],
                ['docente_dni' => $docente_dni, 'asignatura_id' => $asignaturas]
            );
        }

        return redirect()->route('docentes.index')->with('success', '¡Asignaturas asignadas con éxito!');
    }


    public function destroy($docente_dni, $asignatura_id)
    {
        $asignaturaDocente = AsignaturaDocente::where('docente_dni', $docente_dni)
            ->where('asignatura_id', $asignatura_id)
            ->first();

        if ($asignaturaDocente) {
            $asignaturaDocente->delete();
        }

        return redirect()->back()->with('success', 'Asignatura eliminada correctamente');
    }
}
