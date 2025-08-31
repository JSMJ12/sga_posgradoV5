<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\TasaTitulacion;
use App\Models\Titulacion;
use Illuminate\Http\Request;
use App\Models\Tesis;



class TitulacionesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Request $request)
    {
        // Validar que los archivos sean PDFs y que se envíe tesis_id
        $request->validate([
            'tesis_id' => 'required|exists:tesis,id',
            'tesis_path.*' => 'required|mimes:pdf|max:2048',
            'fecha_graduacion' => 'required|date',
        ]);

        $tesisId = $request->tesis_id;

        // Guardar los archivos (si vienen múltiples, puedes adaptar según necesidad)
        $path = null;
        if ($request->hasFile('tesis_path')) {
            foreach ($request->file('tesis_path') as $file) {
                $path = $file->store('titulaciones', 'public');
                // Si quieres guardar todos los paths, puedes almacenarlos en un array y luego en JSON
            }
        }

        // Crear la titulación
        $titulacion = Titulacion::create([
            'tesis_id' => $tesisId,
            'titulado' => $request->titulado ?? 0,
            'fecha_graduacion' => $request->fecha_graduacion,
            'tesis_path' => $path,
        ]);

        // Obtener la tesis y su alumno
        $tesis = Tesis::with('alumno')->find($tesisId);
        if (!$tesis || !$tesis->alumno) {
            return redirect()->back()->with('error', 'Alumno de la tesis no encontrado');
        }

        $alumno = $tesis->alumno;

        // Obtener la matrícula del alumno correspondiente a la maestría de la tesis
        $matricula = $alumno->matriculas()->whereHas('cohorte', function($q) use ($tesis) {
            $q->where('maestria_id', $tesis->maestria_id);
        })->first();

        if (!$matricula) {
            return redirect()->back()->with('error', 'Matrícula correspondiente a la maestría de la tesis no encontrada');
        }

        // Obtener cohorte y maestría del alumno
        $cohorteId = $matricula->cohorte_id;
        $maestriaId = $tesis->maestria_id;
        $maestriaId = $maestria ? $maestria->id : null;

        // Actualizar o crear tasa de titulación
        $tasaTitulacion = TasaTitulacion::firstOrNew([
            'cohorte_id' => $cohorteId,
            'maestria_id' => $maestriaId,
        ]);
        $tasaTitulacion->graduados = ($tasaTitulacion->graduados ?? 0) + 1;
        $tasaTitulacion->save();

        return redirect()->back()->with('success', 'Titulación registrada correctamente');
    }

}
