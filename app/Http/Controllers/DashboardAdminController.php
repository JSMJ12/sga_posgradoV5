<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Maestria;
use App\Models\Secretario;
use App\Models\Docente;
use App\Models\User;
use App\Models\Postulante;

class DashboardAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $maestrias = Maestria::withCount(['postulantes', 'alumnos'])->get();

        $postulantesPorMaestria = $maestrias->map(function ($maestria) {
            return [
                'maestria' => $maestria->nombre,
                'cantidad_postulantes' => $maestria->postulantes_count,
            ];
        });

        $alumnos = Alumno::with('maestrias')->get();

        $totales = [
            'totalMaestrias'   => $maestrias->count(),
            'totalDocentes'    => Docente::count(),
            'totalSecretarios' => Secretario::count(),
            'totalUsuarios'    => User::count(),
            'totalPostulantes' => Postulante::count(),
            'totalAlumnos'     => Alumno::count(),
        ];

        return view('dashboard.administrador', [
            'alumnos'               => $alumnos,
            'matriculadosPorMaestria' => $maestrias,
            'postulantesPorMaestria'  => $postulantesPorMaestria,
            'perPage'               => $perPage,
        ] + $totales); 
    }
}
