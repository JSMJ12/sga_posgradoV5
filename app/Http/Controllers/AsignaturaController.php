<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Maestria;
use App\Models\Asignatura;
use App\Models\Docente;

class AsignaturaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'codigo_asignatura' => 'required|unique:asignaturas',
            'credito' => 'required|numeric',
            'itinerario' => 'nullable',
            'unidad_curricular'=> 'nullable',
            'maestria_id' => 'required|exists:maestrias,id',
        ]);


        $asignatura = Asignatura::create([
            'nombre' => $request->nombre,
            'codigo_asignatura' => $request->codigo_asignatura,
            'credito' => $request->credito,
            'itinerario' => $request->itinerario,
            'unidad_curricular' => $request->unidad_curricular,
            'maestria_id' => $request->maestria_id,
        ]);

        return redirect()->route('maestrias.index')->with('success', 'Asignatura creada exitosamente.');
    }

    public function update(Request $request, Asignatura $asignatura)
    {
        $request->validate([
            'nombre' => 'required',
            'codigo_asignatura' => 'required|unique:asignaturas,codigo_asignatura,'.$asignatura->id,
            'credito' => 'required|numeric',
            'itinerario' => 'nullable',
            'unidad_curricular'=> 'nullable',
            'maestria_id' => 'required|exists:maestrias,id',
        ]);

        $asignatura->nombre = $request->nombre;
        $asignatura->codigo_asignatura = $request->codigo_asignatura;
        $asignatura->credito = $request->credito;
        $asignatura->itinerario = $request->itinerario;
        $asignatura->unidad_curricular = $request->unidad_curricular;
        $asignatura->maestria_id = $request->maestria_id;
        $asignatura->save();

        return redirect()->route('maestrias.index')->with('success', 'Asignatura actualizada exitosamente.');
    }

    public function destroy(Asignatura $asignatura)
    {
        Storage::delete($asignatura->itinerario);
        $asignatura->delete();

        return redirect()->route('maestrias.index')->with('success', 'Asignatura eliminada exitosamente.');
    }
}
