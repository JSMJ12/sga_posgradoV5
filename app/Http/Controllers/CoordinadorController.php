<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Maestria;
use App\Models\Secretario;
use App\Models\Docente;
use App\Models\Postulante;
use App\Models\User;

class CoordinadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $docente = Docente::where('email', $user->email)->first();

        if (!$docente || $docente->maestria()->count() === 0) {
            return redirect()->route('dashboard')->with('error', 'No estás asignado a ninguna maestría.');
        }

        $maestria = $docente->maestria->first();

        $alumnos = Alumno::where('maestria_id', $maestria->id)->get();
        $cantidadPostulantes = Postulante::where('maestria_id', $maestria->id)->count();
        $matriculadosPorMaestria = Maestria::withCount('alumnos')->get();
        $asignaturas = $maestria->asignaturas;

        $totalAlumnos = $alumnos->count();
        $totalPostulantes = $cantidadPostulantes;
        $totalDocentes = Docente::whereHas('asignaturas', function ($query) use ($maestria) {
            $query->whereHas('maestria', function ($query) use ($maestria) {
                $query->where('id', $maestria->id);
            });
        })->count();

        return view('dashboard.coordinador', compact(
            'alumnos',
            'matriculadosPorMaestria',
            'totalAlumnos',
            'totalPostulantes',
            'maestria',
            'totalDocentes',
            'asignaturas'
        ));
    }
}
