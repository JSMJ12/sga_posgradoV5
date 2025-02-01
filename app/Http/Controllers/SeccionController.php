<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Maestria;
use App\Models\Seccion;

class SeccionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        // Obtener todas las secciones con sus maestrías asociadas
        $secciones = Seccion::with('maestrias')->paginate($perPage);

        // Obtener todas las maestrías que no están asociadas a ninguna sección
        $maestrias_noasignadas = Maestria::whereDoesntHave('secciones')->get();

        $maestrias = Maestria::all();

        return view('secciones.index', compact('secciones', 'maestrias_noasignadas', 'perPage', 'maestrias'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:secciones,nombre',
            'maestrias' => 'required|array|min:1',
            'maestrias.*' => 'exists:maestrias,id',
        ]);

        $seccion = new Seccion();
        $seccion->nombre = $validatedData['nombre'];
        $seccion->save();

        $maestrias = collect($validatedData['maestrias'])->unique();
        $seccion->maestrias()->attach($maestrias);

        return redirect()->route('secciones.index')->with('success', 'Sección creada exitosamente');
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:secciones,nombre,' . $id,
            'maestrias' => 'required|array|min:1',
            'maestrias.*' => 'exists:maestrias,id',
        ]);

        $seccion = Seccion::findOrFail($id);

        $seccion->nombre = $validatedData['nombre'];
        $seccion->save();

        $maestrias = collect($validatedData['maestrias'])->unique();
        $seccion->maestrias()->sync($maestrias);

        return back()->with('success', 'Sección actualizada exitosamente');
    }

    public function destroy($id)
    {
        $seccion = Seccion::findOrFail($id);
        $seccion->maestrias()->detach();
        $seccion->delete();

        return redirect()->route('secciones.index')->with('success', 'Sección eliminada exitosamente');
    }

    public function show($id)
    {
        $seccion = Seccion::with('maestrias')->findOrFail($id);
        return response()->json($seccion);
    }
}
