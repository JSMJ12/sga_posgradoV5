<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\TasaTitulacion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TitulacionAlumnoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Request $request)
    {
        $alumnoDni = $request->input('alumno_dni');

        $alumno = Alumno::where('dni', $alumnoDni)->first();

        $tesis = $alumno->tesis->first();
        if ($tesis) {
            $tesis->estado = 'titulado';
            $tesis->save(); 
        }

        return redirect()->back()->with('success', 'Alumno titulado correctamente');
    }
}
