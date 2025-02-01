<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;
use App\Models\Asignatura;
use App\Models\Nota;
use App\Models\Docente;
use App\Models\Matricula;

class DashboardAlumnoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $user = auth()->user();

        // Buscar el alumno basado en nombre1, apellidop, y email_institucional del usuario autenticado
        $alumno = Alumno::where('nombre1', $user->name)
            ->where('apellidop', $user->apellido)
            ->where('email_institucional', $user->email)
            ->firstOrFail();

        // Eager load de relaciones necesarias
        $alumno->load([
            'matriculas.asignatura.cohortes.aula',
            'matriculas.asignatura.notas' => function ($query) use ($alumno) {
                $query->where('alumno_dni', $alumno->dni);
            }
        ]);

        $asignaturas = $alumno->matriculas->map->asignatura;

        // Retornar la vista con los datos
        return view('dashboard.alumno', compact('asignaturas', 'alumno'));
    }
    public function alumnos_notas(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $user = auth()->user();

        // Buscar al alumno por su nombre, apellido y email
        $alumno = Alumno::where('nombre1', $user->name)
            ->where('apellidop', $user->apellido)
            ->where('email_institucional', $user->email)
            ->firstOrFail();

        // Obtener las matriculas del alumno
        $matriculas = Matricula::where('alumno_dni', $alumno->dni)->get();

        // Procesar los datos para obtener el array con la información requerida
        $notasData = $matriculas->mapWithKeys(function ($matricula) use ($alumno) {
            // Obtener la asignatura de la matrícula
            $asignatura = $matricula->asignatura;

            // Obtener el docente asociado a la asignatura de la matrícula
            $docente = $matricula->docente;

            // Obtener las notas relacionadas con la asignatura, el alumno y el docente
            $nota = Nota::where('alumno_dni', $alumno->dni)
                ->where('asignatura_id', $asignatura->id)
                ->where('docente_dni', $docente->dni)
                ->first();

            return [
                $asignatura->nombre => [
                    'docente_nombre' => $docente ? $docente->nombre1 . ' ' . $docente->nombre2 . ' ' . $docente->apellidop . ' ' . $docente->apellidom : 'N/A',
                    'docente_image' => $docente ? $docente->image : 'default_image_path.jpg',
                    'nota_actividades' => $nota ? $nota->nota_actividades : 'N/A',
                    'nota_practicas' => $nota ? $nota->nota_practicas : 'N/A',
                    'nota_autonomo' => $nota ? $nota->nota_autonomo : 'N/A',
                    'examen_final' => $nota ? $nota->examen_final : 'N/A',
                    'recuperacion' => $nota ? $nota->recuperacion : 'N/A',
                    'total' => $nota ? $nota->total : 'N/A',
                ]
            ];
        });

        // Retornar la vista con los datos
        return view('notas.alumnos', compact('notasData', 'perPage', 'alumno'));
    }
}
