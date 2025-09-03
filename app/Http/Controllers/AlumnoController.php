<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Maestria;
use App\Models\Retiro;
use App\Models\Docente;
use App\Models\Secretario;
use App\Models\TasaTitulacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AlumnoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function subirFichaSocioeconomica(Request $request, $id)
    {
        try {
            $request->validate([
                'ficha_socioeconomica' => 'required|mimes:pdf,doc,docx|max:2048', // máximo 2MB
            ]);

            $alumno = Alumno::findOrFail($id);

            $file = $request->file('ficha_socioeconomica');
            $extension = $file->getClientOriginalExtension();
            $filename = 'ficha_' . Str::slug($alumno->nombre1 . '_' . $alumno->apellidop) . '_' . time() . '.' . $extension;

            $ruta = $file->storeAs('private/ficha_socioeconomica', $filename);

            $alumno->ficha_socioeconomica = $ruta;
            $alumno->save();

            return back()->with('success', 'Ficha socioeconómica subida correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al subir la ficha socioeconómica: ' . $e->getMessage());

            return back()->with('error', 'Ocurrió un error al subir la ficha socioeconómica. Por favor, inténtalo nuevamente.');
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $query = Alumno::query();

            $maestriasPermitidas = collect();

            // Filtrar alumnos según rol
            if ($user->hasRole('Administrador')) {
                $query = $query->with([
                    'maestrias',
                    'matriculas.asignatura',
                    'matriculas.docente',
                    'matriculas.cohorte.aula',
                    'matriculas.cohorte.maestria'
                ])
                ->withCount('matriculas')
                ->orderBy('matriculas_count')
                ->orderBy('created_at', 'desc');

                $maestriasPermitidas = Maestria::all(); // Admin puede ver todas
            } 
            elseif ($user->hasRole('Secretario')) {
                $secretario = Secretario::where('email', $user->email)->firstOrFail();
                $maestriasPermitidas = $secretario->seccion->maestrias;

                $query = $query->with([
                    'maestrias',
                    'matriculas.asignatura',
                    'matriculas.docente',
                    'matriculas.cohorte.aula',
                    'matriculas.cohorte.maestria'
                ])
                ->withCount('matriculas')
                ->whereHas('maestrias', function ($q) use ($maestriasPermitidas) {
                    $q->whereIn('maestrias.id', $maestriasPermitidas->pluck('id'));
                })
                ->orderBy('matriculas_count')
                ->orderBy('created_at', 'desc');
            } 
            elseif ($user->hasRole('Coordinador')) {
                $docente = Docente::where('email', $user->email)->firstOrFail();
                $maestria = $docente->maestria->first();
                if (!$maestria) {
                    abort(403, 'El coordinador no tiene ninguna maestría asignada.');
                }
                $maestriasPermitidas = collect([$maestria]);

                $query = $query->with([
                    'maestrias',
                    'matriculas.asignatura',
                    'matriculas.docente',
                    'matriculas.cohorte.aula',
                    'matriculas.cohorte.maestria'
                ])
                ->withCount('matriculas')
                ->whereHas('maestrias', function ($q) use ($maestria) {
                    $q->where('maestrias.id', $maestria->id);
                })
                ->orderBy('matriculas_count')
                ->orderBy('created_at', 'desc');
            } 
            else {
                abort(403, 'No autorizado');
            }

            return datatables()->eloquent($query)
                ->addColumn('maestria_nombre', function ($alumno) {
                    return $alumno->maestrias->pluck('nombre')->implode(', ') ?: 'Sin Maestría';
                })
                ->filterColumn('maestria_nombre', function ($query, $keyword) {
                    $query->whereHas('maestrias', function ($q) use ($keyword) {
                        $q->where('nombre', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('foto', function ($alumno) {
                    $image = $alumno->image ? asset('storage/' . $alumno->image) : asset('images/default.png');
                    return '<img src="' . $image . '" class="img-thumbnail rounded-circle" style="width:60px;height:60px;object-fit:cover;">';
                })
                ->addColumn('nombre_completo', function ($alumno) {
                    return trim("{$alumno->nombre1} {$alumno->nombre2} {$alumno->apellidop} {$alumno->apellidom}");
                })
                ->filterColumn('nombre_completo', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(nombre1,' ',nombre2,' ',apellidop,' ',apellidom) like ?", ["%{$keyword}%"]);
                })
                ->orderColumn('nombre_completo', function ($query, $order) {
                    $query->orderByRaw("CONCAT(nombre1,' ',nombre2,' ',apellidop,' ',apellidom) {$order}");
                })
                ->addColumn('acciones', function ($alumno) use ($user, $maestriasPermitidas) {
                    $acciones = '<div style="display:flex;gap:8px;align-items:center;">';

                    // Filtrar maestrías y matriculas del alumno según permisos
                    $maestriasAlumno = $alumno->maestrias->filter(fn($m) => $maestriasPermitidas->pluck('id')->contains($m->id));
                    $matriculasFiltradas = $alumno->matriculas->filter(fn($m) => $maestriasPermitidas->pluck('id')->contains($m->cohorte->maestria->id));

                    // === Ver Matrículas ===
                    if ($matriculasFiltradas->count() > 0) {
                        $acciones .= '<button type="button" class="btn btn-outline-info btn-sm view-matriculas" 
                            data-id="' . $alumno->dni . '" 
                            data-matriculas=\'' . json_encode(
                                $matriculasFiltradas->map(function($m){
                                    return [
                                        'asignatura' => $m->asignatura->nombre ?? 'No disponible',
                                        'docente' => $m->docente ? $m->docente->nombre1.' '.$m->docente->apellidop : 'No disponible',
                                        'cohorte' => $m->cohorte->nombre ?? 'No disponible',
                                        'maestria' => $m->cohorte->maestria->nombre ?? 'No disponible',
                                        'aula' => $m->cohorte->aula->nombre ?? 'No disponible',
                                        'paralelo' => $m->cohorte->aula->paralelo ?? 'No disponible',
                                    ];
                                })->values()->toArray(),
                                JSON_UNESCAPED_UNICODE
                            ) . '\' title="Ver Matrícula"><i class="fas fa-eye"></i></button>';
                    }

                    // === Matricular (solo si tiene maestrías pendientes) ===
                    if ($maestriasAlumno->count() > 0 && ($user->hasRole('Administrador') || $user->hasRole('Secretario'))) {
                        $maestriasConMatriculas = $matriculasFiltradas->map(fn($m) => $m->cohorte->maestria->id)->unique();
                        $faltanMatriculas = $maestriasAlumno->filter(fn($m) => !$maestriasConMatriculas->contains($m->id));

                        if ($faltanMatriculas->count() > 0) {
                            $acciones .= '<button type="button" class="btn btn-outline-success btn-sm open-matricula-modal" 
                                data-dni="' . $alumno->dni . '" 
                                data-maestrias=\'' . json_encode(
                                    $faltanMatriculas->map(fn($m) => ['id'=>$m->id,'nombre'=>$m->nombre])->values()->toArray(),
                                    JSON_UNESCAPED_UNICODE
                                ) . '\' title="Matricular"><i class="fas fa-plus-circle"></i></button>';
                        }
                    }

                    // === Reportes ===
                    if ($matriculasFiltradas->count() > 0) {
                        $acciones .= '<button type="button" class="btn btn-outline-warning btn-sm open-reportes" 
                            data-dni="' . $alumno->dni . '" 
                            data-nombre="' . $alumno->nombre1.' '.$alumno->apellidop . '" 
                            data-maestrias=\'' . json_encode(
                                $maestriasAlumno->map(fn($m) => ['id'=>$m->id,'nombre'=>$m->nombre])->values()->toArray(),
                                JSON_UNESCAPED_UNICODE
                            ) . '\' title="Ver Reportes"><i class="fas fa-file-alt"></i></button>';
                    }

                    // === Botones Admin ===
                    if ($user->can('dashboard_admin') && $alumno->matriculas->count() > 0) {
                        $acciones .= '<a href="' . url('/notas/create', $alumno->dni) . '" class="btn btn-outline-info btn-sm" title="Calificar"><i class="fas fa-pencil-alt"></i></a>';
                    }

                    if ($user->can('dashboard_admin')) {
                        $acciones .= '<a href="' . route('alumnos.edit', $alumno->dni) . '" class="btn btn-outline-primary btn-sm" title="Editar"><i class="fas fa-edit"></i></a>';
                    }

                    $acciones .= '</div>';
                    return $acciones;
                })
                ->rawColumns(['foto','acciones'])
                ->make(true);
        }

        return view('alumnos.index');
    }

    public function create()
    {
        $provincias = ['Azuay', 'Bolívar', 'Cañar', 'Carchi', 'Chimborazo', 'Cotopaxi', 'El Oro', 'Esmeraldas', 'Galápagos', 'Guayas', 'Imbabura', 'Loja', 'Los Ríos', 'Manabí', 'Morona Santiago', 'Napo', 'Orellana', 'Pastaza', 'Pichincha', 'Santa Elena', 'Santo Domingo de los Tsáchilas', 'Sucumbíos', 'Tungurahua', 'Zamora Chinchipe'];
        $user = auth()->user();

        if ($user->hasRole('Secretario')) {
            $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();
            $maestriasIds = $secretario->seccion->maestrias->pluck('id');
            $maestrias = Maestria::whereIn('id', $maestriasIds)->get();
        } else {
            $maestrias = Maestria::all();
        }

        return view('alumnos.create', compact('provincias', 'maestrias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'maestrias' => 'required|array',
            'maestrias.*' => 'exists:maestrias,id',
            'image' => 'nullable|image|max:2048',
            'dni' => 'required|string|max:20|unique:alumnos,dni|unique:postulantes,dni',
            'email_institucional' => 'required|email|unique:users,email',
        ]);

        $alumno = new Alumno();
        $alumno->fill($request->only([
            'nombre1', 'nombre2', 'apellidop', 'apellidom',
            'sexo', 'dni', 'email_institucional', 'email_personal',
            'estado_civil', 'fecha_nacimiento', 'provincia', 'canton',
            'barrio', 'direccion', 'nacionalidad', 'etnia',
            'carnet_discapacidad', 'tipo_discapacidad', 'porcentaje_discapacidad'
        ]));
        $alumno->contra = Hash::make($request->dni);

        if ($request->hasFile('image')) {
            $alumno->image = $request->file('image')->store('imagenes_usuarios', 'public');
        } else {
            $alumno->image = 'https://ui-avatars.com/api/?name=' . urlencode(substr($alumno->nombre1, 0, 1));
        }

        $alumno->save();

        // Asociar maestrías y sus montos
        foreach ($request->maestrias as $maestriaId) {
            $maestria = Maestria::find($maestriaId);

            $alumno->montos()->attach($maestriaId, [
                'monto_arancel' => $maestria->arancel ?? 0,
                'monto_matricula' => $maestria->matricula ?? 0,
                'monto_inscripcion' => $maestria->inscripcion ?? 0,
            ]);
        }

        // Crear usuario
        $usuario = User::create([
            'name' => $alumno->nombre1,
            'apellido' => $alumno->apellidop,
            'sexo' => $alumno->sexo,
            'email' => $alumno->email_institucional,
            'status' => 'ACTIVO',
            'image' => $alumno->image,
            'password' => Hash::make($request->dni),
        ]);
        $usuario->assignRole(Role::findById(4));

        return redirect()->route('alumnos.index')->with('success', 'Alumno creado exitosamente con montos.');
    }

    public function edit($dni)
    {
        $maestrias = Maestria::where('status', 'ACTIVO')->get();
        $alumno = Alumno::where('dni', $dni)->with('maestrias')->firstOrFail();
        $provincias = ['Azuay', 'Bolívar', 'Cañar', 'Carchi', 'Chimborazo', 'Cotopaxi', 'El Oro', 'Esmeraldas', 'Galápagos', 'Guayas', 'Imbabura', 'Loja', 'Los Ríos', 'Manabí', 'Morona Santiago', 'Napo', 'Orellana', 'Pastaza', 'Pichincha', 'Santa Elena', 'Santo Domingo de los Tsáchilas', 'Sucumbíos', 'Tungurahua', 'Zamora Chinchipe'];
        return view('alumnos.edit', compact('alumno', 'provincias', 'maestrias'));
    }

    public function update(Request $request, $dni)
    {
        $request->validate([
            'maestrias'   => 'required|array',
            'maestrias.*' => 'exists:maestrias,id',
            'image'       => 'nullable|image|max:2048',
        ]);

        $alumno = Alumno::where('dni', $dni)->firstOrFail();
        $alumno->fill($request->only([
            'nombre1', 'nombre2', 'apellidop', 'apellidom', 'dni',
            'estado_civil', 'fecha_nacimiento', 'provincia', 'canton',
            'barrio', 'direccion', 'nacionalidad', 'etnia',
            'email_personal', 'carnet_discapacidad', 'tipo_discapacidad',
            'porcentaje_discapacidad', 'sexo',
        ]));

        // === Manejo de imagen ===
        if ($request->hasFile('image')) {
            if ($alumno->image) {
                Storage::disk('public')->delete($alumno->image);
            }
            $path = $request->file('image')->store('imagenes_usuarios', 'public');
            $alumno->image = $path;

            // Actualizar también al usuario vinculado
            $usuario = User::where('email', $alumno->email_institucional)->first();
            if ($usuario) {
                $usuario->image = $path;
                $usuario->save();
            }
        }

        $alumno->save();

        // === Sincronización de maestrías ===
        $maestriasActuales = $alumno->maestrias->pluck('id')->toArray();

        // Aseguramos que el request traiga solo IDs
        $maestriasNuevas = collect($request->maestrias)->map(function ($m) {
            return is_array($m) ? $m['id'] : $m;
        })->toArray();

        // Maestrías eliminadas
        $maestriasEliminar = array_diff($maestriasActuales, $maestriasNuevas);
        if (!empty($maestriasEliminar)) {
            $alumno->montos()->whereIn('maestria_id', $maestriasEliminar)->delete();
        }

        // Sincronizar maestrías en la tabla pivote
        $alumno->maestrias()->sync($maestriasNuevas);

        // Crear montos para las nuevas maestrías
        $maestriasAgregar = array_diff($maestriasNuevas, $maestriasActuales);
        foreach ($maestriasAgregar as $maestriaId) {
            $maestria = Maestria::find($maestriaId);
            if ($maestria) {
                $alumno->montos()->create([
                    'maestria_id'      => $maestriaId,
                    'monto_arancel'    => $maestria->arancel ?? 0,
                    'monto_matricula'  => $maestria->matricula ?? 0,
                    'monto_inscripcion'=> $maestria->inscripcion ?? 0,
                ]);
            }
        }

        return redirect()->route('alumnos.index')
            ->with('success', 'Alumno actualizado correctamente con montos.');
    }

    public function retirarse(Request $request, $dni)
    {
        try {
            $request->validate([
                'retiro_documento' => 'required|file|mimes:pdf,doc,docx|max:2048',
                'alumno_dni' => 'required|exists:alumnos,dni',
            ]);

            $alumno = Alumno::where('dni', $dni)->firstOrFail();

            $path = $request->file('retiro_documento')->store('retiros', 'public');


            Retiro::create([
                'alumno_dni' => $request->input('alumno_dni'),
                'documento_path' => $path,
                'fecha_retiro' => now(),
            ]);

            $usuario = User::where('email', $alumno->email_institucional)->firstOrFail();
            $usuario->status = 'INACTIVO';
            $usuario->save();

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
                $tasaTitulacion->retirados += 1;
                $tasaTitulacion->save();
            } else {
                // Si no existe, lo creamos con valores iniciales
                TasaTitulacion::create([
                    'cohorte_id' => $cohorteId,
                    'maestria_id' => $maestriaId,
                    'retirados' => 1,  // Iniciamos con 1 graduado
                ]);
            }

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->with('success', 'Solicitud de retiro enviada con éxito.');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
