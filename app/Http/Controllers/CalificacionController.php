<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nota;
use App\Models\Matricula;
use App\Models\Docente;
use App\Models\CalificacionVerificacion;
use App\Models\Cohorte;
use App\Models\User;
use App\Models\Alumno;
use Spatie\Permission\Models\Role;

class CalificacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($docente_dni, $asignatura_id, $cohorte_id)
    {
        $cohorte = Cohorte::findOrFail($cohorte_id);
        $aforoMaximo = $cohorte->aforo;
        // Obtener matrículas
        $matriculas = Matricula::where([
            'docente_dni' => $docente_dni,
            'asignatura_id' => $asignatura_id,
            'cohorte_id' => $cohorte_id
        ])->with('alumno')->get();

        $alumnos = $matriculas->pluck('alumno');

        $notas = Nota::whereIn('alumno_dni', $alumnos->pluck('dni'))
            ->where([
                'docente_dni' => $docente_dni,
                'asignatura_id' => $asignatura_id,
                'cohorte_id' => $cohorte_id
            ])->get()
            ->keyBy('alumno_dni');

        return view('calificaciones.create', compact('alumnos', 'aforoMaximo', 'notas', 'docente_dni', 'asignatura_id', 'cohorte_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'docente_dni' => 'required|string',
            'asignatura_id' => 'required|integer',
            'cohorte_id' => 'required|integer',
            'alumno_dni' => 'nullable|array',
            'nota_actividades' => 'nullable|array',
            'nota_practicas' => 'nullable|array',
            'nota_autonomo' => 'nullable|array',
            'examen_final' => 'nullable|array',
            'recuperacion' => 'nullable|array',
            'total' => 'nullable|array',
            'nuevo_alumno.dni' => 'nullable|array',
            'nuevo_alumno.nombre1' => 'nullable|array',
            'nuevo_alumno.nombre2' => 'nullable|array',
            'nuevo_alumno.apellidop' => 'nullable|array',
            'nuevo_alumno.apellidom' => 'nullable|array',
        ]);

        $docenteDni = $request->input('docente_dni');
        $asignaturaId = $request->input('asignatura_id');
        $cohorteId = $request->input('cohorte_id');
        $cohorte = Cohorte::findOrFail($cohorteId);
        $alumnoDnis = $request->input('alumno_dni', []);
        $notas = $request->only(['nota_actividades', 'nota_practicas', 'nota_autonomo', 'examen_final', 'recuperacion', 'total']);
        $nuevosAlumnos = $request->input('nuevo_alumno', []);
        $nuevosAlumnos = array_map(function ($field) {
            return array_filter($field, fn($value) => !is_null($value));
        }, $nuevosAlumnos);
        $calificacionVerificacion = CalificacionVerificacion::where([
            'docente_dni' => $docenteDni,
            'asignatura_id' => $asignaturaId,
            'cohorte_id' => $cohorteId
        ])->first();

        if ($calificacionVerificacion) {
            $calificacionVerificacion->calificado = 1;
            $calificacionVerificacion->editar = 0;
            $calificacionVerificacion->save();
        }

        // Actualización o creación de notas para alumnos existentes
        foreach ($alumnoDnis as $alumnoDni) {
            $updateData = array_filter([
                'nota_actividades' => $notas['nota_actividades'][$alumnoDni] ?? null,
                'nota_practicas' => $notas['nota_practicas'][$alumnoDni] ?? null,
                'nota_autonomo' => $notas['nota_autonomo'][$alumnoDni] ?? null,
                'examen_final' => $notas['examen_final'][$alumnoDni] ?? null,
                'recuperacion' => $notas['recuperacion'][$alumnoDni] ?? null,
                'total' => $notas['total'][$alumnoDni] ?? null,
            ]);

            Nota::updateOrCreate(
                [
                    'docente_dni' => $docenteDni,
                    'alumno_dni' => $alumnoDni,
                    'asignatura_id' => $asignaturaId,
                    'cohorte_id' => $cohorteId,
                ],
                $updateData
            );
        }

        // Manejo de nuevos alumnos
        foreach ($nuevosAlumnos['dni'] as $index => $dni) {
            $nombre1 = $nuevosAlumnos['nombre1'][$index];
            $apellidop = $nuevosAlumnos['apellidop'][$index];
            $ultimo4Dni = substr($dni, -4);
            $email_institucional = strtolower($nombre1 . '-' . $apellidop . $ultimo4Dni) . '@unesum.edu.ec';

            // Primero intenta obtener el alumno si existe o crea uno nuevo
            $alumno = Alumno::firstOrCreate(
                ['dni' => $dni],
                [
                    'nombre1' => $nombre1,
                    'nombre2' => $nuevosAlumnos['nombre2'][$index] ?? null,
                    'apellidop' => $apellidop,
                    'apellidom' => $nuevosAlumnos['apellidom'][$index] ?? null,
                    'email_institucional' => $email_institucional,
                    'monto_total' => $cohorte->maestria->arancel,
                    'maestria_id' => $cohorte->maestria->id,
                    'contra' => bcrypt($dni),
                ]
            );

            // Si el registro fue creado recientemente, realiza otras acciones
            if ($alumno->wasRecentlyCreated) {
                // Crea el usuario asociado al alumno
                $usuario = User::firstOrCreate(
                    ['email' => $email_institucional],
                    [
                        'name' => $nombre1,
                        'apellido' => $apellidop,
                        'password' => bcrypt($dni),
                        'status' => 'ACTIVO',
                    ]
                );

                if ($usuario->wasRecentlyCreated) {
                    $alumnoRole = Role::findById(4);
                    $usuario->assignRole($alumnoRole);

                    // Actualiza o crea la matrícula
                    Matricula::updateOrCreate(
                        [
                            'alumno_dni' => $dni,
                            'asignatura_id' => $asignaturaId,
                            'cohorte_id' => $cohorteId,
                            'docente_dni' => $docenteDni,
                        ]
                    );

                    $cohorte->aforo -= 1;
                    $cohorte->save();
                }

                $usuario->save();
            }

            // Actualiza o crea la nota
            Nota::updateOrCreate(
                [
                    'docente_dni' => $docenteDni,
                    'alumno_dni' => $dni,
                    'asignatura_id' => $asignaturaId,
                    'cohorte_id' => $cohorteId,
                ],
                array_filter([
                    'nota_actividades' => $request->input("nuevo_nota_actividades.$index"),
                    'nota_practicas' => $request->input("nuevo_nota_practicas.$index"),
                    'nota_autonomo' => $request->input("nuevo_nota_autonomo.$index"),
                    'examen_final' => $request->input("nuevo_examen_final.$index"),
                    'recuperacion' => $request->input("nuevo_recuperacion.$index"),
                    'total' => $request->input("nuevo_total.$index"),
                ])
            );
        }


        return redirect()->route('dashboard_docente')->with('success', 'Calificaciones almacenadas exitosamente');
    }

    public function edit($alumno_dni, $docente_dni, $asignatura_id, $cohorte_id)
    {
        $calificacionVerificacion = CalificacionVerificacion::where([
            'docente_dni' => $docente_dni,
            'asignatura_id' => $asignatura_id,
            'cohorte_id' => $cohorte_id
        ])->first();

        $tienePermisoEditar = $calificacionVerificacion ? $calificacionVerificacion->editar : false;

        $nota = Nota::where([
            'cohorte_id' => $cohorte_id,
            'asignatura_id' => $asignatura_id,
            'docente_dni' => $docente_dni,
            'alumno_dni' => $alumno_dni
        ])->firstOrFail();

        return view('calificaciones.edit', compact('nota', 'tienePermisoEditar'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nota_actividades' => 'nullable|numeric',
            'nota_practicas' => 'nullable|numeric',
            'nota_autonomo' => 'nullable|numeric',
            'examen_final' => 'nullable|numeric',
            'recuperacion' => 'nullable|numeric',
            'total' => 'nullable|numeric',
        ]);

        $nota = Nota::findOrFail($id);
        $nota->update($request->only('nota_actividades', 'nota_practicas', 'nota_autonomo', 'examen_final', 'recuperacion', 'total'));

        return redirect()->route('calificaciones.show1', [
            $nota->alumno_dni,
            $nota->docente_dni,
            $nota->asignatura_id,
            $nota->cohorte_id
        ])->with('success', 'La nota ha sido actualizada exitosamente.');
    }

    public function show($alumno_dni, $docente_dni, $asignatura_id, $cohorte_id)
    {
        $calificacionVerificacion = CalificacionVerificacion::where([
            'docente_dni' => $docente_dni,
            'asignatura_id' => $asignatura_id,
            'cohorte_id' => $cohorte_id
        ])->first();

        $tienePermisoVerNotas = $calificacionVerificacion ? $calificacionVerificacion->editar : false;

        $notas = Nota::where([
            'cohorte_id' => $cohorte_id,
            'asignatura_id' => $asignatura_id,
            'docente_dni' => $docente_dni,
            'alumno_dni' => $alumno_dni
        ])->get();

        $cohorte = Cohorte::with('periodo_academico')->find($cohorte_id);
        $fechaLimite = $cohorte->periodo_academico->fecha_fin->addWeek();

        return view('calificaciones.show', compact('notas', 'fechaLimite', 'tienePermisoVerNotas'));
    }
}
