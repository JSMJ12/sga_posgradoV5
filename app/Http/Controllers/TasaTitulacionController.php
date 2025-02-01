<?php

namespace App\Http\Controllers;

use App\Exports\EstudiantesExport;
use App\Exports\TitulacionExport;
use App\Models\Alumno;
use App\Models\Cohorte;
use App\Models\Maestria;
use App\Models\TasaTitulacion;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TasaTitulacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        // Cargar maestrías con sus cohortes
        $maestrias = Maestria::with('cohortes')->get();
        return view('tasa_titulacion.index', compact('maestrias'));
    }

    public function show($id)
    {
        // Buscar la cohorte y obtener datos de tasa de titulación
        $cohorte = Cohorte::findOrFail($id);

        $tasaTitulaciones = TasaTitulacion::where('cohorte_id', $cohorte->id)->first();

        return response()->json($tasaTitulaciones);
    }

    public function getCohortes($id)
    {
        $cohortes = Cohorte::where('maestria_id', $id)->get();
        return response()->json($cohortes);
    }

    public function export($maestria_id, $cohorte_id)
    {
        $maestria = Maestria::findOrFail($maestria_id);
        $cohorte = Cohorte::findOrFail($cohorte_id);

        
        $alumnos = Alumno::whereHas('matriculas', function ($query) use ($cohorte_id) {
            $query->where('cohorte_id', $cohorte_id);
        })
            ->where('maestria_id', $maestria_id)
            ->distinct('dni') 
            ->get();

        return Excel::download(new EstudiantesExport($alumnos, $maestria, $cohorte), 'Estudiantes_SIIES.xlsx');
    }
    public function export2($maestria_id, $cohorte_id)
    {
        $maestria = Maestria::findOrFail($maestria_id);
        $cohorte = Cohorte::findOrFail($cohorte_id);

        $alumnos = Alumno::whereHas('matriculas', function ($query) use ($cohorte_id) {
            $query->where('cohorte_id', $cohorte_id);
        })
        ->where('maestria_id', $maestria_id)
        ->whereHas('titulaciones') // Filtrar solo alumnos con titulaciones
        ->distinct('dni') 
        ->get();    

        return Excel::download(new TitulacionExport($alumnos, $maestria, $cohorte), 'Graduados_SIIES.xlsx');
    }
}
