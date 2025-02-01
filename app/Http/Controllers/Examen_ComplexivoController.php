<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Docente;
use App\Models\ExamenComplexivo;
use App\Notifications\ExamenComplexivoAsignado;
use App\Models\Cohorte;
use App\Models\Matricula;
use App\Models\Secretario;
use App\Models\TasaTitulacion;
use App\Models\Titulacion;
use App\Models\User;
use Illuminate\Support\Carbon;

use Illuminate\Http\Request;

class Examen_ComplexivoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $docente = Docente::where('email', $user->email)->first();

        if (!$docente || !$docente->maestria()->exists()) {
            return $request->ajax()
                ? response()->json(['error' => 'No estás asignado a ninguna maestría.'], 403)
                : redirect()->back()->withErrors(['error' => 'No estás asignado a ninguna maestría.']);
        }

        $maestria = $docente->maestria()->first();

        $cohortes = $maestria->cohortes;

        return view('titulacion.examen_complexivo', compact('cohortes'));
    }
    public function store(Request $request)
    {
        $cohorteId = $request->input('cohorte_id');
        $cohorte = Cohorte::where('id', $cohorteId)->first();

        if (!$cohorte) {
            $message = 'Cohorte no encontrado.';
            if ($request->ajax()) {
                return response()->json(['error' => $message], 404);
            } else {
                return redirect()->back()->withErrors(['error' => $message]);
            }
        }

        $alumnos = Matricula::where('cohorte_id', $cohorteId)
            ->with('alumno')
            ->get()
            ->filter(function ($matricula) {
                $tesis = $matricula->alumno->tesis->first();
                return $tesis && $tesis->tipo == 'examen complexivo';
            })
            ->unique(function ($matricula) {
                return $matricula->alumno->dni;
            });


        if ($alumnos->isEmpty()) {
            $message = 'Ningún alumno tiene tesis de tipo examen complexivo en este cohorte.';
            if ($request->ajax()) {
                return response()->json(['error' => $message], 404);
            } else {
                return redirect()->back()->withErrors(['error' => $message]);
            }
        }

        foreach ($alumnos as $matricula) {
            $alumno = $matricula->alumno;
            $tesis = $alumno->tesis->first();

            if ($tesis && $tesis->tipo == 'examen complexivo') {
                $examen = ExamenComplexivo::updateOrCreate(
                    ['alumno_dni' => $alumno->dni],
                    [
                        'lugar' => $request->input('lugar'),
                        'fecha_hora' => $request->input('fecha_hora'),
                        'alumno_dni' => $alumno->dni,
                    ]
                );

                $user = User::where('email', $alumno->email_institucional)->first();
                if ($user) {
                    $fecha = Carbon::parse($request->input('fecha_hora'))->format('d/m/Y');
                    $hora = Carbon::parse($request->input('fecha_hora'))->format('H:i');
                    $user->notify(new ExamenComplexivoAsignado($fecha, $request->input('lugar'), $hora));
                }
            }
        }

        // Confirmación después de realizar la asignación del examen
        $message = 'Examen complexivo asignado a los alumnos correctamente.';
        if ($request->ajax()) {
            return response()->json(['success' => $message]);
        } else {
            return redirect()->back()->with('success', $message);
        }
    }

    public function calificar_examen(Request $request)
    {
        // Verificar si la solicitud es AJAX para DataTables
        if ($request->ajax()) {
            $user = auth()->user();

            // Filtrar los alumnos según el rol del usuario
            if ($user->hasRole('Administrador')) {
                $query = Alumno::with(['maestria', 'tesis'])->whereHas('tesis', function ($query) {
                    $query->where('tipo', 'examen complexivo'); // Filtrar por tipo de tesis
                });
            } else {
                $secretario = Secretario::where('nombre1', $user->name)
                    ->where('apellidop', $user->apellido)
                    ->where('email', $user->email)
                    ->firstOrFail();
                $maestriasIds = $secretario->seccion->maestrias->pluck('id');
                $query = Alumno::with(['maestria', 'tesis'])
                    ->whereIn('maestria_id', $maestriasIds)
                    ->whereHas('tesis', function ($query) {
                        $query->where('tipo', 'examen complexivo');
                    });
            }

            // Configurar DataTables con las columnas necesarias
            return datatables()->eloquent($query)
                ->addColumn('maestria_nombre', function ($alumno) {
                    return $alumno->maestria ? $alumno->maestria->nombre : 'Sin Maestría';
                })
                ->addColumn('foto', function ($alumno) {
                    return '<img src="' . asset('storage/' . $alumno->image) . '" alt="Foto de ' . $alumno->nombre1 . '" class="img-thumbnail rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">';
                })
                ->addColumn('nombre_completo', function ($alumno) {
                    return "{$alumno->nombre1}<br>{$alumno->nombre2}<br>{$alumno->apellidop}<br>{$alumno->apellidom}";
                })
                ->addColumn('tipo_tesis', function ($alumno) {
                    return $alumno->tesis->first() ? $alumno->tesis->first()->tipo : 'Sin Tesis';
                })
                ->addColumn('acciones', function ($alumno) {
                    $acciones = '<div style="display: flex; gap: 10px; align-items: center;">';
                    
                    // Validar si nota no es null o 0
                    if ($alumno->titulaciones->first() && $alumno->titulaciones->first()->nota != null || $alumno->titulaciones->first()->nota != 0) {
                        $acciones .= '<button type="button" class="btn btn-outline-success btn-sm d-flex align-items-center gap-2" 
                                      data-bs-toggle="modal" 
                                      data-bs-target="#modalCalificarExamen" 
                                      data-dni="' . $alumno->dni . '" 
                                      data-nombre="' . $alumno->nombre1 . ' ' . $alumno->apellidop . '">
                                      <i class="bi bi-pencil-square"></i> Calificar
                                  </button>';
                    }else {
                        // Mostrar label si ya está calificado
                        $acciones .= '<span class="badge bg-secondary d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle"></i> Ya calificado
                                      </span>';
                    }                
                
                    $acciones .= '</div>';
                    return $acciones;
                })                
                ->rawColumns(['foto', 'acciones', 'nombre_completo']) // Permitir HTML en estas columnas
                ->toJson();
        }

        // Retornar la vista si no es una solicitud AJAX
        return view('alumnos.examen_complexivo');
    }

    public function actualizarNotaYFechaGraduacion(Request $request)
    {
        $request->validate([
            'alumno_dni' => 'required',
            'nota' => 'required|numeric|min:0|max:10',
            'fecha_graduacion' => 'required|date',
        ]);

        // Actualizar la nota en el examen complexivo
        $examenComplexivo = ExamenComplexivo::where('alumno_dni', $request->alumno_dni)->first();
        if ($examenComplexivo) {
            $examenComplexivo->nota = $request->nota;
            $examenComplexivo->save();
        } else {
            return response()->json(['error' => 'Examen Complexivo no encontrado.'], 404);
        }

        // Registrar la fecha de graduación en titulaciones
        $titulacion = Titulacion::firstOrCreate(
            ['alumno_dni' => $request->alumno_dni],
            ['titulado' => false]
        );

        $titulacion->fecha_graduacion = $request->fecha_graduacion;
        $titulacion->save();
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

        return redirect()->back()->with('success', 'Datos actualizados correctamente.');
    }

}
