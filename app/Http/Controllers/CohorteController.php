<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cohorte;
use App\Models\Maestria;
use App\Models\Secretario;
use App\Models\PeriodoAcademico;
use App\Models\Aula;
use App\Models\Docente;
use App\Exports\CohorteAlumnosExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Nota;
use App\Models\CalificacionVerificacion;
use App\Models\User;
use Illuminate\Validation\Rule;

class CohorteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('perPage', 10);

        if ($user->hasRole('Administrador')) {
            // Si el usuario es administrador, muestra todos los cohortes
            $cohortes = Cohorte::with(['maestria', 'periodo_academico', 'aula']);
        } elseif ($user->hasRole('Coordinador')) {
            // Si el usuario es coordinador, busca la maestría asociada al docente
            $docente = Docente::where('email', $user->email)->first();

            if (!$docente || !$docente->maestria()->exists()) {
                return $request->ajax()
                    ? response()->json(['error' => 'No estás asignado a ninguna maestría.'], 403)
                    : redirect()->back()->withErrors(['error' => 'No estás asignado a ninguna maestría.']);
            }

            $maestria = $docente->maestria()->first();
            $maestriaId = $maestria->id;

            // Filtra los cohortes de la maestría del coordinador
            $cohortes = Cohorte::with(['maestria', 'periodo_academico', 'aula'])
                ->where('maestria_id', $maestriaId);
        } else {
            // Si el usuario no es administrador ni coordinador, asume que es un secretario
            $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();

            // Obtén los identificadores de las maestrías asociadas a la sección del secretario
            $maestriasIds = $secretario->seccion->maestrias->pluck('id');

            // Filtra los cohortes que pertenecen a esas maestrías
            $cohortes = Cohorte::with(['maestria', 'periodo_academico', 'aula'])
                ->whereIn('maestria_id', $maestriasIds);
        }

        if ($request->ajax()) {
            return datatables()->eloquent($cohortes)
                ->addColumn('aula_nombre', function ($cohorte) {
                    return $cohorte->aula && $cohorte->aula->nombre ? $cohorte->aula->nombre : 'No asignada';
                })
                ->addColumn('alumnos', function ($cohorte) {
                    return '
                    <a href="' . route('cohortes.exportarAlumnos', $cohorte->id) . '" class="btn btn-success btn-sm" title="Listado de alumnos">
                        <i class="fas fa-file-excel"></i>
                    </a>';
                })
                ->addColumn('acciones', function ($cohorte) {
                    return '
                    <a href="' . route('cohortes.edit', $cohorte->id) . '" class="btn btn-outline-primary btn-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="' . route('cohortes.destroy', $cohorte->id) . '" method="POST" style="display: inline-block;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este cohorte?\');">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>';
                })
                ->addColumn('verificaciones', function ($cohorte) {
                    return '
                            <div class="d-flex gap-1">
                                <button class="btn btn-info btn-sm btn-verificaciones" data-cohorte-id="' . $cohorte->id . '" data-toggle="modal" data-target="#verificacionModal" title="Verificación">
                                    <i class="fas fa-clipboard-check"></i>
                                </button>
                                <button type="button" class="btn btn-primary btn-sm btn-proceso-titulacion" data-toggle="modal" data-target="#procesoTitulacionModal" data-id="' . $cohorte->id . '" title="Titulación">
                                    <i class="fas fa-graduation-cap"></i>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm btn-examen-complexivo" data-toggle="modal" data-target="#examenComplexivoModal" data-id="' . $cohorte->id . '" title="Examen Complexivo">
                                    <i class="fas fa-book"></i>
                                </button>
                            </div>
                        ';
                })


                ->rawColumns(['acciones', 'alumnos', 'verificaciones'])
                ->toJson();
        }

        return view('cohortes.index', compact('perPage'));
    }

    public function verificaciones($cohorteId)
    {
        // Obtener cohorte con su maestría
        $cohorte = Cohorte::with('maestria.asignaturas')->findOrFail($cohorteId);

        // Obtener todas las asignaturas de la maestría
        $asignaturas = $cohorte->maestria->asignaturas;

        // Obtener verificaciones ya registradas
        $verificaciones = CalificacionVerificacion::with(['docente', 'asignatura'])
            ->where('cohorte_id', $cohorteId)
            ->get();

        // Armar la respuesta combinando asignaturas y verificaciones
        $resultado = $asignaturas->map(function ($asignatura) use ($verificaciones, $cohorteId) {
            $verificacion = $verificaciones->firstWhere('asignatura_id', $asignatura->id);

            $docente = $verificacion?->docente;

            // Verificar si existen notas

            $notasExisten = false;
            $pdfNotasUrl = null;

            if ($docente) {
                $notasExisten = Nota::where([
                    'docente_dni' => $docente->dni,
                    'asignatura_id' => $asignatura->id,
                    'cohorte_id' => $cohorteId,
                ])->exists();

                if ($notasExisten) {
                    $pdfNotasUrl = route('pdf.notas.asignatura', [
                        'docenteId' => $docente->dni,
                        'asignaturaId' => $asignatura->id,
                        'cohorteId' => $cohorteId,
                        'aulaId' => $verificacion->aula_id ?? null,
                        'paraleloId' => $verificacion->paralelo_id ?? null,
                    ]);
                }
            }

            return [
                'asignatura' => $asignatura->nombre,
                'docente' => $docente?->getFullNameAttribute() ?? 'Sin docente',
                'calificado' => $verificacion?->calificado ?? false,
                'notas_existen' => $notasExisten,
                'pdf_notas_url' => $pdfNotasUrl,
            ];
        });

        return response()->json($resultado);
    }


    public function create()
    {
        $user = auth()->user();

        if ($user->hasRole('Secretario')) {
            $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();
            //
            $maestriasIds = $secretario->seccion->maestrias->pluck('id');
            $maestrias = Maestria::whereIn('id', $maestriasIds)
                ->where('status', 'ACTIVO')
                ->get();
        } elseif ($user->hasRole('Coordinador')) {
            // Si el usuario es coordinador, busca la maestría asociada al docente
            $docente = Docente::where('email', $user->email)->first();

            if (!$docente || !$docente->maestria()->exists()) {
                return $request->ajax()
                    ? response()->json(['error' => 'No estás asignado a ninguna maestría.'], 403)
                    : redirect()->back()->withErrors(['error' => 'No estás asignado a ninguna maestría.']);
            }

            $maestria = $docente->maestria()->first();
            $maestrias = Maestria::where('id', $maestria->id)
                ->where('status', 'ACTIVO')
                ->get();
        } else {
            $maestrias = Maestria::where('status', 'ACTIVO')->get();
        }
        $periodos_academicos = PeriodoAcademico::all();
        $aulas = Aula::all();

        return view('cohortes.create', compact('maestrias', 'periodos_academicos', 'aulas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                Rule::unique('cohortes')->where(function ($query) use ($request) {
                    return $query->where('maestria_id', $request->maestria_id);
                }),
            ],
            'maestria_id' => 'required|exists:maestrias,id',
            'periodo_academico_id' => 'required|exists:periodos_academicos,id',
            'aula_id' => 'nullable',
            'aforo' => 'required|integer',
            'modalidad' => 'required|in:presencial,hibrida,virtual',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ], [
            'nombre.required' => 'El nombre de la cohorte es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.unique' => 'Ya existe una cohorte con ese nombre en la maestría seleccionada.',
            'maestria_id.required' => 'Debe seleccionar una maestría.',
            'maestria_id.exists' => 'La maestría seleccionada no es válida.',
            'periodo_academico_id.required' => 'Debe seleccionar un periodo académico.',
            'periodo_academico_id.exists' => 'El periodo académico no es válido.',
            'aforo.required' => 'El aforo es obligatorio.',
            'aforo.integer' => 'El aforo debe ser un número entero.',
            'modalidad.required' => 'La modalidad es obligatoria.',
            'modalidad.in' => 'La modalidad seleccionada no es válida.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio no es válida.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin no es válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
        ]);


        Cohorte::create($request->only([
            'nombre',
            'maestria_id',
            'periodo_academico_id',
            'aula_id',
            'aforo',
            'modalidad',
            'fecha_inicio',
            'fecha_fin',
        ]));

        return redirect()->route('cohortes.index')->with('success', 'La cohorte ha sido creada exitosamente.');
    }

    public function edit($cohorte)
    {
        $cohorte = Cohorte::where('id', $cohorte)->firstOrFail();
        $maestrias = Maestria::all();
        $periodos_academicos = PeriodoAcademico::all();
        $aulas = Aula::all();

        return view('cohortes.edit', compact('cohorte', 'maestrias', 'periodos_academicos', 'aulas'));
    }

    public function update(Request $request, $cohorte)
    {
        $request->validate([
            'nombre' => 'required|string',
            'maestria_id' => 'required|exists:maestrias,id',
            'periodo_academico_id' => 'required|exists:periodos_academicos,id',
            'aula_id' => 'nullable|exists:aulas,id',
            'aforo' => 'required|integer',
            'modalidad' => 'required|in:presencial,hibrida,virtual',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $cohorte = Cohorte::findOrFail($cohorte);

        $cohorte->update([
            'nombre' => $request->input('nombre'),
            'maestria_id' => $request->input('maestria_id'),
            'periodo_academico_id' => $request->input('periodo_academico_id'),
            'aula_id' => $request->input('aula_id'),
            'aforo' => $request->input('aforo'),
            'modalidad' => $request->input('modalidad'),
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin' => $request->input('fecha_fin'),
        ]);


        return redirect()->route('cohortes.index')->with('success', 'La cohorte ha sido actualizada exitosamente.');
    }


    public function destroy($cohorte)
    {
        $cohorte = Cohorte::where('id', $cohorte)->firstOrFail();
        try {
            $cohorte->delete();
            return redirect()->route('cohortes.index')->with('success', 'El cohorte ha sido eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('cohortes.index')->with('error', 'Error al eliminar el cohorte: ' . $e->getMessage());
        }
    }
    public function exportarAlumnos($cohorte_id)
    {
        // Obtener la cohorte con su relación a la maestría
        $cohorte = Cohorte::with('maestria')->findOrFail($cohorte_id);

        // Obtener el nombre de la maestría y la cohorte
        $maestriaNombre = str_replace(' ', '_', $cohorte->maestria->nombre);
        $cohorteNombre = str_replace(' ', '_', $cohorte->nombre);

        // Generar el nombre del archivo con la maestría y la cohorte
        $fileName = "Alumnos_{$maestriaNombre}_{$cohorteNombre}_" . now()->format('Y-m-d') . ".xlsx";

        return Excel::download(new CohorteAlumnosExport($cohorte_id), $fileName);
    }
    public function proceso_titulacion($id)
    {
        $cohorte = Cohorte::with([
            'matriculas.alumno.tesis.tutorias',
            'matriculas.alumno.tesis.tutor',
            'matriculas.alumno.titulaciones'
        ])->findOrFail($id);

        $alumnosUnicos = $cohorte->matriculas
            ->pluck('alumno')
            ->unique('dni')
            ->filter(function ($alumno) {
                return $alumno->examenComplexivo === null;
            })
            ->values();

        $alumnosProcesados = $alumnosUnicos->map(function ($alumno) {
            $primerTesis = $alumno->tesis->first();
            $tutorias = $primerTesis ? $primerTesis->tutorias : collect();
            $tutor = $primerTesis?->tutor;

            // Buscar usuario por email si existe
            $tutorUserId = null;
            if ($tutor && $tutor->email) {
                $user = User::where('email', $tutor->email)->first();
                $tutorUserId = $user?->id;
            }

            return [
                'alumno' => [
                    'dni' => $alumno->dni,
                    'full_name' => $alumno->full_name,
                ],
                'estado_tesis' => optional($primerTesis)->estado ?? 'sin tesis',
                'tiene_tesis' => (bool) $primerTesis,
                'tutorias' => $tutorias->map(function ($tutoria) {
                    return [
                        'estado' => $tutoria->estado,
                        'fecha' => $tutoria->fecha ?? null,
                    ];
                })->values(),
                'tutorias_completadas' => $tutorias->where('estado', 'realizada')->count(),
                'tutor' => $tutor?->full_name,
                'tutor_user_id' => $tutorUserId,
                'graduado' => optional($alumno->titulaciones->first())->fecha_graduacion ?? null,
            ];
        });

        return response()->json($alumnosProcesados);
    }
    public function examenComplexivo($id)
    {
        $cohorte = Cohorte::with([
            'matriculas.alumno.examenComplexivo'
        ])->findOrFail($id);

        $alumnosUnicos = $cohorte->matriculas
            ->pluck('alumno')
            ->unique('dni')
            ->values();

        // Filtrar solo los alumnos que tienen examen complexivo registrado
        $alumnosConExamen = $alumnosUnicos->filter(function ($alumno) {
            return $alumno->examenComplexivo !== null;
        });

        $datos = $alumnosConExamen->map(function ($alumno) {
            $examen = $alumno->examenComplexivo;

            return [
                'alumno' => $alumno->full_name,
                'lugar' => $examen->lugar ?? 'No registrado',
                'fecha_hora' => $examen->fecha_hora ?? 'No programada',
                'nota' => $examen->nota !== null ? $examen->nota : 'No asignada aún',
            ];
        });

        return response()->json($datos->values());
    }
}
