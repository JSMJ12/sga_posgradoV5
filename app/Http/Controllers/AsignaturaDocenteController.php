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
        $asignaturas = Asignatura::all();
        $user = auth()->user();

        if ($user->hasRole('Secretario')) {
            $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();
            //
            $maestriasIds = $secretario->seccion->maestrias->pluck('id');
            $maestrias = Maestria::whereIn('id', $maestriasIds)
                ->where('status', 'ACTIVO')
                ->get();
            $asignaturas = Asignatura::whereIn('maestria_id', $maestriasIds)->get();
        } else {
            $asignaturas = Asignatura::all();
            $maestrias = Maestria::where('status', 'ACTIVO')->get();
        }

        return view('asignaturas_docentes.create', compact('docente', 'asignaturas', 'maestrias'));
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
