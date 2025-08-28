<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Secretario;
use App\Models\Alumno;
use App\Models\Cohorte;
use App\Models\Maestria;
use App\Models\Docente;
use App\Models\User;
use App\Models\Postulante;

class DashboardSecretarioController extends Controller
{   public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $user = auth()->user();

        // Buscar al secretario autenticado
        $secretario = Secretario::where('nombre1', $user->name)
            ->where('apellidop', $user->apellido)
            ->where('email', $user->email)
            ->firstOrFail();

        // Obtener las maestrías de la sección del secretario
        $maestrias = $secretario->seccion->maestrias;
        $maestriaIds = $maestrias->pluck('id'); // IDs de esas maestrías

        // Postulantes por maestría
        $postulantesPorMaestria = [];
        foreach ($maestrias as $maestria) {
            $cantidadPostulantes = Postulante::where('maestria_id', $maestria->id)->count();
            $postulantesPorMaestria[] = [
                'maestria' => $maestria->nombre,
                'cantidad_postulantes' => $cantidadPostulantes,
            ];
        }

        // Paginación
        $perPage = $request->input('perPage', 10);

        // 🔑 Alumnos que tengan maestrías dentro de las maestrías del secretario
        $alumnos = Alumno::whereHas('maestrias', function ($q) use ($maestriaIds) {
            $q->whereIn('maestrias.id', $maestriaIds);
        })->with('maestrias')->get();

        // Gráfico de matriculados por maestría (solo las del secretario)
        $matriculadosPorMaestria = Maestria::withCount('alumnos')
            ->whereIn('id', $maestriaIds)
            ->get();

        // Totales
        $totalDocentes = Docente::count();
        $totalPostulantes = Postulante::count();
        $totalAlumnos = $alumnos->count(); // solo alumnos de las maestrías del secretario

        return view('dashboard.secretario', compact(
            'alumnos',
            'matriculadosPorMaestria',
            'totalAlumnos',
            'perPage',
            'totalDocentes',
            'totalPostulantes',
            'maestrias',
            'postulantesPorMaestria'
        ));
    }

}
