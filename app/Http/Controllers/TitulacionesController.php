<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\TasaTitulacion;
use App\Models\Titulacion;
use Illuminate\Http\Request;

class TitulacionesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Request $request)
    {
        // Validar que los archivos sean PDFs
        $request->validate([
            'tesis_path.*' => 'required|mimes:pdf|max:2048',
            'fecha_graduacion' => 'required|date',
        ]);

        // Guardar los archivos
        if ($request->hasFile('tesis_path')) {
            foreach ($request->file('tesis_path') as $file) {
                $path = $file->store('titulaciones', 'public');
            }
        }

        Titulacion::create([
            'alumno_dni' => $request->alumno_dni,
            'titulado' => $request->titulado,
            'fecha_graduacion' => $request->fecha_graduacion,
            'tesis_path' => $path,
        ]);
        $alumnoDni = $request->alumno_dni;
        $alumno = Alumno::where('dni', $alumnoDni)->first();

        if (!$alumno) {
            return redirect()->back()->with('error', 'Alumno no encontrado');
        }

        // Obtener la primera matrícula del alumno
        $matricula = $alumno->matriculas()->first();
        if (!$matricula) {
            return redirect()->back()->with('error', 'Matrícula no encontrada');
        }

        // Obtener el cohorte y la maestría
        $cohorteId = $matricula->cohorte_id;
        $maestriaId = $alumno->maestria_id;

        // Buscar o crear la tasa de titulación para el cohorte y la maestría
        $tasaTitulacion = TasaTitulacion::where('cohorte_id', $cohorteId)
            ->where('maestria_id', $maestriaId)
            ->first();

        if ($tasaTitulacion) {
            $tasaTitulacion->graduados += 1;
            $tasaTitulacion->save();
        } else {
            // Si no existe, lo creamos con valores iniciales
            TasaTitulacion::create([
                'cohorte_id' => $cohorteId,
                'maestria_id' => $maestriaId,
                'graduados' => 1,
            ]);
        }

        return redirect()->back()->with('success', 'Titulación registrada correctamente');
    }
}
